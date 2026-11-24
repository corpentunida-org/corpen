#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Cambia el entorno local entre DESARROLLO y PRODUCCIÓN.
#
# Uso (desde cualquier carpeta):
#   ./scripts/cambiar-entorno.sh preparar     # 1.ª vez: crea .env.produccion y .env.desarrollo
#   ./scripts/cambiar-entorno.sh desarrollo   # usa .env.desarrollo
#   ./scripts/cambiar-entorno.sh produccion   # usa .env.produccion (pide confirmación)
#   ./scripts/cambiar-entorno.sh estado       # muestra a qué base está conectada la app
#
# Qué hace al cambiar:
#   1. Respalda el .env actual si tiene cambios que no están en ninguno de los dos archivos.
#   2. Copia .env.<entorno> a .env.
#   3. Recrea los contenedores (app y, si están corriendo, worker/scheduler). Docker
#      carga el .env al CREAR el contenedor; sin recrearlo la app sigue con el entorno anterior.
#   4. Limpia la caché de configuración y verifica la conexión real a la base de datos.
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail

PROYECTO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROYECTO"

PLANTILLA=".env.desarrollo.example"
HOST_DESARROLLO="192.168.10.13"
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

# Escribe CLAVE=VALOR en un archivo .env (reemplaza la línea o la agrega al final).
fijar_clave() {
    local archivo="$1" clave="$2" valor="$3" tmp
    tmp="$(mktemp)"
    awk -v k="$clave" -v v="$valor" '
        BEGIN { hecho = 0 }
        $0 ~ "^" k "=" { print k "=" v; hecho = 1; next }
        { print }
        END { if (!hecho) print k "=" v }
    ' "$archivo" > "$tmp"
    cat "$tmp" > "$archivo"
    rm -f "$tmp"
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

preparar() {
    [[ -f "$PLANTILLA" ]] || error "No existe $PLANTILLA (haz git pull)."

    if [[ -f .env.produccion ]]; then
        echo "✔ .env.produccion ya existe (no se toca)."
    else
        [[ -f .env ]] || error "No hay .env. Pide a tu líder técnico el .env de producción y guárdalo como .env.produccion."
        grep -q "^DB_HOST=\"\?$HOST_DESARROLLO" .env && \
            error "Tu .env ya apunta a desarrollo; no puedo usarlo como .env.produccion. Crea .env.produccion a mano."
        cp -p .env .env.produccion
        echo "✔ .env.produccion creado a partir de tu .env actual."
    fi

    if [[ -f .env.desarrollo && "${1:-}" != "--forzar" ]]; then
        echo "✔ .env.desarrollo ya existe (no se toca; usa 'preparar --forzar' para regenerarlo)."
    else
        cp .env.produccion .env.desarrollo
        sed -i '1i # ==== ENTORNO DE DESARROLLO / PRUEBAS - generado desde '"$PLANTILLA"' ====' .env.desarrollo
        local clave valor
        while IFS='=' read -r clave valor; do
            [[ -z "$clave" || "$clave" == \#* ]] && continue
            fijar_clave .env.desarrollo "$clave" "$valor"
        done < <(grep -E '^[A-Z_]+=' "$PLANTILLA")
        echo "✔ .env.desarrollo generado desde .env.produccion + $PLANTILLA."
    fi

    if grep -qE '^DB_(USERNAME|PASSWORD)=CAMBIAR_AQUI' .env.desarrollo; then
        aviso "Falta poner TU usuario y contraseña de la BD de desarrollo en .env.desarrollo"
        aviso "(líneas DB_USERNAME y DB_PASSWORD). Luego ejecuta: $0 desarrollo"
    fi
}

# ── Argumentos ─────────────────────────────────────────────────────────────────
command -v docker >/dev/null || error "Docker no está disponible en esta terminal."

DESTINO="${1:-}"
case "$DESTINO" in
    preparar|init)     preparar "${2:-}"; exit 0 ;;
    desarrollo|dev|d)  DESTINO="desarrollo" ;;
    produccion|prod|p) DESTINO="produccion" ;;
    estado|status|s)   banner "$(entorno_actual)"; verificar_conexion; exit 0 ;;
    *) echo "Uso: $0 {preparar|desarrollo|produccion|estado}"; exit 1 ;;
esac

ORIGEN=".env.$DESTINO"
[[ -f "$ORIGEN" ]] || error "No existe $ORIGEN. Ejecuta primero: $0 preparar"
grep -qE '^DB_(USERNAME|PASSWORD)=CAMBIAR_AQUI' "$ORIGEN" && error "$ORIGEN aún tiene DB_USERNAME/DB_PASSWORD=CAMBIAR_AQUI."

ACTUAL="$(entorno_actual)"
[[ "$ACTUAL" == "$DESTINO" ]] && echo "Ya estás en $DESTINO. Se recrearán los contenedores igualmente."

if [[ "$DESTINO" == "produccion" ]]; then
    echo "${ROJO} Vas a conectar tu entorno local a PRODUCCIÓN. ${NORMAL}"
    read -r -p "Escribe 'produccion' para confirmar: " confirmacion
    [[ "$confirmacion" == "produccion" ]] || error "Cancelado. No se cambió nada."
fi

# ── 1. Respaldar .env si tiene cambios propios ──────────────────────────────────
if [[ -f .env && "$ACTUAL" == "desconocido" ]]; then
    RESPALDO=".env.respaldo-$(date +%Y%m%d-%H%M%S)"
    cp -p .env "$RESPALDO"
    aviso "El .env actual no coincidía con ningún entorno; respaldado en $RESPALDO"
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
