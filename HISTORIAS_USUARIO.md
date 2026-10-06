# Inventario de Historias de Usuario - Proyecto Final

## Historia de Usuario HU-01: Registrar un hábito

- **ID:** HU-01
- **Nombre:** Registrar un hábito
- **Como:** Usuario final
- **Quiero:** Registrar un nuevo hábito indicando su nombre
- **Para:** Llevar un control de las actividades que deseo realizar diariamente
- **Estimación (Story Points):** 3
- **Prioridad:** Alta
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Registro exitoso):** **Dado que** el usuario se encuentra en la sección de registro de hábitos, **Cuando** ingresa el nombre de un hábito válido y confirma el registro, **Entonces** el sistema debe guardar el hábito y mostrarlo en la lista de hábitos.

  - **Escenario 2 (Nombre vacío):** **Dado que** el usuario se encuentra en la sección de registro de hábitos, **Cuando** intenta registrar un hábito sin ingresar un nombre, **Entonces** el sistema debe solicitar que complete el campo requerido.


## Historia de Usuario HU-02: Registrar el cumplimiento diario

- **ID:** HU-02
- **Nombre:** Registrar cumplimiento diario
- **Como:** Usuario final
- **Quiero:** Marcar un hábito como cumplido durante el día
- **Para:** Mantener un registro de los días en los que realizo cada hábito
- **Estimación (Story Points):** 3
- **Prioridad:** Alta
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Cumplimiento registrado):** **Dado que** el usuario tiene un hábito registrado, **Cuando** marca el hábito como cumplido durante el día, **Entonces** el sistema debe registrar el cumplimiento correspondiente a esa fecha.

  - **Escenario 2 (Consulta del cumplimiento):** **Dado que** el usuario ya registró el cumplimiento de un hábito, **Cuando** consulta sus hábitos, **Entonces** el sistema debe mostrar el hábito como cumplido para la fecha correspondiente.


## Historia de Usuario HU-03: Calcular racha de días consecutivos

- **ID:** HU-03
- **Nombre:** Calcular racha de días consecutivos
- **Como:** Usuario final
- **Quiero:** Consultar cuántos días consecutivos he cumplido un hábito
- **Para:** Conocer mi constancia y observar mi progreso
- **Estimación (Story Points):** 5
- **Prioridad:** Alta
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Racha consecutiva):** **Dado que** el usuario ha cumplido un hábito durante varios días consecutivos, **Cuando** consulta el hábito, **Entonces** el sistema debe mostrar la cantidad de días consecutivos cumplidos.

  - **Escenario 2 (Interrupción de la racha):** **Dado que** el usuario tiene una racha de días consecutivos y deja de cumplir el hábito durante un día, **Cuando** consulta nuevamente el hábito, **Entonces** el sistema debe actualizar la racha de acuerdo con los registros de cumplimiento.


## Historia de Usuario HU-04: Organizar hábitos por categorías

- **ID:** HU-04
- **Nombre:** Organizar hábitos por categorías
- **Como:** Usuario final
- **Quiero:** Organizar mis hábitos mediante categorías
- **Para:** Identificar y distinguir con mayor facilidad los diferentes tipos de actividades
- **Estimación (Story Points):** 3
- **Prioridad:** Media
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Asignación de categoría):** **Dado que** el usuario está registrando un hábito, **Cuando** selecciona una categoría disponible, **Entonces** el sistema debe asociar el hábito con la categoría seleccionada.

  - **Escenario 2 (Visualización por categoría):** **Dado que** existen hábitos registrados en diferentes categorías, **Cuando** el usuario consulta su lista de hábitos, **Entonces** el sistema debe permitir distinguir los hábitos de acuerdo con su categoría.


## Historia de Usuario HU-05: Personalizar hábitos con colores y etiquetas

- **ID:** HU-05
- **Nombre:** Personalizar hábitos con colores y etiquetas
- **Como:** Usuario final
- **Quiero:** Asignar colores y etiquetas a mis hábitos
- **Para:** Identificar y organizar visualmente mis actividades de acuerdo con mis preferencias
- **Estimación (Story Points):** 5
- **Prioridad:** Media
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Asignación de color):** **Dado que** el usuario tiene un hábito registrado, **Cuando** selecciona un color para identificarlo, **Entonces** el sistema debe mostrar el hábito utilizando el color seleccionado.

  - **Escenario 2 (Asignación de etiqueta):** **Dado que** el usuario tiene un hábito registrado, **Cuando** agrega una etiqueta al hábito, **Entonces** el sistema debe mostrar la etiqueta asociada al hábito.


## Historia de Usuario HU-06: Visualizar el progreso mediante gráficas

- **ID:** HU-06
- **Nombre:** Visualizar progreso mediante gráficas
- **Como:** Usuario final
- **Quiero:** Consultar gráficas sobre el cumplimiento de mis hábitos
- **Para:** Visualizar de forma clara mi progreso y las metas logradas
- **Estimación (Story Points):** 5
- **Prioridad:** Alta
- **Criterios de Aceptación (Gherkin):**
  - **Escenario 1 (Visualización del progreso):** **Dado que** el usuario cuenta con registros de cumplimiento, **Cuando** consulta la sección de progreso, **Entonces** el sistema debe mostrar una gráfica que represente su avance.

  - **Escenario 2 (Sin registros):** **Dado que** el usuario no cuenta con registros de cumplimiento, **Cuando** consulta la sección de progreso, **Entonces** el sistema debe mostrar que no existen datos suficientes para generar la gráfica.