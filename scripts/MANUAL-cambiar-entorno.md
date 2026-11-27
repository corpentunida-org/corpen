# Manual · Cambiar el entorno local (desarrollo / producción)

Script: `scripts/cambiar-entorno.sh`

Sirve para elegir a qué base de datos (y correo/S3) se conecta la aplicación que corre en tu
máquina, sin editar el `.env` a mano.

---

## 0. Primera vez en un equipo nuevo

1. Instala **Docker Desktop** y, en Windows, **Ubuntu (WSL)**. En Docker Desktop activa
   *Settings → Resources → WSL integration → Ubuntu*.
2. Clona el proyecto **dentro de Ubuntu** (no en `C:\`), por ejemplo en `~/proyectos/aplicacionp`:
   ```bash
   mkdir -p ~/proyectos && cd ~/proyectos
   git clone https://github.com/corpentunida-org/corpen.git aplicacionp
   cd aplicacionp
   ```
3. Pide al líder técnico tu `.env.desarrollo` (por un medio seguro, **nunca** por git o chat) y
   guárdalo en la raíz del proyecto. `.env.produccion` y `.env.backup` solo a quien los necesite.
4. Ejecuta:
   ```bash
   bash scripts/cambiar-entorno.sh desarrollo
   ```
   El script revisa todo solo:
   - Avisa si Docker no está instalado o encendido.
   - Avisa si falta algún `.env`.
   - Corrige un `.env` guardado con formato de Windows.
   - Si faltan `vendor/` o `public/build`, pregunta si los instala dentro del contenedor (la
     primera vez tarda varios minutos, porque también construye la imagen).
5. La última línea debe decir `BD: ... / desarrolloappcorpen`. La app queda en
   **http://localhost:83**.

Tu equipo debe poder llegar al servidor de base de datos (red de la oficina o VPN). Si no,
el script lo indica al final.

---

## 1. Dónde se ejecuta

El script es de bash y corre dentro de Ubuntu (WSL). Se puede lanzar de dos maneras:

**A) Desde PowerShell**, parado en la carpeta del proyecto
(`PS ...\\wsl.localhost\Ubuntu\home\sistemas\proyectos\aplicacionp>`), anteponiendo `wsl`:

```powershell
wsl bash scripts/cambiar-entorno.sh
```

`wsl` entra solo a la carpeta equivalente en Ubuntu, así que no hace falta `cd`. No funciona
sin `wsl` delante (PowerShell no entiende bash).

**B) Desde la consola de Ubuntu (WSL)** directamente. Dos formas de abrirla:

- **Desde VS Code (recomendado):** menú *Terminal → New Terminal*. Si la terminal dice
  `sistemas@...:~/proyectos/aplicacionp$`, ya estás en el lugar correcto. Si abre PowerShell,
  en la flecha junto al `+` de la terminal elige **Ubuntu (WSL)**.
- **Desde Windows:** abre la app **Ubuntu** (o escribe `wsl` en PowerShell) y luego:

  ```bash
  cd ~/proyectos/aplicacionp
  ```

Requisito: Docker Desktop encendido y los contenedores del proyecto levantados
(`docker compose up -d`).

---

## 2. Cómo se ejecuta

```bash
bash scripts/cambiar-entorno.sh
```

Aparece el menú:

```
  ¿Con cuál .env quieres trabajar?   (actual: desarrollo)

    1) Actual       → .env             (BD: ...)  recargar sin cambiar
    2) Desarrollo   → .env.desarrollo  (BD: ...)  pruebas
    3) Producción   → .env.produccion  (BD: ...)  ⚠ datos REALES
    4) Backup       → .env.backup      (BD: ...)  ⚠ copias de seguridad
    5) Mantener el .env actual (solo ver estado)

  Elige 1, 2, 3, 4 o 5:
