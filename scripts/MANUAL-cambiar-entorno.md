# Manual · Cambiar el entorno local (desarrollo / producción)

Script: `scripts/cambiar-entorno.sh`

Sirve para elegir a qué base de datos (y correo/S3) se conecta la aplicación que corre en tu
máquina, sin editar el `.env` a mano.

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

    1) Desarrollo   → .env.desarrollo (base de pruebas)
    2) Producción   → .env.produccion (datos REALES)
    3) Mantener el .env actual (solo ver estado)

  Elige 1, 2 o 3:
```

| Opción | Qué hace |
|---|---|
| **1 Desarrollo** | Copia `.env.desarrollo` → `.env`, recrea los contenedores y muestra la conexión (BD `desarrolloappcorpen`, correo a Mailpit). |
| **2 Producción** | Pide escribir `produccion` para confirmar. Copia `.env.produccion` → `.env`, recrea los contenedores y muestra la conexión. **Todo lo que hagas afecta datos, correos y archivos reales.** |
| **3 Mantener** | No cambia nada; solo muestra en qué entorno estás y a qué BD/correo/S3 está conectada la app. |
| Otra tecla | "Opción no válida. No se cambió nada." |

Atajos sin menú (útiles en scripts):

```bash
bash scripts/cambiar-entorno.sh desarrollo
bash scripts/cambiar-entorno.sh produccion
bash scripts/cambiar-entorno.sh estado
```

> También funciona `./scripts/cambiar-entorno.sh`, pero si sale *Permission denied* (pasa cuando
> el archivo se edita desde Windows) usa `bash scripts/...` o corre una vez
> `chmod +x scripts/cambiar-entorno.sh`.

Al terminar verás un resumen como:

```
  ✔ ENTORNO: DESARROLLO (base de pruebas, correo a Mailpit)
→ Verificando conexión real de la app...
BD:     CGMBOG23-DB:3308 / desarrolloappcorpen
Correo: host.docker.internal:1025
S3:     (desactivado)
```

Si la línea **BD** no muestra la base esperada, no trabajes hasta revisarlo.

---

## 3. Los archivos `.env`

En la raíz del proyecto solo existen tres, y **ninguno se sube a git**:

| Archivo | Uso |
|---|---|
| `.env` | El que usa la app en este momento. **No lo edites a mano**: el script lo sobrescribe. |
| `.env.desarrollo` | Configuración de desarrollo (base de pruebas). |
| `.env.produccion` | Configuración de producción. Contiene credenciales reales: no lo compartas ni lo copies. |

Reglas:

- Un cambio permanente (nueva variable, otra contraseña, etc.) se hace en `.env.desarrollo` o
  `.env.produccion`, y después se ejecuta el script para aplicarlo.
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
| `No existe .env.desarrollo` / `.env.produccion` | Falta el archivo en la raíz; pídelo al líder técnico. |
| `No se pudo verificar la conexión` | El contenedor `app` no está corriendo (`docker compose up -d`) o el usuario/contraseña de la BD es incorrecto. |
| `El servidor de correo no responde` (en desarrollo) | Falta Mailpit: `docker run -d --name mailpit --restart unless-stopped -p 8025:8025 -p 1025:1025 axllent/mailpit` |
| `Permission denied` | Ejecuta con `bash scripts/cambiar-entorno.sh`. |
