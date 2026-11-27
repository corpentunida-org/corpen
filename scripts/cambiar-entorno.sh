#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Cambia el entorno local (qué .env usa la app) entre DESARROLLO, PRODUCCIÓN y BACKUP.
# Funciona en cualquier equipo del equipo de desarrollo: Ubuntu/WSL, Linux, macOS o Git Bash.
#
# Uso (desde cualquier carpeta):
#   bash scripts/cambiar-entorno.sh              # MENÚ: 1) actual 2) desarrollo 3) producción 4) backup 5) estado
#   bash scripts/cambiar-entorno.sh actual       # deja el .env como está y solo recarga los contenedores
#   bash scripts/cambiar-entorno.sh desarrollo   # usa .env.desarrollo (pruebas: copia de producción)
#   bash scripts/cambiar-entorno.sh produccion   # usa .env.produccion (pide confirmación)
#   bash scripts/cambiar-entorno.sh backup       # usa .env.backup     (pide confirmación)
#   bash scripts/cambiar-entorno.sh estado       # muestra a qué base está conectada la app
#   bash scripts/cambiar-entorno.sh ayuda
#
# Qué hace al cambiar:
#   0. Revisa requisitos del equipo (Docker encendido, docker compose, archivos .env) y, la
#      primera vez, ofrece instalar dependencias (vendor/ y public/build) dentro del contenedor.
#   1. NO crea respaldos: si el .env tenía cambios propios, solo avisa qué variables se descartan.
#   2. Copia .env.<entorno> a .env (corrigiendo saltos de línea de Windows si los hay).
#   3. Recrea los contenedores (app y, si están corriendo, worker/scheduler). Docker
#      carga el .env al CREAR el contenedor; sin recrearlo la app sigue con el entorno anterior.
#   4. Limpia la caché de configuración y verifica la conexión real a la base de datos.
#
# Los archivos .env.* NO están en git (tienen contraseñas): cada desarrollador los recibe aparte.
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

if [[ -t 1 ]]; then
    VERDE=$'\e[1;32m'; ROJO=$'\e[1;41;97m'; AMARILLO=$'\e[1;33m'; AZUL=$'\e[1;36m'; NORMAL=$'\e[0m'
else
    VERDE=""; ROJO=""; AMARILLO=""; AZUL=""; NORMAL=""
fi

error() { echo "${AMARILLO}✖ $*${NORMAL}" >&2; exit 1; }
aviso() { echo "${AMARILLO}! $*${NORMAL}"; }
paso()  { echo "${AZUL}→ $*${NORMAL}"; }
mayus() { tr '[:lower:]' '[:upper:]' <<<"$1"; }   # (macOS trae bash 3.2: sin ${var^^})

preguntar_si() {   # preguntar_si "¿Texto?"  → 0 si responde s/si/y
    local r
    read -r -p "$1 [s/N]: " r || true
    r="$(tr -d '\r[:space:]' <<<"$r" | tr '[:upper:]' '[:lower:]')"
    [[ "$r" == "s" || "$r" == "si" || "$r" == "sí" || "$r" == "y" ]]
}

# ── 0. Requisitos del equipo ───────────────────────────────────────────────────
DC=()   # comando de docker compose (v2 "docker compose" o v1 "docker-compose")

