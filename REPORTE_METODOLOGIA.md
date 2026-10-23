# Reporte Técnico de Selección y Justificación Metodológica

**Asignatura:** Ingeniería de Software (IS-2026)  
**Unidad:** 1 - Cierre Sumativo  
**Proyecto Final:** Gestor y Visualizador de Hábitos Personales  
**Equipo de Desarrollo:** Equipo A, Grupo 7A  
**Integrantes:** Isela Carranco Martínez, Carla Guadalupe Ruiz Vallejo y Manuel Arreguín Tamayo  
**Docente:** Rosalba Castanedo Martínez  
**Institución:** Universidad Politécnica de Guanajuato  
**Fecha:** 22/10/2026

---

## 1. Caracterización del Sistema y Entorno de Proyecto

### Ámbito de Dominio

El proyecto pertenece al ámbito del desarrollo de software para la gestión y visualización de hábitos personales. Se propone desarrollar una aplicación que permita registrar actividades diarias, organizar los hábitos por categorías, calcular rachas de días consecutivos y consultar el progreso mediante gráficas.

### Problema Principal a Resolver

Muchas personas intentan mejorar sus rutinas mediante anotaciones en papel o notas digitales dispersas. Estos métodos dificultan el seguimiento de las actividades, favorecen los olvidos y no siempre permiten identificar con claridad el progreso alcanzado.

El sistema busca atender esta problemática mediante una herramienta que centralice los registros de hábitos y permita consultar su cumplimiento a lo largo del tiempo. De esta manera, los usuarios podrán conocer sus rachas, organizar sus actividades y visualizar sus avances.

### Complejidad Técnica y Criticidad

El sistema presenta una complejidad técnica moderada, debido a que contempla el registro y almacenamiento de hábitos, el seguimiento de los cumplimientos diarios, el cálculo de rachas consecutivas, la organización por categorías y la generación de estadísticas y gráficas.

La criticidad se considera baja en comparación con sistemas que requieren altos niveles de seguridad operacional, como los utilizados en los sectores médico o financiero. Sin embargo, es importante mantener la integridad de los registros, calcular correctamente las rachas y proporcionar resultados confiables.

Los principales atributos de calidad considerados son la mantenibilidad, la confiabilidad, la seguridad, la eficiencia y la aceptabilidad. Estos atributos se encuentran definidos en el archivo `CALIDAD.md`, tomando como referencia los criterios de ingeniería de software estudiados en Sommerville, capítulo 24.

### Perfil de Integrantes y Cliente

El equipo está conformado por tres estudiantes de Ingeniería en Datos e Inteligencia Artificial. Actualmente, el proyecto se encuentra en la etapa de requisitos y documentación. Se cuenta con historias de usuario, criterios de aceptación, estimaciones y una planificación inicial del Sprint 1.

Los requisitos son definidos y validados por el propio equipo. No se ha establecido un cliente externo ni un Product Owner independiente, por lo que las decisiones sobre prioridades y alcance se realizan de manera colaborativa.

Como herramientas de apoyo se contempla el uso de Visual Studio Code, Git, GitHub, Python y Microsoft SQL Server, de acuerdo con los documentos de paradigma y calidad del proyecto. La tecnología específica para desarrollar la interfaz web y las herramientas para generar gráficas quedan pendientes de selección.

---

## 2. Evaluación Multicriterio de Alternativas Metodológicas

Para seleccionar el enfoque de desarrollo se comparan los modelos clásicos, las metodologías ágiles y una alternativa híbrida adaptada a las características del equipo.

| Criterio de evaluación | Enfoque clásico (Cascada/Espiral) | Enfoque ágil (Scrum/XP) | Enfoque híbrido propuesto |
|---|---|---|---|
| Gestión de cambios | En Cascada, los cambios pueden requerir revisar etapas y documentos previos. Espiral permite reevaluar riesgos en cada ciclo. | Favorece la adaptación de requisitos mediante iteraciones y retroalimentación frecuente. | Permite ajustar las historias de usuario durante los ciclos de trabajo, conservando el registro de cambios mediante Git y Pull Requests. |
| Documentación exigida | Suele establecer documentación formal en las distintas etapas del desarrollo. | Prioriza el software funcional y mantiene la documentación necesaria para el desarrollo. | Conserva los documentos de requisitos, paradigma y calidad, complementándolos con historias de usuario y criterios de aceptación. |
| Participación del cliente | La revisión suele realizarse en etapas o entregas previamente establecidas. | Promueve la participación frecuente del Product Owner y la retroalimentación durante el desarrollo. | El equipo define y valida los requisitos, con revisiones periódicas de los avances y ajustes cuando sean necesarios. |
| Velocidad de entrega | En Cascada, el producto integrado suele entregarse después de completar las etapas principales. | Favorece la entrega de incrementos funcionales en ciclos cortos. | Divide el sistema en incrementos priorizados y establece revisiones de código y validaciones antes de integrar cambios. |

