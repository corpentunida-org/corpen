#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Cambia el entorno local entre DESARROLLO y PRODUCCIÓN.
#
# Uso (desde cualquier carpeta):
#   ./scripts/cambiar-entorno.sh              # MENÚ: 1) actual 2) desarrollo 3) producción 4) backup 5) ver estado
#   ./scripts/cambiar-entorno.sh actual       # deja el .env como está y solo recarga los contenedores
#   ./scripts/cambiar-entorno.sh desarrollo   # usa .env.desarrollo (pruebas: copia de producción)
#   ./scripts/cambiar-entorno.sh produccion   # usa .env.produccion (pide confirmación)
#   ./scripts/cambiar-entorno.sh backup       # usa .env.backup     (pide confirmación)
#   ./scripts/cambiar-entorno.sh estado       # muestra a qué base está conectada la app
#
# Qué hace al cambiar:
#   1. NO crea respaldos: si el .env tenía cambios propios, solo avisa qué variables se descartan.
#   2. Copia .env.<entorno> a .env.
#   3. Recrea los contenedores (app y, si están corriendo, worker/scheduler). Docker
#      carga el .env al CREAR el contenedor; sin recrearlo la app sigue con el entorno anterior.
#   4. Limpia la caché de configuración y verifica la conexión real a la base de datos.
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail

PROYECTO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROYECTO"

# Entornos disponibles: cada uno es un archivo .env.<nombre> en la raíz del proyecto.
#   desarrollo → base que recibe copias de producción; aquí se hacen TODAS las pruebas.
#   produccion → base real; solo para código ya probado en desarrollo.
#   backup     → base de copias de seguridad / control de cambios para restaurar. No es para trabajar.
ENTORNOS=(desarrollo produccion backup)
# Entornos que exigen escribir su nombre para confirmar.
PROTEGIDOS=(produccion backup)

VERDE=$'\e[1;32m'; ROJO=$'\e[1;41;97m'; AMARILLO=$'\e[1;33m'; AZUL=$'\e[1;36m'; NORMAL=$'\e[0m'

error() { echo "${AMARILLO}✖ $*${NORMAL}" >&2; exit 1; }
aviso() { echo "${AMARILLO}! $*${NORMAL}"; }

# Ejecuta un comando dentro del servicio "app" sin depender del nombre de la carpeta.
en_app() { docker compose exec -T app "$@"; }

entorno_actual() {
    local e
    for e in "${ENTORNOS[@]}"; do
        if [[ -f ".env.$e" ]] && cmp -s .env ".env.$e"; then echo "$e"; return; fi
    done
    echo "desconocido"
}

# Nombre de la base de datos de un archivo .env (solo el nombre, nunca credenciales).
base_de() {
    [[ -f "$1" ]] || { echo "falta $1"; return; }
    grep -E '^DB_DATABASE=' "$1" | head -1 | cut -d= -f2- | tr -d '"\r' || true
}

verificar_conexion() {
    echo "${AZUL}→ Verificando conexión real de la app...${NORMAL}"
    local intento salida
    for intento in 1 2 3 4 5 6; do
        if salida=$(en_app php artisan tinker --execute='
            $r = DB::selectOne("select @@hostname h, @@port p, database() d");
            echo "BD:     {$r->h}:{$r->p} / {$r->d}\n";
            $mh = config("mail.mailers.smtp.host"); $mp = config("mail.mailers.smtp.port");
            $ok = @fsockopen($mh, (int) $mp, $e, $m, 3) ? "" : "  (NO responde)";
            echo "Correo: {$mh}:{$mp}{$ok}\n";
            echo "S3:     ".(config("filesystems.disks.s3.bucket") ?: "(desactivado)")."\n";' 2>/dev/null); then
            echo "$salida"
            if grep -q "NO responde" <<<"$salida"; then
                aviso "El servidor de correo no responde. En desarrollo hace falta Mailpit:"
                aviso "  docker run -d --name mailpit --restart unless-stopped -p 8025:8025 -p 1025:1025 axllent/mailpit"
            fi
            return 0
        fi
        sleep 3
    done
    aviso "El .env YA quedó en este entorno, pero la app no logra conectarse a su base de datos."
    aviso "Revisa: 1) que tu equipo llegue al servidor de BD (red de la oficina / VPN),"
    aviso "        2) usuario y contraseña del .env.<entorno>,  3) docker compose ps (app corriendo)."
    aviso "Detalle del error:  docker compose exec app php artisan db:show"
    aviso "Para volver a desarrollo:  bash scripts/cambiar-entorno.sh desarrollo"
    exit 1
}

banner() {
    case "$1" in
        desarrollo) echo; echo "${VERDE}  ✔ ENTORNO: DESARROLLO (pruebas sobre copia de producción)  ${NORMAL}"; echo ;;
        produccion) echo; echo "${ROJO}  ⚠ ENTORNO: PRODUCCIÓN — los cambios afectan datos, correos y archivos REALES  ${NORMAL}"; echo ;;
        backup)     echo; echo "${ROJO}  ⚠ ENTORNO: BACKUP — base de copias de seguridad; no la modifiques salvo para restaurar  ${NORMAL}"; echo ;;
        *)          echo; echo "${AMARILLO}  ? ENTORNO: el .env no coincide con ningún .env.<entorno>${NORMAL}"; echo ;;
    esac
}

# ── Argumentos ─────────────────────────────────────────────────────────────────
command -v docker >/dev/null || error "Docker no está disponible en esta terminal."