revisar_requisitos() {
    [[ -f docker-compose.yml ]] || error "No encuentro docker-compose.yml en $PROYECTO. ¿El script está dentro de la carpeta scripts/ del proyecto?"

    command -v docker >/dev/null 2>&1 || error "Docker no está instalado o no está disponible en esta terminal.
   · Windows: instala Docker Desktop y activa Settings → Resources → WSL integration → Ubuntu.
   · Luego abre una terminal NUEVA de Ubuntu y vuelve a ejecutar el script."

    docker info >/dev/null 2>&1 || error "Docker está instalado pero NO está encendido (o tu usuario no tiene permiso).
   · Abre Docker Desktop y espera a que diga 'Engine running'.
   · En Linux: sudo systemctl start docker   (y: sudo usermod -aG docker \$USER, luego cierra sesión)."

    if docker compose version >/dev/null 2>&1; then DC=(docker compose)
    elif command -v docker-compose >/dev/null 2>&1; then DC=(docker-compose)
    else error "Falta 'docker compose'. Actualiza Docker Desktop (incluye Compose v2)."; fi

    local hay=0 e
    for e in "${ENTORNOS[@]}"; do [[ -f ".env.$e" ]] && hay=1; done
    [[ $hay -eq 1 ]] || error "No hay ningún archivo .env.<entorno> en $PROYECTO.
   Pide a tu líder técnico (por un medio seguro, NO por git/chat) al menos .env.desarrollo
   y guárdalo en la raíz del proyecto. Luego vuelve a ejecutar el script."
}

# Corrige un .env editado en Windows: quita BOM y saltos CRLF (rompen contraseñas y hosts).
normalizar_env() {
    local f="$1" tmp
    if head -c3 "$f" | od -An -tx1 | grep -qi 'ef bb bf' || grep -q $'\r' "$f"; then
        tmp="$(mktemp)"
        sed $'1s/^\xEF\xBB\xBF//' "$f" | tr -d '\r' > "$tmp"
        cat "$tmp" > "$f"; rm -f "$tmp"
        aviso "$f tenía formato de Windows (CRLF/BOM); se corrigió automáticamente."
    fi
}

# Primera vez en un equipo: vendor/ y public/build no vienen en git y el contenedor los necesita.
# Se PREGUNTA antes de tocar el .env; la instalación se hace después (necesita el .env copiado).
INSTALAR_DEPENDENCIAS=""
revisar_dependencias() {
    local falta=()
    [[ -f vendor/autoload.php ]]        || falta+=("vendor/ (composer install)")
    [[ -f public/build/manifest.json ]] || falta+=("public/build (npm run build)")
    [[ ${#falta[@]} -eq 0 ]] && return 0

    aviso "Este equipo aún no tiene: ${falta[*]}"
    aviso "Sin eso la app no arranca. Se puede instalar dentro del contenedor (tarda varios minutos la primera vez)."
    preguntar_si "¿Instalar ahora?" || error "Cancelado. No se cambió nada."
    INSTALAR_DEPENDENCIAS=1
}

preparar_dependencias() {
    [[ -n "$INSTALAR_DEPENDENCIAS" ]] || return 0

    if [[ ! -f vendor/autoload.php ]]; then
        paso "composer install (dentro del contenedor)..."
        "${DC[@]}" run --rm --no-deps -T app composer install --no-interaction --no-scripts \
            || error "Falló composer install. Revisa tu conexión a internet y vuelve a intentar."
    fi
    if [[ ! -f public/build/manifest.json ]]; then
        paso "npm install && npm run build (dentro del contenedor)..."
        "${DC[@]}" run --rm --no-deps -T app sh -c '. ~/.nvm/nvm.sh && npm install && npm run build' \
            || error "Falló npm run build. Revisa el error anterior."
    fi
}

# Ejecuta un comando dentro del servicio "app" sin depender del nombre de la carpeta.
en_app() { "${DC[@]}" exec -T app "$@"; }

app_corriendo() { "${DC[@]}" ps --services --status running 2>/dev/null | grep -qx app; }

entorno_actual() {
    local e
    [[ -f .env ]] || { echo "ninguno"; return; }
    for e in "${ENTORNOS[@]}"; do
        if [[ -f ".env.$e" ]] && cmp -s .env ".env.$e"; then echo "$e"; return; fi
    done
    echo "desconocido"
}

# Nombre de la base de datos de un archivo .env (solo el nombre, nunca credenciales).
base_de() {
    [[ -f "$1" ]] || { echo "no disponible: falta $1"; return; }
    grep -E '^DB_DATABASE=' "$1" | head -1 | cut -d= -f2- | tr -d '"\r' || true
}

verificar_conexion() {
    paso "Verificando conexión real de la app..."
    app_corriendo || error "El contenedor 'app' no está corriendo. Revisa:  ${DC[*]} logs --tail=50 app"
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
            echo
            echo "  App:    http://localhost:83"
            return 0
        fi
        sleep 3
    done
    aviso "El .env YA quedó en este entorno, pero la app no logra conectarse a su base de datos."
    aviso "Revisa: 1) que tu equipo llegue al servidor de BD (red de la oficina / VPN),"
    aviso "        2) usuario y contraseña del .env.<entorno>,  3) ${DC[*]} ps (app corriendo)."
    aviso "Detalle del error:  ${DC[*]} exec app php artisan db:show"
    aviso "Para volver a desarrollo:  bash scripts/cambiar-entorno.sh desarrollo"
    exit 1
}

banner() {
    case "$1" in
        desarrollo) echo; echo "${VERDE}  ✔ ENTORNO: DESARROLLO (pruebas sobre copia de producción)  ${NORMAL}"; echo ;;
        produccion) echo; echo "${ROJO}  ⚠ ENTORNO: PRODUCCIÓN — los cambios afectan datos, correos y archivos REALES  ${NORMAL}"; echo ;;
        backup)     echo; echo "${ROJO}  ⚠ ENTORNO: BACKUP — base de copias de seguridad; no la modifiques salvo para restaurar  ${NORMAL}"; echo ;;
        ninguno)    echo; echo "${AMARILLO}  ? ENTORNO: no hay .env todavía (elige un entorno)  ${NORMAL}"; echo ;;
        *)          echo; echo "${AMARILLO}  ? ENTORNO: el .env no coincide con ningún .env.<entorno>  ${NORMAL}"; echo ;;
    esac
}