### Análisis de las alternativas

El modelo en Cascada proporciona una secuencia organizada de etapas, pero puede dificultar la incorporación de cambios cuando los requisitos necesitan ajustarse durante el desarrollo. El modelo Espiral permite considerar los riesgos en ciclos sucesivos, aunque implica actividades de análisis de riesgos que podrían resultar innecesarias para un proyecto de esta escala.

Las metodologías ágiles, como Scrum y XP, favorecen la adaptación de requisitos y la validación frecuente. Scrum proporciona una estructura para organizar el trabajo, mientras que XP aporta prácticas de desarrollo orientadas a la calidad del código.

El enfoque híbrido permite aprovechar estas ventajas y conservar la documentación formal solicitada en la asignatura. Además, se adapta a la organización actual del proyecto, que utiliza historias de usuario, estimaciones en Story Points, una planificación de Sprint y un tablero Kanban en GitHub Projects.

---

## 3. Justificación Fundada de la Metodología Seleccionada

**Metodología seleccionada:** Desarrollo incremental con prácticas de organización de Scrum, prácticas de calidad de XP y gestión visual mediante Kanban.

### Argumentación técnica fundamentada

De acuerdo con Sommerville (2011, capítulo 2), la selección del proceso de desarrollo debe considerar las características del sistema, la estabilidad de los requisitos, la experiencia del equipo, los riesgos y las restricciones del proyecto. No existe una metodología universal que resulte óptima para todos los sistemas.

Para el Gestor y Visualizador de Hábitos Personales se selecciona el desarrollo incremental porque permite implementar y validar las funcionalidades progresivamente. Este modelo ya se encuentra establecido en `PARADIGMA.md`, por lo que la propuesta mantiene la coherencia con las decisiones previas del equipo.

La organización del trabajo incorporará prácticas de Scrum, como la planificación de ciclos, la definición de objetivos y la priorización de historias de usuario. También se consideran prácticas de XP, como las pruebas unitarias, las pruebas funcionales y la revisión del código. El tablero Kanban de GitHub Projects permitirá visualizar las tareas pendientes, en preparación, en progreso, en revisión y terminadas.

La elección se fundamenta en los siguientes factores:

- **Volatilidad e incertidumbre de los requisitos:** aunque las funcionalidades principales ya están identificadas, pueden surgir ajustes al diseñar y validar el sistema. El desarrollo incremental permite incorporar estos cambios sin replantear todo el proyecto.
- **Tamaño y experiencia del equipo:** los tres integrantes pueden distribuir las tareas y mantener comunicación directa. La planificación y las revisiones permiten organizar el trabajo sin establecer una estructura administrativa excesiva.
- **Complejidad y calidad:** el cálculo de rachas, el almacenamiento de los registros y la generación de gráficas requieren validaciones para evitar resultados incorrectos. Las pruebas y las revisiones de código ayudan a detectar defectos.
- **Participación del cliente:** dado que el equipo define y valida los requisitos, las historias de usuario y sus criterios de aceptación servirán como referencia para comprobar que las funcionalidades cumplen los objetivos planteados.
- **Tiempo y entregas:** la división del trabajo en incrementos permite priorizar las funcionalidades principales y evaluar los resultados antes de incorporar nuevas características.

Por lo anterior, se considera que el desarrollo incremental, complementado con prácticas ágiles de organización y calidad, ofrece un equilibrio adecuado entre adaptabilidad, documentación, colaboración y mantenibilidad.

La propuesta no implica que el equipo haya implementado Scrum o XP de manera formal. Se utilizarán las prácticas que resulten pertinentes para el proyecto, manteniendo como modelo principal el desarrollo incremental definido en `PARADIGMA.md`.

---

## 4. Definición del Proceso Adaptado y Artefactos

### Fases o ciclos del proceso

El proceso se organizará en ciclos de trabajo cuya duración deberá acordar el equipo de acuerdo con el calendario académico. La planificación inicial contempla un Sprint 1, pero la duración definitiva de los ciclos todavía no está establecida.

**Fase 1. Análisis y planificación**

Se revisarán las necesidades del sistema, se priorizarán las historias de usuario y se definirán los criterios de aceptación. Cada historia deberá contar con una estimación y estar suficientemente clara antes de incorporarse al ciclo de trabajo.

**Fase 2. Diseño del sistema**

Se elaborarán los modelos necesarios para representar la estructura y el comportamiento del sistema. También se definirá la organización de los componentes y la forma de almacenar los hábitos y sus registros.

**Fase 3. Implementación incremental**

