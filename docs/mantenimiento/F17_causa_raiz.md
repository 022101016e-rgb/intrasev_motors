# F17 - Diagnostico y analisis de causa raiz (INC-01)

| Pregunta | Respuesta respaldada por evidencia |
|---|---|
| Sintoma concreto reproducido | Con 3 prestamos activos `GET /mantenimiento/evaluar/3` devuelve `puede_registrar: true` (E1). |
| Hipotesis principal | La comparacion incluye el valor maximo (`<=`) cuando deberia excluirlo (`<`). |
| Componente examinado | `app/Services/PrestamoService.php`, metodo `puedeRegistrar()`; tambien `DiagnosticoPrestamo` (controlador). |
| Prueba que descarto otras causas | `testRechazaAlAlcanzarElLimite` falla sobre el servicio aislado, sin HTTP ni base de datos (E2). Los casos con 2 y 4 prestamos pasan, lo que descarta controlador, ruta, vista y datos. |
| Causa tecnica comprobada | `return $actuales <= $this->maximo;`. `$actuales` es la cantidad ANTES de registrar el nuevo prestamo; quien ya tiene el maximo no puede registrar otro, por lo que la condicion correcta es `<`. |
| Condicion del proceso que permitio el escape | No existia una prueba automatizada de frontera (actuales = maximo) vinculada al requisito. |
| Que se cambia y que se preserva | Cambia solo el operador de comparacion. Se preservan la firma del metodo, las excepciones por valores invalidos y los casos con 2 y 4 prestamos. |

## Cinco porques
1. Por que se acepta un prestamo adicional? Porque el servicio responde verdadero en el limite.
2. Por que responde verdadero? Porque compara `actuales <= maximo`.
3. Por que esa comparacion es incorrecta? Porque `actuales` se evalua antes de registrar el nuevo prestamo.
4. Por que no se detecto? Porque no habia prueba automatizada para `actuales = maximo`.
5. Que mejora evita repetirlo? Incluir pruebas de frontera vinculadas al requisito (ya agregadas en A3).