# Sin argumentos: menú interactivo para elegir entorno.
menu() {
    local actual op
    actual="$(entorno_actual)"
    echo
    echo "${AZUL}  ¿Con cuál .env quieres trabajar?${NORMAL}   (actual: ${actual})"
    echo
    echo "    1) Actual       → .env             (BD: $(base_de .env))  recargar sin cambiar"
    echo "    2) Desarrollo   → .env.desarrollo  (BD: $(base_de .env.desarrollo))  pruebas"
    echo "    3) Producción   → .env.produccion  (BD: $(base_de .env.produccion))  ⚠ datos REALES"
    echo "    4) Backup       → .env.backup      (BD: $(base_de .env.backup))  ⚠ copias de seguridad"
    echo "    5) Mantener el .env actual (solo ver estado)"
    echo
    read -r -p "  Elige 1, 2, 3, 4 o 5: " op
    op="$(tr -d '\r[:space:]' <<<"$op")"   # desde PowerShell la respuesta llega con \r
    case "$op" in
        1) DESTINO="actual" ;;
        2) DESTINO="desarrollo" ;;
        3) DESTINO="produccion" ;;
        4) DESTINO="backup" ;;
        5) DESTINO="estado" ;;
        *) error "Opción no válida. No se cambió nada." ;;
    esac
}

DESTINO="${1:-}"
[[ -z "$DESTINO" ]] && menu
case "$DESTINO" in
    actual|recargar|a) DESTINO="actual" ;;
    desarrollo|dev|d)  DESTINO="desarrollo" ;;
    produccion|prod|p) DESTINO="produccion" ;;
    backup|b)          DESTINO="backup" ;;
    estado|status|s)   banner "$(entorno_actual)"; verificar_conexion; exit 0 ;;
    *) echo "Uso: $0 [actual|desarrollo|produccion|backup|estado]   (sin argumentos muestra el menú)"; exit 1 ;;
esac

# Opción "actual": no copia nada; solo recarga los contenedores con el .env que ya está.
if [[ "$DESTINO" == "actual" ]]; then
    [[ -f .env ]] || error "No existe .env. Elige un entorno (desarrollo, produccion o backup)."
    DESTINO="$(entorno_actual)"
    echo "${AZUL}→ Se mantiene el .env actual (${DESTINO}); solo se recargan los contenedores.${NORMAL}"
    RECARGAR_SOLO=1
fi

if [[ -z "${RECARGAR_SOLO:-}" ]]; then
ORIGEN=".env.$DESTINO"
[[ -f "$ORIGEN" ]] || error "No existe $ORIGEN. Créalo (pídelo a tu líder técnico)."
grep -qE '^DB_(USERNAME|PASSWORD)=CAMBIAR_AQUI' "$ORIGEN" && error "$ORIGEN aún tiene DB_USERNAME/DB_PASSWORD=CAMBIAR_AQUI."

ACTUAL="$(entorno_actual)"
[[ "$ACTUAL" == "$DESTINO" ]] && echo "Ya estás en $DESTINO. Se recrearán los contenedores igualmente."

if [[ " ${PROTEGIDOS[*]} " == *" $DESTINO "* ]]; then
    echo "${ROJO} Vas a conectar tu entorno local a ${DESTINO^^}. ${NORMAL}"
    echo "No es una contraseña: para confirmar escribe la PALABRA  $DESTINO"
    read -r -p "Confirmación: " confirmacion
    # Acepta mayúsculas/minúsculas y tildes (Producción, PRODUCCION, produccion...).
    confirmacion="$(tr -d '\r[:space:]' <<<"$confirmacion" | tr '[:upper:]' '[:lower:]' \
                    | sed 's/ó/o/g; s/Ó/o/g; s/á/a/g; s/é/e/g; s/í/i/g; s/ú/u/g')"
    [[ "$confirmacion" == "$DESTINO" ]] || error "Cancelado: escribiste '$confirmacion' y se esperaba '$DESTINO'. No se cambió nada."
fi

# ── 1. Avisar (SIN respaldar) si .env tenía cambios propios ─────────────────────
# Por decisión del equipo NO se crean archivos .env.respaldo-*: los cambios permanentes se hacen
# en .env.desarrollo / .env.produccion, nunca directamente en .env. Solo se listan los NOMBRES de
# las variables que se pierden (nunca sus valores).
if [[ -f .env && "$ACTUAL" == "desconocido" ]]; then
    PERDIDAS="$(diff <(grep -E '^[A-Z_]+=' .env | sort) <(grep -E '^[A-Z_]+=' "$ORIGEN" | sort) \
                | grep '^<' | sed -E 's/^< ([A-Z_]+)=.*/\1/' | tr '\n' ' ' || true)"
    aviso "El .env actual tenía cambios propios que se descartan (no se crea respaldo)."
    [[ -n "$PERDIDAS" ]] && aviso "Variables distintas en el .env descartado: $PERDIDAS"
    aviso "Si los necesitas, ponlos en $ORIGEN y vuelve a ejecutar: $0 $DESTINO"
fi

# ── 2. Copiar ──────────────────────────────────────────────────────────────────
cp "$ORIGEN" .env
echo "${AZUL}→ .env ← $ORIGEN${NORMAL}"
fi

# ── 3. Recrear contenedores ────────────────────────────────────────────────────
SERVICIOS=(app)
CORRIENDO="$(docker compose ps --services --status running 2>/dev/null || true)"
for s in worker scheduler; do
    if grep -qx "$s" <<<"$CORRIENDO"; then SERVICIOS+=("$s"); fi
done
echo "${AZUL}→ Recreando: ${SERVICIOS[*]}${NORMAL}"
docker compose up -d --force-recreate "${SERVICIOS[@]}" 2>&1 | grep -v "orphan containers" || true

# ── 4. Limpiar caché y verificar ───────────────────────────────────────────────
en_app php artisan config:clear >/dev/null
banner "$DESTINO"
verificar_conexion
