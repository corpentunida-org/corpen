# Migraciones desactivadas

Laravel solo ejecuta las migraciones de `database/migrations/`. Las de esta carpeta **no se
ejecutan nunca** con `php artisan migrate` (ni manual ni en el despliegue).

| Migraciones | Motivo | Fecha |
|---|---|---|
| `2026_11_23_*` (rsv_perfiles, rsv_roles, rsv_permisos, rsv_perfil_user, rsv_roles_user, rsv_roles_permisos, rsv_usuario_inmueble) | Migraciones de prueba de un esquema propio de perfiles/permisos RSV. Acordado con su autor (Stiven) que **no deben crearse en producción**. El módulo RSV usa los roles/permisos `rsv.*` de Spatie (`RsvPermissionsSeeder`). | 2026-11-27 |

Para reactivar alguna (previo acuerdo del equipo), muévela de vuelta a `database/migrations/`
con `git mv`. No agregues aquí migraciones que ya se ejecutaron en producción.