ayuda() {
    sed -n '3,25p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'
    echo "Manual completo: scripts/MANUAL-cambiar-entorno.md"
}

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
    read -r -p "  Elige 1, 2, 3, 4 o 5: " op || true
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

# ── Argumentos ─────────────────────────────────────────────────────────────────
DESTINO="${1:-}"
case "$DESTINO" in ayuda|help|-h|--help) ayuda; exit 0 ;; esac

revisar_requisitos

[[ -z "$DESTINO" ]] && menu
case "$DESTINO" in
    actual|recargar|a) DESTINO="actual" ;;
    desarrollo|dev|d)  DESTINO="desarrollo" ;;
    produccion|prod|p) DESTINO="produccion" ;;
    backup|b)          DESTINO="backup" ;;
    estado|status|s)   banner "$(entorno_actual)"; verificar_conexion; exit 0 ;;
    *) echo "Uso: bash scripts/cambiar-entorno.sh [actual|desarrollo|produccion|backup|estado|ayuda]   (sin argumentos muestra el menú)"; exit 1 ;;
esac

# Opción "actual": no copia nada; solo recarga los contenedores con el .env que ya está.
if [[ "$DESTINO" == "actual" ]]; then
    [[ -f .env ]] || error "No existe .env todavía. Elige un entorno (2 desarrollo, 3 producción o 4 backup)."
    normalizar_env .env
    DESTINO="$(entorno_actual)"
    paso "Se mantiene el .env actual (${DESTINO}); solo se recargan los contenedores."
    RECARGAR_SOLO=1
    revisar_dependencias
fi

