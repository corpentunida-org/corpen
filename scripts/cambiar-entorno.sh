#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Cambia el entorno local entre DESARROLLO y PRODUCCIÓN.
#
# Uso (desde cualquier carpeta):
#   ./scripts/cambiar-entorno.sh              # MENÚ: pregunta 1) desarrollo 2) producción 3) mantener actual
#   ./scripts/cambiar-entorno.sh desarrollo   # usa .env.desarrollo
#   ./scripts/cambiar-entorno.sh produccion   # usa .env.produccion (pide confirmación)
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

VERDE=$'\e[1;32m'; ROJO=$'\e[1;41;97m'; AMARILLO=$'\e[1;33m'; AZUL=$'\e[1;36m'; NORMAL=$'\e[0m'

error() { echo "${AMARILLO}✖ $*${NORMAL}" >&2; exit 1; }
aviso() { echo "${AMARILLO}! $*${NORMAL}"; }

# Ejecuta un comando dentro del servicio "app" sin depender del nombre de la carpeta.
en_app() { docker compose exec -T app "$@"; }

entorno_actual() {
    if   [[ -f .env.desarrollo ]] && cmp -s .env .env.desarrollo; then echo "desarrollo"
    elif [[ -f .env.produccion ]] && cmp -s .env .env.produccion; then echo "produccion"
    else echo "desconocido"; fi
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
    error "No se pudo verificar la conexión (¿está corriendo el servicio app? ¿la BD responde? ¿usuario/contraseña correctos?)."
}

banner() {
    case "$1" in
        desarrollo) echo; echo "${VERDE}  ✔ ENTORNO: DESARROLLO (base de pruebas, correo a Mailpit)  ${NORMAL}"; echo ;;
        produccion) echo; echo "${ROJO}  ⚠ ENTORNO: PRODUCCIÓN — los cambios afectan datos, correos y archivos REALES  ${NORMAL}"; echo ;;
        *)          echo; echo "${AMARILLO}  ? ENTORNO: no coincide con .env.desarrollo ni .env.produccion  ${NORMAL}"; echo ;;
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
    echo "    1) Desarrollo   → .env.desarrollo (base de pruebas)"
    echo "    2) Producción   → .env.produccion (datos REALES)"
    echo "    3) Mantener el .env actual (solo ver estado)"
    echo
    read -r -p "  Elige 1, 2 o 3: " op
    op="$(tr -d '\r[:space:]' <<<"$op")"   # desde PowerShell la respuesta llega con \r
    case "$op" in
        1) DESTINO="desarrollo" ;;
        2) DESTINO="produccion" ;;
        3) DESTINO="estado" ;;
        *) error "Opción no válida. No se cambió nada." ;;
    esac
}

DESTINO="${1:-}"
[[ -z "$DESTINO" ]] && menu
case "$DESTINO" in
    desarrollo|dev|d)  DESTINO="desarrollo" ;;
    produccion|prod|p) DESTINO="produccion" ;;
    estado|status|s)   banner "$(entorno_actual)"; verificar_conexion; exit 0 ;;
    *) echo "Uso: $0 [desarrollo|produccion|estado]   (sin argumentos muestra el menú)"; exit 1 ;;
esac

ORIGEN=".env.$DESTINO"
[[ -f "$ORIGEN" ]] || error "No existe $ORIGEN. Créalo (pídelo a tu líder técnico)."
grep -qE '^DB_(USERNAME|PASSWORD)=CAMBIAR_AQUI' "$ORIGEN" && error "$ORIGEN aún tiene DB_USERNAME/DB_PASSWORD=CAMBIAR_AQUI."

ACTUAL="$(entorno_actual)"
[[ "$ACTUAL" == "$DESTINO" ]] && echo "Ya estás en $DESTINO. Se recrearán los contenedores igualmente."

if [[ "$DESTINO" == "produccion" ]]; then
    echo "${ROJO} Vas a conectar tu entorno local a PRODUCCIÓN. ${NORMAL}"
    echo "No es una contraseña: para confirmar escribe la PALABRA  produccion  (minúsculas, sin tilde)."
    read -r -p "Confirmación: " confirmacion
    confirmacion="$(tr -d '\r[:space:]' <<<"$confirmacion")"
    [[ "$confirmacion" == "produccion" ]] || error "Cancelado. No se cambió nada."
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
