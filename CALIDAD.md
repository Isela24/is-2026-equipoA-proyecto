# MATRIZ DE ATRIBUTOS DE CALIDAD Y ESTÁNDARES (SOMMERVILLE CAP. 24)

## 1. Mantenibilidad (Maintainability)

- **Métrica Objetivo:** Máximo 15 líneas por función y complejidad ciclomática menor a 5.

- **Estándar de Codificación:** Cumplimiento del estándar PEP 8 mediante Flake8,
  con 0 advertencias de sintaxis.

- **Nomenclatura:** Se utilizarán identificadores significativos en español o inglés.
  Las variables y funciones utilizarán `snake_case` y las clases utilizarán
  `PascalCase`.

## 2. Confiabilidad y Seguridad (Dependability & Security)

- **Validación de Entradas:** Se realizará un manejo explícito de excepciones mediante
  bloques `try-except`, evitando capturas genéricas de excepciones.

- **Control de Datos:** Se excluirán credenciales, contraseñas y tokens del código
  fuente mediante el archivo `.gitignore`.

## 3. Eficiencia (Efficiency)

- **Uso de Memoria:** Se procurará la liberación adecuada de recursos y se utilizarán
  estructuras de datos apropiadas de acuerdo con las necesidades del sistema, como
  listas y diccionarios.

## 4. Aceptabilidad (Acceptability)

- **Documentación de Funciones:** Todo método público deberá incluir un docstring breve
  que explique sus parámetros y valores de retorno.