```

Junto a cada opción se muestra el nombre de la base que tiene configurada ese archivo.

| Opción | Qué hace |
|---|---|
| **1 Actual** | No copia nada: deja el `.env` como está, recrea los contenedores y muestra la conexión. Útil si los contenedores quedaron con una configuración vieja. |
| **2 Desarrollo** | Copia `.env.desarrollo` → `.env`, recrea los contenedores y muestra la conexión. Aquí se hacen **todas** las pruebas. |
| **3 Producción** | Pide escribir `produccion` para confirmar. Copia `.env.produccion` → `.env`, recrea los contenedores y muestra la conexión. **Todo lo que hagas afecta datos, correos y archivos reales.** |
| **4 Backup** | Pide escribir `backup` para confirmar. Copia `.env.backup` → `.env`. Solo para consultar o restaurar copias de seguridad. |
| **5 Ver estado** | No cambia nada; solo muestra en qué entorno estás y a qué BD/correo/S3 está conectada la app. |
| Otra tecla | "Opción no válida. No se cambió nada." |

Atajos sin menú (útiles en scripts):

```bash
bash scripts/cambiar-entorno.sh actual
bash scripts/cambiar-entorno.sh desarrollo
bash scripts/cambiar-entorno.sh produccion
bash scripts/cambiar-entorno.sh backup
bash scripts/cambiar-entorno.sh estado
```

> También funciona `./scripts/cambiar-entorno.sh`, pero si sale *Permission denied* (pasa cuando
> el archivo se edita desde Windows) usa `bash scripts/...` o corre una vez
> `chmod +x scripts/cambiar-entorno.sh`.

Al terminar verás un resumen como:

```
  ✔ ENTORNO: DESARROLLO (pruebas sobre copia de producción)
→ Verificando conexión real de la app...
BD:     CGMBOG23-DB:3308 / desarrolloappcorpen
Correo: host.docker.internal:1025
S3:     (desactivado)
```

Si la línea **BD** no muestra la base esperada, no trabajes hasta revisarlo.

---

## 3. Los archivos `.env`

En la raíz del proyecto existen cuatro (el `.env` activo y tres entornos), y **ninguno se sube a git**:

| Archivo | Uso |
|---|---|
| `.env` | El que usa la app en este momento. **No lo edites a mano**: el script lo sobrescribe. |
| `.env.desarrollo` | **Desarrollo.** Base que recibe copias de producción. Aquí se ejecutan todas las pruebas de código antes de pasar a producción. |
| `.env.produccion` | **Producción.** Base real. Solo se usa cuando el código ya se probó en desarrollo. Contiene credenciales reales: no lo compartas ni lo copies. |
| `.env.backup` | **Backup.** Base de copias de seguridad y control de cambios, para restaurar o investigar un error grave. No es para trabajar a diario. |

Flujo: **desarrollo** (probar) → **producción** (publicar). **Backup** solo se usa para respaldar o
restaurar.

Reglas:

- Un cambio permanente (nueva variable, otra contraseña, etc.) se hace en `.env.desarrollo`,
  `.env.produccion` o `.env.backup`, y después se ejecuta el script para aplicarlo.
- Para agregar otro entorno: crea `.env.<nombre>`, añade `<nombre>` a la lista `ENTORNOS` al inicio
  del script y agrega su opción en el menú.
- Si editaste `.env` a mano, al cambiar de entorno el script **no crea respaldos**: solo avisa
  qué variables se descartaron (muestra nombres, nunca valores).
- Un compañero nuevo debe pedirle al líder técnico sus archivos `.env.desarrollo` y
  `.env.produccion` y guardarlos en la raíz del proyecto.

---

## 4. Problemas frecuentes

| Mensaje | Solución |
|---|---|
| `Docker no está disponible en esta terminal` | Docker Desktop está apagado, o no tiene activada la integración con Ubuntu (Settings → Resources → WSL integration). |
| PowerShell: `bash: no se reconoce...` | Faltó `wsl` delante: `wsl bash scripts/cambiar-entorno.sh`. |
| `No existe .env.desarrollo` / `.env.produccion` / `.env.backup` | Falta el archivo en la raíz; pídelo al líder técnico. |
| `No se pudo verificar la conexión` | El contenedor `app` no está corriendo (`docker compose up -d`) o el usuario/contraseña de la BD es incorrecto. |
| `El servidor de correo no responde` (en desarrollo) | Falta Mailpit: `docker run -d --name mailpit --restart unless-stopped -p 8025:8025 -p 1025:1025 axllent/mailpit` |
| `Permission denied` | Ejecuta con `bash scripts/cambiar-entorno.sh`. |
| `Docker está instalado pero NO está encendido` | Abre Docker Desktop y espera a que diga *Engine running*. |
| `No hay ningún archivo .env.<entorno>` | Equipo nuevo: falta recibir y guardar `.env.desarrollo` en la raíz. |
| `Este equipo aún no tiene: vendor/ ...` | Responde `s` para instalar dentro del contenedor. |
| `No se pudieron levantar los contenedores` | Otro programa usa el puerto 83, o falló la construcción de la imagen (revisa el error mostrado). |
| `El contenedor 'app' se detuvo al arrancar` | Revisa el log que muestra el script; normalmente faltan dependencias o el `.env` está incompleto. |
| Ayuda rápida | `bash scripts/cambiar-entorno.sh ayuda` |
