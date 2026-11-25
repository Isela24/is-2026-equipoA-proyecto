# RITMO — Multiusuario con tu esquema MariaDB existente

Esta versión adapta el frontend RITMO y su backend PHP al `schema.sql` suministrado. **No crea tablas nuevas ni altera la estructura de `GestorHabitos`.**

## Modelo de datos que utiliza

| Tabla del esquema | Columnas principales | Función |
|---|---|---|
| `Usuarios` | `id_usuario`, `nombre`, `correo`, `password_hash`, `activo` | Registro, acceso e identidad |
| `Habitos` | `id_habito`, `id_usuario`, `id_categoria`, `nombre`, `color`, `fecha_creacion` | Hábitos privados por usuario |
| `Cumplimientos` | `id_cumplimiento`, `id_habito`, `fecha` | Cumplimiento único por día y hábito |
| `Categorias` | `id_categoria`, `nombre` | Catálogo compartido |
| `Etiquetas` | `id_etiqueta`, `nombre` | Catálogo compartido |
| `HabitoEtiquetas` | `id_habito`, `id_etiqueta` | Asociaciones de etiquetas |

**El acceso utiliza directamente `Usuarios.correo` y `Usuarios.password_hash`.** Las contraseñas se generan con `password_hash()` y se verifican con `password_verify()`; jamás se guardan como texto plano.

## 1. Instalación

1. Haz un respaldo de `gestorhabitos` desde phpMyAdmin → Exportar → SQL.
2. Copia el contenido de este paquete a la carpeta `web/` de tu repositorio; mantén las carpetas `docs/` y `base de datos/` sin cambios.
3. **Si ya habías instalado la versión anterior**, sustituye `web/api/auth.php` y `web/api/_common.php` además de los demás archivos; **no ejecutes** `instalar_auth.php` porque esta versión ya no requiere ninguna migración.
4. Mantén la configuración local `web/config/database.php`; o crea una copia de `web/config/database.example.php` y personaliza host/puerto/usuario/contraseña.
5. En XAMPP inicia **MySQL/MariaDB** y comprueba que PHP 8.1+ tenga habilitada la extensión `pdo_mysql`.
6. Desde PowerShell, en la raíz del repositorio, inicia:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t web
```

7. Entra a **http://127.0.0.1:8000/login.php** para crear una cuenta. Con Apache puedes instalar la carpeta bajo `htdocs` y abrir su `login.php` por `localhost`.

**No reimportes `schema.sql` ni borres datos.** El programa utiliza tu estructura actual, que ya tiene seis tablas y cuenta con campos `correo`, `password_hash` y `activo`.

## 2. Prueba de usuarios independientes

1. En "Crear cuenta" registra una persona de prueba con correo y contraseña de **10–72 bytes**.
2. En su dashboard crea el hábito **Leer 20 minutos** y márcalo como completado. Debe mostrarse en sus estadísticas y calendario.
3. Cierra sesión usando "Salir"; la página `index.php` debe volver a `login.php`.
4. Registra una segunda persona de prueba con **otro correo**. No debe ver el hábito anterior.
5. Crea el hábito **Hacer ejercicio**, ciérralo y vuelve a iniciar sesión con la primera cuenta. Comprueba que los datos continúan separados.
6. En phpMyAdmin, bajo `gestorhabitos`, comprueba con consultas de **solo lectura**:

```sql
SELECT id_usuario, nombre, correo, activo FROM Usuarios;
SELECT id_habito, id_usuario, nombre, color FROM Habitos;
SELECT id_cumplimiento, id_habito, fecha FROM Cumplimientos;
```

Verifica que cada hábito tiene el `id_usuario` de su propietario. Los cumplimientos están asociados a hábitos y el backend verifica esa pertenencia para toda operación.

### Control de acceso

- Sin sesión, `http://127.0.0.1:8000/api/habitos.php` debe responder **HTTP 401**.
- Con sesión, cada consulta de hábitos se limita a `id_usuario` autenticado.
- Intentar alterar un hábito de otra persona debe responder **HTTP 404**.
- Las cuentas `activo = 0` no pueden iniciar sesión ni seguir utilizando la API.
- Categorías y etiquetas son catálogos compartidos; no almacenan el progreso individual.

## 3. Archivos principales

```text
web/
  login.php                     Formulario de registro e inicio de sesión
  index.php                     Dashboard protegido
  assets/auth.css               Estilo del login
  assets/auth.js                Comunicación del formulario con la API
  assets/style.css              Estilo del dashboard
  assets/app.js                 Vista de hábitos, calendario y estadísticas
  api/_session.php              Configuración de cookies y sesiones
  api/_common.php               PDO, seguridad y operaciones de datos
  api/auth.php                  Registro, inicio y cierre de sesión contra Usuarios
  api/bootstrap.php             Datos privados al iniciar dashboard
  api/habitos.php               Hábitos del usuario
  api/cumplimientos.php         Cumplimientos del usuario
  api/categorias.php            Catálogo compartido de categorías
  api/etiquetas.php             Catálogo compartido de etiquetas
  api/rachas.php                Rachas
  api/estadisticas.php          Estadísticas personales
  api/diagnostico.php           Columnas reales (exige sesión y localhost)
  config/database.example.php  Plantilla sin contraseña real
  config/.htaccess              Impide servir archivos de config por Apache
  .gitignore                    Excluye config/database.php
```

## 4. Seguridad y límites

- Consultas preparadas con PDO para valores ingresados por el usuario.
- `password_hash()` y `password_verify()` para almacenar y verificar contraseñas.
- Regeneración del ID de sesión al autenticar; cookies `HttpOnly`, `SameSite=Lax` y `Secure` si HTTPS está activo.
- Token CSRF para operaciones POST, PUT y DELETE.
- Verificación del dueño en edición, eliminación y cumplimiento de hábitos.
- Límite básico de intentos de acceso **por sesión**, que debe reforzarse para producción.
- **No publiques** `config/database.php` ni contraseñas en GitHub.

Esta entrega está preparada para pruebas locales. Antes de publicar la plataforma en Internet se requieren HTTPS, credenciales MariaDB de permisos mínimos, mayor protección ante ataques de fuerza bruta, respaldos, gestión de errores y auditoría de seguridad. **La conexión real con tu MariaDB local debe comprobarse en tu propia computadora.**

## 5. Si ya usaste el paquete anterior

La versión anterior creó o intentó crear una tabla `ritmo_cuentas`. **Este código ya no la usa**. Si llegó a crearse, puedes dejarla sin usar por ahora; no la borres sin respaldo ni verificación de datos. Las credenciales auténticas de esta versión son las de `Usuarios.password_hash`.

Con tu `schema.sql` concreto, la versión anterior también guardaba el hash en `Usuarios.password_hash` al registrar cuentas nuevas. Por eso esas cuentas deberían poder ingresar con la versión corregida; compruébalo con sus credenciales. No copies hashes ni reasignes hábitos sin verificar la identidad y la correspondencia de `id_usuario`.
