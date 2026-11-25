# Adaptación al schema.sql enviado

Se comparó el modelo de datos con `/schema.sql` (MariaDB 10.4+), que define seis tablas y una `Usuarios` con `correo`, `password_hash`, `activo` y fechas predeterminadas.

## Correcciones realizadas

- `api/auth.php` registra directamente en `Usuarios` y verifica contraseñas en `Usuarios.password_hash`.
- `api/_common.php` valida que el `id_usuario` autenticado exista y tenga `activo = 1`.
- Se eliminó la dependencia de `ritmo_cuentas` y ya **no existe instalador ni migración**.
- No se cambia `Habitos`, `Cumplimientos`, `Categorias`, `Etiquetas` ni `HabitoEtiquetas`.
- Cada operación consulta o modifica hábitos vinculados a `Habitos.id_usuario` de la sesión autenticada.
- Los catálogos de categorías y etiquetas siguen siendo compartidos por diseño.

## Precauciones

- Usa el `database.php` local; nunca publiques contraseñas.
- Si ejecutaste previamente el instalador anterior, la tabla `ritmo_cuentas` podría existir. Este proyecto la ignora; no la borres sin copia de seguridad.
- Si hay cuentas previas con `password_hash` válido en `Usuarios`, pueden iniciar sesión con su correo y contraseña.
- No se ha podido ejecutar un acceso real a tu servidor MariaDB local; debes probarlo en XAMPP.
