# Guía 1 – Unidad 2: Diseño del Plan de Pruebas

## Documentación hasta la Parte III

**Asignatura:** Análisis y Diseño de Sistemas de Información II
**Guía de Laboratorio N.°6:** Diseño del Plan de Pruebas
**Proyecto:** IntraServ Motors

---

## 1. Título de la práctica

Diseño de un plan de pruebas para un módulo del sistema.

---

## 2. Propósito

Diseñar de manera sistemática las pruebas de un módulo real del proyecto. La práctica **no** consiste en automatizar pruebas todavía; busca definir previamente:

- Qué se va a probar.
- Qué requisitos serán verificados.
- Qué condiciones deben evaluarse.
- Qué datos serán utilizados.
- Qué resultado debería producir cada prueba.

El trabajo servirá como base para las siguientes actividades de ejecución y automatización de pruebas.

---

## 3. Objetivo

Diseñar un plan reducido de pruebas para un módulo del sistema, estableciendo:

- Alcance.
- Estrategia.
- Criterios de entrada y salida.
- Trazabilidad con requisitos.
- Casos de prueba **positivos**, **negativos** y **de frontera** con resultados esperados verificables.

---

## 4. Resultados esperados

Al finalizar la práctica, el estudiante deberá ser capaz de:

1. Seleccionar un módulo relevante del proyecto.
2. Delimitar claramente el alcance de las pruebas.
3. Identificar los requisitos que serán evaluados.
4. Elaborar una matriz de trazabilidad requisitos–pruebas.
5. Diseñar casos de prueba positivos.
6. Diseñar casos de prueba negativos.
7. Diseñar casos asociados a valores o condiciones de frontera.
8. Especificar precondiciones y datos de entrada.
9. Formular resultados esperados verificables.
10. Establecer criterios de entrada y salida.

---

## 5. Herramientas necesarias

- Proyecto CodeIgniter 4 desarrollado por el equipo.
- Editor de código.
- Navegador web.
- Servidor local de desarrollo.
- Base de datos destinada a pruebas.
- Git y repositorio del proyecto.
- Requisitos y Product Backlog elaborados anteriormente.
- Opcionalmente Postman o herramienta equivalente, cuando el módulo exponga endpoints o servicios HTTP.

---

## 6. Fundamento teórico

Se utilizan cuatro conceptos fundamentales:

### 6.1 Plan de pruebas

Un plan de pruebas establece:

```
Qué probar + Cómo probar + Con qué condiciones
```

En esta práctica se desarrollará un plan reducido, suficiente para organizar las pruebas de un módulo del proyecto.

### 6.2 Caso de prueba

Un caso de prueba debe especificar como mínimo:

- Requisito relacionado.
- Precondición.
- Datos de entrada.
- Acción o pasos principales.
- Resultado esperado.

### 6.3 Tipos de casos

- **Positivos:** condiciones válidas.
- **Negativos:** condiciones inválidas o prohibidas.
- **De frontera:** valores cercanos a límites relevantes.

### 6.4 Trazabilidad

La relación fundamental es:

```
Requisito → Criterio de aceptación → Caso de prueba
```

Cada caso deberá tener una justificación clara dentro del sistema.

---

## 7. Parte I. Selección y alcance

### Actividad 1. Seleccionar el módulo

Seleccionar un módulo funcional que se encuentre suficientemente implementado para ser evaluado. Ejemplos posibles:

- Estudiantes.
- Usuarios.
- Préstamos.
- Productos.
- Reservas.
- Solicitudes.
- Matrícula.
- Inventario.

> **Importante:** NO seleccionar todo el sistema, solo un módulo.

**Propuesta para IntraServ Motors:** módulo de *Servicios* (gestión de solicitudes de servicio de los vehículos de los clientes), por estar suficientemente implementado en el proyecto.

### Actividad 2. Identificar los requisitos

Seleccionar entre **2 y 4 requisitos funcionales** relacionados con el módulo y completar el formato:

| ID | Requisito | Criterio de aceptación principal |
|----|-----------|----------------------------------|
| RF-01 | | |
| RF-02 | | |
| RF-03 | | |
| RF-04 | | |

### Actividad 3. Definir el alcance

Indicar explícitamente qué funcionalidades están **dentro** y **fuera** de la prueba:

| Incluido en el alcance | Fuera del alcance |
|------------------------|-------------------|
| Registrar solicitud de servicio | Envío de notificaciones |
| Validar disponibilidad de horario | Generación de reportes |
| Validar estado del cliente | Módulo administrativo |
| Consultar solicitud | Facturación / pagos |

---

## 8. Parte II. Elaboración del plan reducido

### Actividad 4. Completar el plan de pruebas

El plan debe presentarse obligatoriamente con el siguiente formato:

| Elemento | Descripción |
|----------|-------------|
| Código del plan | PT-01 |
| Módulo | |
| Objetivo de las pruebas | |
| Requisitos considerados | |
| Alcance | |
| Fuera del alcance | |
| Nivel(es) de prueba | |
| Tipo(s) de prueba | |
| Estrategia | |
| Ambiente de prueba | |
| Datos requeridos | |
| Responsables | |
| Criterios de entrada | |
| Criterios de salida | |

### Actividad 5. Definir la estrategia

La estrategia debe explicar **brevemente cómo** se evaluará el módulo. No debe limitarse a escribir *"Se realizarán pruebas"*.

**Ejemplo:**

> Se evaluarán las funcionalidades principales del módulo mediante casos funcionales positivos, negativos y de frontera. Se utilizará una base de datos de prueba con información controlada. Los casos se ejecutarán inicialmente de manera manual desde la interfaz del sistema y se registrará el resultado obtenido para compararlo con el resultado esperado.

### Actividad 6. Definir criterios de entrada

Como mínimo considerar condiciones como:

- Módulo implementado.
- Aplicación ejecutable.
- Base de datos disponible.
- Datos de prueba preparados.
- Casos diseñados.

**Ejemplo:**

> El módulo debe ejecutarse sin errores de configuración y la base de datos de prueba debe contener clientes en diferentes estados.

### Actividad 7. Definir criterios de salida

Establecer condiciones **objetivas y comprobables**. Por ejemplo:

- Todos los casos definidos fueron ejecutados.
- Todos los casos críticos fueron evaluados.
- No existen defectos críticos pendientes.
- Los resultados fueron documentados.

> **Evitar criterios ambiguos** como *"Cuando todo funcione bien"*. El criterio debe poder comprobarse.

---

## 9. Parte III. Matriz de trazabilidad

### Actividad 8. Elaborar la matriz

Cada requisito debe estar relacionado con **uno o más casos**. Utilizar el siguiente formato:

| Req. | Descripción | Casos asociados |
|------|-------------|-----------------|
| RF-01 | | CP- |
| RF-02 | | CP- |
| RF-03 | | CP- |
| RF-04 | | CP- |

**Ejemplo:**

| Req. | Descripción | Casos asociados |
|------|-------------|-----------------|
| RF-03 | Registrar solicitud de servicio | CP-01, CP-02, CP-03, CP-04 |
| RF-04 | Registrar finalización de servicio | CP-05, CP-06 |

---

## Notas finales (partes siguientes, no incluidas)

La guía continúa con:

- **Parte IV.** Diseño de casos de prueba (ficha obligatoria).
- **Parte V.** Casos positivos (mínimo 2).
- **Parte VI.** Casos negativos (mínimo 3).
- **Parte VII.** Resultados esperados.
- Entregables, evidencias de revisión y criterios de evaluación (total 20 puntos).