if [[ -z "${RECARGAR_SOLO:-}" ]]; then
    ORIGEN=".env.$DESTINO"
    [[ -f "$ORIGEN" ]] || error "No existe $ORIGEN en este equipo. Pídelo a tu líder técnico (por un medio seguro) y guárdalo en la raíz del proyecto."
    if grep -qE '^DB_(USERNAME|PASSWORD)=(CAMBIAR_AQUI)?$' "$ORIGEN"; then
        error "$ORIGEN tiene DB_USERNAME o DB_PASSWORD vacío (o CAMBIAR_AQUI). Complétalo antes de usarlo."
    fi
    normalizar_env "$ORIGEN"

    ACTUAL="$(entorno_actual)"
    [[ "$ACTUAL" == "$DESTINO" ]] && echo "Ya estás en $DESTINO. Se recrearán los contenedores igualmente."

    if [[ " ${PROTEGIDOS[*]} " == *" $DESTINO "* ]]; then
        echo "${ROJO} Vas a conectar tu entorno local a $(mayus "$DESTINO"). ${NORMAL}"
        echo "No es una contraseña: para confirmar escribe la PALABRA  $DESTINO"
        read -r -p "Confirmación: " confirmacion || true
        # Acepta mayúsculas/minúsculas y tildes (Producción, PRODUCCION, produccion...).
        confirmacion="$(tr -d '\r[:space:]' <<<"$confirmacion" | tr '[:upper:]' '[:lower:]' \
                        | sed 's/ó/o/g; s/Ó/o/g; s/á/a/g; s/é/e/g; s/í/i/g; s/ú/u/g')"
        [[ "$confirmacion" == "$DESTINO" ]] || error "Cancelado: escribiste '$confirmacion' y se esperaba '$DESTINO'. No se cambió nada."
    fi

    revisar_dependencias

    # ── 1. Avisar (SIN respaldar) si .env tenía cambios propios ─────────────────
    # Por decisión del equipo NO se crean archivos .env.respaldo-*: los cambios permanentes se
    # hacen en .env.<entorno>, nunca directamente en .env. Solo se listan los NOMBRES de las
    # variables que se pierden (nunca sus valores).
    if [[ "$ACTUAL" == "desconocido" ]]; then
        PERDIDAS="$(diff <(grep -E '^[A-Z_]+=' .env | sort) <(grep -E '^[A-Z_]+=' "$ORIGEN" | sort) \
                    | grep '^<' | sed -E 's/^< ([A-Z_]+)=.*/\1/' | tr '\n' ' ' || true)"
        aviso "El .env actual tenía cambios propios que se descartan (no se crea respaldo)."
        [[ -n "$PERDIDAS" ]] && aviso "Variables distintas en el .env descartado: $PERDIDAS"
        aviso "Si los necesitas, ponlos en $ORIGEN y vuelve a ejecutar: bash scripts/cambiar-entorno.sh $DESTINO"
    fi

    # ── 2. Copiar ──────────────────────────────────────────────────────────────
    cp "$ORIGEN" .env
    paso ".env ← $ORIGEN"
fi

# ── 3. Dependencias (solo la primera vez en un equipo) y contenedores ──────────
preparar_dependencias

SERVICIOS=(app)
CORRIENDO="$("${DC[@]}" ps --services --status running 2>/dev/null || true)"
for s in worker scheduler; do
    if grep -qx "$s" <<<"$CORRIENDO"; then SERVICIOS+=("$s"); fi
done
paso "Recreando: ${SERVICIOS[*]}  (la primera vez construye la imagen y puede tardar varios minutos)"
if ! SALIDA="$("${DC[@]}" up -d --force-recreate "${SERVICIOS[@]}" 2>&1)"; then
    echo "$SALIDA" | tail -20
    error "No se pudieron levantar los contenedores (ver error arriba). ¿El puerto 83 está ocupado por otra app?"
fi
echo "$SALIDA" | grep -v "orphan containers" || true

# Esperar a que el contenedor app quede corriendo (en el arranque hace config:cache, etc.).
for _ in $(seq 1 20); do app_corriendo && break; sleep 2; done
app_corriendo || { "${DC[@]}" logs --tail=30 app || true; error "El contenedor 'app' se detuvo al arrancar (ver log arriba)."; }

# ── 4. Limpiar caché y verificar ───────────────────────────────────────────────
en_app php artisan config:clear >/dev/null || aviso "No se pudo limpiar la caché de configuración."
banner "$DESTINO"
verificar_conexion
