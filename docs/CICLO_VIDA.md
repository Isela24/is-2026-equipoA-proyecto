# ANÁLISIS DEL CICLO DE VIDA Y GESTIÓN DE RIESGOS

## 1. Mapeo de Fases Clásicas

### 1.1 Requerimientos y Análisis

En esta fase se identifican las necesidades principales del Gestor y Visualizador de Hábitos Personales. Se definen las funciones para registrar hábitos diarios, consultar rachas de cumplimiento, organizar hábitos mediante categorías y visualizar el progreso mediante gráficos.

También se consideran los posibles cambios solicitados durante el desarrollo, como la incorporación de puntos, niveles, medallas, colores y etiquetas personalizadas.

### 1.2 Diseño de Arquitectura y Base de Datos

Se diseña la estructura de la aplicación web y la organización de los datos necesarios para almacenar usuarios, hábitos y registros de cumplimiento.

El diseño debe permitir incorporar posteriormente nuevas funcionalidades, como categorías, colores, etiquetas, puntos, niveles y medallas, sin tener que reconstruir completamente el sistema.

### 1.3 Implementación

Se desarrolla la aplicación web y sus funcionalidades de manera progresiva. Inicialmente se implementa el registro de hábitos y la visualización de las rachas.

Posteriormente pueden incorporarse nuevas funciones mediante incrementos, como categorías, colores y etiquetas.

### 1.4 Pruebas y Verificación

Se realizan pruebas para comprobar que el registro de hábitos, el cálculo de rachas, la organización de las actividades y la visualización de los gráficos funcionen correctamente.

Cada nueva funcionalidad incorporada mediante un incremento debe ser probada antes de integrarse con el resto del sistema.

### 1.5 Mantenimiento y Evolución

La aplicación se mantiene mediante correcciones y nuevas versiones. El enfoque incremental permite agregar funcionalidades de acuerdo con las necesidades del proyecto sin detener completamente las funciones existentes.

Entre las posibles ampliaciones se encuentran nuevos gráficos, métricas, categorías, etiquetas, puntos, niveles y medallas.