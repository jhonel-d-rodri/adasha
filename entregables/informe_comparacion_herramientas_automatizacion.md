# Informe de pruebas del segundo módulo

## Comparación de herramientas de automatización en el sistema ADASHA

**Estudiante:** ______________________________  
**Asignatura:** ______________________________  
**Docente:** _________________________________  
**Fecha:** ___________________________________

## 1. Introducción

El presente informe describe las pruebas automatizadas realizadas al sistema ADASHA, una aplicación web desarrollada con Laravel para gestionar proyectos, planos arquitectónicos y cálculos de materiales. La actividad compara los tipos de herramientas de prueba disponibles en el proyecto y presenta los resultados obtenidos al ejecutar la suite automatizada.

## 2. Objetivo

Verificar mediante pruebas automatizadas que las funciones principales de ADASHA respondan correctamente y comparar las pruebas unitarias con las pruebas funcionales (Feature) incluidas en Laravel/PHPUnit.

## 3. Herramientas utilizadas

| Herramienta | Uso en el sistema | Ventajas observadas | Alcance |
|---|---|---|---|
| PHPUnit | Motor de ejecución y aserciones de pruebas | Permite automatizar verificaciones repetibles y detectar regresiones | Ejecuta tanto pruebas unitarias como funcionales |
| Pruebas Unit de Laravel | Comprueban unidades pequeñas de lógica, como el estado de un proyecto | Son rápidas y aíslan reglas de negocio | No recorren toda la aplicación web |
| Pruebas Feature de Laravel | Envían solicitudes a rutas y comprueban respuestas, permisos, validaciones y persistencia | Verifican el comportamiento integrado desde la aplicación | Requieren más configuración y suelen tardar más |
| SQLite en memoria | Base de datos temporal definida para el entorno de pruebas | Aísla los datos y evita modificar la base de datos de desarrollo | Los resultados se limitan al entorno simulado de pruebas |

Laravel proporciona la estructura de pruebas y PHPUnit ejecuta los casos. Por ello, la comparación se realiza entre enfoques de prueba disponibles en el proyecto, no entre productos independientes de automatización.

## 4. Módulos y funciones comprobados

Las pruebas automatizadas existentes abarcan estas áreas del sistema:

- **Inicio de sesión:** formulario, ingreso válido, rechazo de credenciales incorrectas, validación de campos obligatorios y protección del dashboard.
- **Proyectos:** estados del proyecto y acceso a los datos asociados.
- **Planos:** permisos de acceso a archivos, almacenamiento privado y rechazo de archivos PDF donde se requiere una imagen.
- **Cálculo de materiales:** validación de medidas, área de vanos, cálculos para construcciones de dos plantas y tipos de cubierta.
- **Reportes:** aislamiento de la información de cada usuario y filtrado de cálculos por proyecto.
- **Seguridad y consistencia:** límite de intentos de inicio de sesión, rechazo de callback de Google inválido y conservación de los cálculos anteriores ante un fallo simulado.
- **Dashboard y perfil:** actividad semanal y visualización de la imagen de perfil.

## 5. Procedimiento

1. Se revisó la configuración del proyecto y se constató que PHPUnit ejecuta las carpetas `tests/Unit` y `tests/Feature`.
2. Se confirmó que el entorno de pruebas usa SQLite en memoria.
3. Se ejecutó el comando `php artisan test` desde la raíz del proyecto.
4. Se revisaron el estado final de la ejecución, el número de pruebas y las aserciones reportadas.

## 6. Resultados

La ejecución final terminó correctamente:

- **Pruebas aprobadas:** 33.
- **Pruebas fallidas:** 0.
- **Aserciones completadas:** 134.
- **Duración informada por PHPUnit:** 17,25 segundos.

Los resultados indican que los casos automatizados incluidos en el repositorio pasaron en el entorno de pruebas. En particular, se verificó que los usuarios no accedan a planos o reportes ajenos, que las entradas inválidas sean rechazadas y que las cantidades calculadas para un caso de dos plantas coincidan con los valores esperados por la prueba.

## 7. Comparación de los enfoques

| Criterio | Prueba unitaria | Prueba funcional (Feature) |
|---|---|---|
| Qué comprueba | Una regla o unidad de lógica específica | La interacción de varias partes de la aplicación mediante solicitudes |
| Ejemplo en ADASHA | Estados posibles de un proyecto | Inicio de sesión, permisos de planos y cálculo de materiales |
| Velocidad habitual | Más rápida | Puede tardar más por la preparación de base de datos, sesión y solicitudes |
| Tipo de defectos que ayuda a encontrar | Errores en reglas concretas | Errores de integración entre rutas, controladores, permisos y datos |
| Resultado de esta ejecución | 4 pruebas de estado de proyecto y una prueba de ejemplo | 28 pruebas funcionales, incluidas autenticación, planos, cálculos y reportes |

La suite contiene 5 pruebas Unit y 28 pruebas Feature. Las pruebas unitarias son útiles para localizar fallos en reglas pequeñas; las pruebas funcionales ofrecen una revisión más cercana al uso real de los módulos web. Para ADASHA se complementan: ninguna sustituye a la otra.

## 8. Conclusión

La ejecución automatizada de ADASHA fue satisfactoria: las 33 pruebas y sus 134 aserciones finalizaron sin errores. PHPUnit, junto con las herramientas de prueba de Laravel y SQLite en memoria, permite revisar de forma repetible las reglas de estado y los flujos principales del sistema.

Para las funciones web de ADASHA, las pruebas Feature brindan la cobertura más directa de los recorridos de usuario, permisos y validaciones. Las pruebas Unit siguen siendo valiosas para verificar de manera rápida la lógica aislada, como el estado de un proyecto. Se recomienda conservar ambos enfoques y ampliar los casos cuando se agreguen nuevas funciones.

## 9. Evidencias para adjuntar

Adjuntar capturas reales de la terminal después de ejecutar `php artisan test`:

1. Captura del resumen que muestra `Tests: 33 passed (134 assertions)`.
2. Captura de los grupos Unit y Feature aprobados.
3. Si el docente pide evidencia visual de la aplicación, capturas de los módulos de inicio de sesión, planos, cálculo y reportes usando datos de prueba.

**Nota:** las capturas deben obtenerse en el equipo del estudiante al ejecutar o abrir el sistema; este informe no inventa evidencias visuales.

## 10. Referencias del proyecto

- `composer.json`: dependencias y comando de pruebas.
- `phpunit.xml`: suites Unit y Feature y configuración de SQLite en memoria.
- `tests/Unit/ProyectoEstadoTest.php`: pruebas de estados de proyecto.
- `tests/Feature/InicioSesionTest.php`: pruebas de autenticación.
- `tests/Feature/CorreccionesSistemaTest.php`: pruebas de planos, cálculos y seguridad.
- `tests/Feature/AccesoReportesTest.php`: pruebas de permisos y filtrado de reportes.