Las funcionalidades se desarrollarán progresivamente, comenzando por las de mayor prioridad. Los cambios se realizarán en ramas de trabajo y se registrarán mediante commits con los prefijos establecidos por el equipo.

**Fase 4. Pruebas y revisión**

Se comprobará el cumplimiento de los criterios de aceptación mediante pruebas funcionales y unitarias. El código se revisará mediante Pull Requests antes de integrarse en la rama principal.

**Fase 5. Integración y evaluación**

Se integrarán los cambios aprobados y se verificará que las funcionalidades incorporadas funcionen correctamente en conjunto. También se registrarán los defectos identificados y las correcciones necesarias.

**Fase 6. Documentación y entrega**

Se actualizarán los documentos técnicos, el tablero de GitHub Projects y los registros de cambios. Las versiones correspondientes a las entregas académicas se identificarán mediante etiquetas de Git cuando proceda.

### Planificación inicial del Sprint 1

La meta propuesta para el primer Sprint consiste en construir la funcionalidad base del sistema, permitiendo registrar hábitos, registrar su cumplimiento diario, calcular las rachas y organizar los hábitos mediante categorías.

| Historia | Funcionalidad | Estimación | Responsable |
|---|---|---:|---|
| HU-01 | Registrar un hábito | 3 puntos | @Isela24 |
| HU-02 | Registrar el cumplimiento diario | 3 puntos | @24030621 |
| HU-03 | Calcular racha de días consecutivos | 5 puntos | @Mane-Arreguin |
| HU-04 | Organizar hábitos por categorías | 3 puntos | @Isela24 |
| **Total** | | **14 puntos** | |

Las historias HU-05, correspondiente a la personalización con colores y etiquetas, y HU-06, correspondiente a la visualización del progreso mediante gráficas, permanecen en el Product Backlog y no forman parte del compromiso inicial del Sprint 1.

### Definition of Ready (DoR)

Una historia de usuario estará lista para incorporarse a un Sprint cuando:

- Tenga un identificador y nombre claramente definidos.
- Esté redactada con el formato Como / Quiero / Para.
- Cuente con criterios de aceptación en formato Gherkin.
- Tenga estimación en Story Points y prioridad.
- El equipo comprenda el objetivo de la historia.
- Se hayan identificado las dependencias que puedan impedir su desarrollo.

### Definition of Done (DoD)

Una historia de usuario se considerará terminada cuando:

- La funcionalidad haya sido implementada.
- Se hayan verificado sus criterios de aceptación.
- Se hayan realizado las pruebas correspondientes.
- El código haya sido revisado por al menos otro integrante mediante Pull Request.
- No existan errores conocidos que impidan el funcionamiento de la funcionalidad.
- Los cambios hayan sido integrados a la rama principal mediante Pull Request.

### Artefactos del proyecto

| Artefacto | Propósito |
|---|---|
| Historias de usuario | Identificar las necesidades del usuario y las funcionalidades esperadas. |
| Criterios de aceptación en Gherkin | Establecer las condiciones para verificar el comportamiento de cada historia. |
| Planificación del Sprint | Definir la meta, las historias comprometidas y sus estimaciones. |
| Product Backlog | Mantener las funcionalidades pendientes priorizadas. |
| Diagramas UML | Representar la estructura y el comportamiento del sistema. |
| Especificación de requisitos (SRS) | Documentar los requisitos funcionales y no funcionales del sistema. |
| Código fuente | Contener la implementación de las funcionalidades. |
| Pruebas unitarias y funcionales | Verificar el comportamiento del sistema y el cumplimiento de los requisitos. |
| `PARADIGMA.md` | Documentar los métodos, las herramientas y los procedimientos de desarrollo. |
| `CALIDAD.md` | Definir los atributos de calidad y las métricas objetivo. |
| `README.md` | Presentar el proyecto, sus integrantes y las tecnologías utilizadas. |
| Manual de usuario | Explicar cómo utilizar las funcionalidades disponibles. |
| GitHub Projects | Visualizar y organizar el estado de las tareas. |
| Pull Requests y etiquetas Git | Controlar las revisiones, integraciones y versiones del proyecto. |

Las historias de usuario, la planificación inicial del Sprint, `PARADIGMA.md`, `CALIDAD.md` y `README.md` forman parte de la documentación existente. Los demás artefactos deberán elaborarse o completarse conforme avance el proyecto.

### Matriz de responsabilidades del equipo

La distribución de responsabilidades se organizará de acuerdo con las tareas asignadas y los acuerdos de colaboración del equipo.

