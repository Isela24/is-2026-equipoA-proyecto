# MATRIZ DE ATRIBUTOS DE CALIDAD Y ESTÁNDARES (SOMMERVILLE CAP. 24)

### 1. Mantenibilidad (Maintainability)

- **Métrica Objetivo:** Máximo 15 líneas por función; complejidad ciclomática < 5.
- **Estándar de Codificación:** Cumplimiento del estándar PEP 8 mediante Flake8 con 0 advertencias de sintaxis.
- **Nomenclatura:** Identificadores significativos en español o inglés (variables `snake_case`, clases `PascalCase`).
- **Organización del Código:** Separación de las funciones relacionadas con la gestión de hábitos, la lógica del sistema y el acceso a la base de datos para facilitar su mantenimiento.

### 2. Confiabilidad y Seguridad (Dependability & Security)

- **Validación de Entradas:** Validación de los datos ingresados por el usuario, como nombre del hábito, fecha, frecuencia y duración.
- **Manejo de Errores:** Uso de bloques `try-except` específicos para controlar errores durante la ejecución del sistema y la conexión con la base de datos.
- **Control de Datos:** Exclusión de credenciales, contraseñas o datos de conexión en el código fuente mediante `.gitignore`.
- **Consultas Seguras:** Uso de consultas parametrizadas para trabajar con los datos almacenados en Microsoft SQL Server.

### 3. Eficiencia (Efficiency)

- **Uso de Memoria:** Uso de estructuras de datos adecuadas en Python para manejar los registros de hábitos obtenidos de la base de datos.
- **Consultas SQL:** Uso de consultas que obtengan únicamente los datos necesarios para generar las métricas y tendencias de los hábitos.
- **Procesamiento de Datos:** Evitar operaciones innecesarias al generar estadísticas y visualizaciones de los registros.

### 4. Aceptabilidad (Acceptability)

- **Interfaz:** El sistema permitirá registrar, consultar, modificar y eliminar hábitos de manera sencilla.
- **Mensajes al Usuario:** El sistema mostrará mensajes claros cuando los datos sean incorrectos o cuando una operación se complete correctamente.
- **Documentación de Funciones:** Las funciones principales deberán incluir un `docstring` breve que describa su propósito, parámetros y valores de retorno cuando corresponda.
- **Facilidad de Uso:** Las opciones principales del sistema deberán ser claras para que el usuario pueda gestionar sus hábitos sin conocimientos técnicos.