| Integrante o función | Responsabilidades |
|---|---|
| Isela Carranco Martínez (@Isela24) | Responsable de HU-01 y HU-04 en la planificación inicial. También participa en la documentación y las decisiones del equipo. |
| Carla Guadalupe Ruiz Vallejo (@24030621) | Responsable de HU-02 en la planificación inicial. También participa en las actividades de desarrollo, revisión y documentación que acuerde el equipo. |
| Manuel Arreguín Tamayo (@Mane-Arreguin) | Responsable de HU-03 en la planificación inicial. También participa en las actividades de desarrollo, revisión y documentación que acuerde el equipo. |
| Gestión de requisitos | Responsabilidad compartida: definir, priorizar y validar las historias de usuario. |
| Revisión de código y pruebas | Responsabilidad compartida: revisar cambios, comprobar criterios de aceptación y verificar la calidad del sistema. |

La asignación de historias de usuario no limita la colaboración entre integrantes. Las tareas de coordinación y facilitación del proceso podrán distribuirse entre los miembros de acuerdo con las necesidades del proyecto.

---

## 5. Compromiso de Calidad y Gobernanza de Código

### Alineación con PARADIGMA.md y CALIDAD.md

El proyecto seguirá los métodos, herramientas y procedimientos definidos en `PARADIGMA.md`. Asimismo, utilizará las métricas y reglas de calidad establecidas en `CALIDAD.md`, tomando como referencia los atributos de calidad de ingeniería de software estudiados en Sommerville, capítulo 24.

Los compromisos principales son los siguientes:

**1. Mantenibilidad**

- Mantener las funciones en un máximo objetivo de 15 líneas.
- Procurar una complejidad ciclomática menor que 5.
- Aplicar las convenciones PEP 8 y utilizar Flake8 para revisar el código Python.
- Utilizar nombres significativos y convenciones consistentes: `snake_case` para variables y funciones y `PascalCase` para clases.
- Separar la gestión de hábitos, la lógica del sistema y el acceso a la base de datos.

**2. Confiabilidad y seguridad**

- Validar los datos ingresados, incluidos el nombre del hábito, las fechas, la frecuencia y la duración cuando corresponda.
- Utilizar bloques `try-except` específicos para controlar errores previstos.
- Excluir credenciales y datos de conexión del repositorio mediante `.gitignore`.
- Utilizar consultas parametrizadas para las operaciones con Microsoft SQL Server, si se confirma esta tecnología en la implementación.

**3. Eficiencia**

- Utilizar estructuras de datos adecuadas para procesar los registros.
- Consultar únicamente los datos necesarios para las estadísticas y visualizaciones.
- Evitar operaciones redundantes durante el cálculo de rachas y la generación de métricas.

**4. Aceptabilidad y facilidad de uso**

- Diseñar opciones claras para registrar, consultar, modificar y eliminar hábitos.
- Mostrar mensajes comprensibles cuando los datos sean incorrectos o las operaciones se completen correctamente.
- Documentar las funciones principales mediante docstrings breves cuando corresponda.
- Facilitar que los usuarios gestionen sus hábitos sin necesitar conocimientos técnicos.

Estos criterios representan objetivos de calidad que deberán comprobarse durante la implementación y las pruebas. Su inclusión en el reporte no implica que ya se hayan alcanzado.

### Mecanismos de control de cambios

La gestión del código se realizará mediante Git y GitHub, siguiendo las reglas de colaboración establecidas por el equipo.

**Estrategia de ramas**

- Se prohíben los envíos directos a `main`.
- Los cambios deberán realizarse en ramas de trabajo, como `feature/*`.
- Cada rama deberá atender una funcionalidad, corrección o tarea identificable.

**Convención de commits**

Se utilizarán los siguientes prefijos:

- `feat:` para nuevas funcionalidades.
- `fix:` para correcciones de errores.
- `docs:` para documentación.
- `style:` para cambios de formato que no alteren la lógica.
- `refactor:` para mejoras en la estructura del código sin cambiar su comportamiento esperado.

**Revisión de código**

Todo Pull Request deberá incluir una descripción de los cambios realizados y recibir la aprobación escrita de al menos un integrante del equipo antes del merge. Durante la revisión se comprobará que el cambio cumpla los criterios de aceptación y respete las reglas de calidad del proyecto.

**Gestión de tareas**

GitHub Projects se utilizará para organizar las tareas y visualizar su estado. Las historias de usuario deberán mantener sus estimaciones, responsables y prioridades, de acuerdo con la planificación acordada.

**Gestión de versiones**

Las versiones importantes se identificarán mediante etiquetas de Git. Para el cierre sumativo de la Unidad 1 se utilizará la etiqueta `v1.0-u1`, una vez que el reporte metodológico y los cambios correspondientes hayan sido revisados e integrados en `main`.

---

## Referencia bibliográfica

Sommerville, I. (2011). *Software Engineering* (9.ª ed.). Addison-Wesley. Capítulos 2 y 24.