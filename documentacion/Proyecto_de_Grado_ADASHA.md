# INSTITUTO TECNOLÓGICO SACABA

## CARRERA DE SISTEMAS INFORMÁTICOS

# DESARROLLO DE UN SISTEMA WEB PARA EL CÁLCULO AUTOMATIZADO DE MATERIALES DE CONSTRUCCIÓN A PARTIR DEL ANÁLISIS DE PLANOS ARQUITECTÓNICOS MEDIANTE VISIÓN ARTIFICIAL PARA LA CONSTRUCTORA ADASHA

Proyecto de grado presentado para optar al título de Técnico Superior en Sistemas Informáticos.

**Postulante:** [Nombre completo]  
**Tutor:** [Nombre del tutor]  
**Gestión:** [Año]  
**Sacaba, Cochabamba – Bolivia**

---

# ÍNDICE PROPUESTO

1. CAPÍTULO I: INTRODUCCIÓN  
   1.1 Introducción · 1.2 Diagnóstico · 1.3 Planteamiento y formulación del problema · 1.4 Objetivos · 1.5 Justificación · 1.6 Alcances y delimitaciones.
2. CAPÍTULO II: MARCO TEÓRICO CONCEPTUAL  
   2.1 Metodología de investigación mixta · 2.2 Metodología de desarrollo Scrum · 2.3 Metodología de modelado · 2.4 Herramientas y tecnologías de implementación.
3. CAPÍTULO III: INGENIERÍA DEL PROYECTO  
   3.1 Requerimientos generales del sistema · 3.2 Aplicación de la metodología ágil Scrum.

> Al pasarlo a Word se debe usar **Referencias > Tabla de contenido** para que los números de página se generen automáticamente.

---

# CAPÍTULO I. INTRODUCCIÓN

## 1.1 Introducción

La planificación de una obra requiere estimar cantidades de materiales a partir de planos arquitectónicos. Cuando este proceso se realiza manualmente, requiere tiempo, puede producir errores de transcripción y dificulta el seguimiento de proyectos e inventario. La Constructora ADASHA requiere una herramienta centralizada para registrar proyectos, asociar planos, analizar información geométrica y generar estimaciones de materiales de obra gruesa.

El presente proyecto desarrolla un sistema web que integra gestión de proyectos, carga de planos, procesamiento asistido por visión artificial, cálculo referencial de materiales, reportes e inventario. El sistema está orientado a mejorar la organización de la información y a apoyar la toma de decisiones durante la etapa inicial de una construcción.

## 1.2 Diagnóstico

Antes del sistema, el registro de proyectos, datos de clientes, dimensiones de muros, materiales y cálculos podía dispersarse entre documentos, hojas de cálculo y comunicaciones informales. Esto dificulta consultar el avance, reutilizar información y controlar las existencias disponibles. Además, la medición manual de longitudes de muro desde un plano puede ser repetitiva.

## 1.3 Planteamiento y formulación del problema

### 1.3.1 Planteamiento del problema

¿Cómo mejorar el proceso de registro de proyectos y la estimación inicial de materiales de construcción a partir de planos arquitectónicos en la Constructora ADASHA?

## 1.4 Planteamiento de objetivos

### 1.4.1 Objetivo general

Desarrollar un sistema web para el cálculo automatizado y referencial de materiales de construcción, a partir del análisis de planos arquitectónicos mediante visión artificial, para la Constructora ADASHA.

### 1.4.2 Objetivos específicos

- Gestionar usuarios, proyectos, clientes y la ubicación geográfica de cada obra.
- Permitir el registro, carga, consulta y análisis de planos arquitectónicos.
- Extraer una longitud total referencial de muros a partir de imágenes de planos.
- Calcular cantidades estimadas de materiales de obra gruesa según las características del proyecto.
- Generar reportes visuales y en PDF con datos de proyecto, coordenadas y materiales.
- Administrar materiales, precios, stock mínimo y movimientos de inventario.
- Proteger la información mediante autenticación, roles y control de acceso por propietario.

## 1.5 Justificación

### 1.5.1 Justificación tecnológica

El sistema utiliza una arquitectura web MVC con Laravel, MySQL, PHP, Blade, Bootstrap, JavaScript y Python. La incorporación de OpenCV permite incorporar técnicas de visión artificial como apoyo al análisis del plano.

### 1.5.2 Justificación social

La centralización de información de clientes, proyectos y planos facilita la consulta de antecedentes de una obra y mejora la trazabilidad interna.

### 1.5.3 Justificación económica

La estimación inicial y el control de existencias ayudan a anticipar requerimientos de compra y reducen reprocesos administrativos. Las cantidades generadas deben considerarse referenciales y validarse por el profesional responsable de la obra.

## 1.6 Alcances y delimitaciones

### 1.6.1 Alcances

El sistema incluye gestión de proyectos, planos, cálculos de obra gruesa, materiales, inventario, reportes, autenticación local y acceso con Google.

### 1.6.2 Delimitaciones

El análisis de planos funciona como apoyo para obtener una longitud referencial; la precisión depende de la calidad, escala y tipo de imagen cargada. No reemplaza el cálculo estructural ni la aprobación de un arquitecto o ingeniero civil.

---

# CAPÍTULO II. MARCO TEÓRICO CONCEPTUAL

## 2.1 Metodología de investigación mixta

### 2.1.1 Definición

La investigación mixta integra procedimientos cualitativos y cuantitativos. En este proyecto, la parte cualitativa permite comprender las necesidades de gestión de la Constructora ADASHA; la parte cuantitativa permite organizar datos de proyectos, longitudes, dimensiones, cantidades de materiales y movimientos de inventario.

### 2.1.2 Tipos de enfoque de investigación

#### 2.1.2.1 Enfoque cualitativo

Se aplica al identificar problemas del proceso manual, necesidades de usuarios y criterios de usabilidad de las interfaces.

#### 2.1.2.2 Enfoque cuantitativo

Se aplica al cálculo de cantidades, dimensiones, áreas de vanos, existencias y resultados de pruebas.

#### 2.1.2.3 Herramientas de recolección

Se utilizan observación del proceso de trabajo, entrevistas o consultas a usuarios de la constructora, revisión de planos y pruebas del sistema.

## 2.2 Metodología de desarrollo: Scrum

Se adopta Scrum como enfoque ágil. El trabajo se organiza en iteraciones que priorizan módulos funcionales y verificables: autenticación, gestión de proyectos, procesamiento de planos, cálculo/reportes e inventario.

### 2.2.1 Roles y artefactos en Scrum

El Product Owner representa las necesidades de la Constructora ADASHA; el equipo de desarrollo implementa el sistema; y el tutor acompaña la revisión académica. Los artefactos son el Product Backlog, Sprint Backlog e incremento funcional.

### 2.2.2 Etapas del ciclo de vida en Scrum

Las etapas consideradas son: inicio, planificación, implementación, revisión, retrospectiva y entrega del incremento.

### 2.2.3 Historias de usuario

Ejemplo: *Como administrador, quiero registrar entradas y salidas de materiales para conocer el stock disponible y evitar faltantes en una obra.*

## 2.3 Metodología de modelado

### 2.3.1 Requerimientos funcionales y no funcionales

Los requerimientos se especifican mediante funcionalidades, restricciones, actores y criterios de validación.

### 2.3.2 UML

UML permite representar casos de uso, clases, secuencias y actividades del sistema antes de documentar la implementación.

### 2.3.3 Modelado de la base de datos relacional

El modelo relacional define entidades, claves primarias, claves foráneas y relaciones entre usuarios, proyectos, planos, cálculos, materiales y movimientos.

## 2.4 Herramientas y tecnologías de implementación

### 2.4.1 Lenguajes de programación, IDE y herramientas de desarrollo

PHP, JavaScript, HTML5, CSS, Python, Visual Studio Code, Composer y XAMPP.

### 2.4.2 Sistema gestor de base de datos

MySQL se utiliza para la persistencia de información; XAMPP proporciona el entorno local de Apache, PHP y MySQL.

### 2.4.3 Herramientas de diseño

Bootstrap y CSS propio se utilizan para construir interfaces responsivas y consistentes.

### 2.4.4 Frameworks y librerías

Laravel, Blade, Laravel Socialite, DomPDF, OpenCV, Leaflet y Bootstrap.

### 2.4.5 Control de versiones

Git permite mantener historial de cambios y respaldar el código fuente.

### 2.4.6 Herramientas de pruebas

PHPUnit y las pruebas de Laravel validan reglas de negocio, acceso y rutas críticas.

### 2.4.7 Administración de proyectos

El seguimiento de tareas puede organizarse mediante tablero de Product Backlog y Sprints.

<!-- Material complementario del marco conceptual -->
## 2.5 Arquitectura MVC

El patrón Modelo–Vista–Controlador separa la persistencia de datos, la lógica de negocio y las interfaces. Los modelos representan las entidades de la base de datos; los controladores validan solicitudes y aplican reglas; las vistas Blade presentan la información al usuario.

## 2.6 Visión artificial

La visión artificial aplica algoritmos para obtener información desde imágenes. En ADASHA, un script en Python procesa archivos de plano compatibles y entrega resultados que el sistema conserva como datos extraídos. OpenCV es una biblioteca de código abierto orientada a visión por computador y procesamiento de imágenes.

## 2.7 Tecnologías utilizadas

| Tecnología | Uso en ADASHA |
|---|---|
| Laravel 12 / PHP 8.2 | Aplicación web, rutas, controladores, modelos y validaciones. |
| MySQL | Persistencia de usuarios, proyectos, planos, cálculos e inventario. |
| Blade, Bootstrap y CSS | Interfaces responsivas. |
| JavaScript | Interacciones de formularios y mapa. |
| Python y OpenCV | Procesamiento de planos e identificación de datos geométricos. |
| Leaflet y OpenStreetMap | Visualización de ubicación geográfica. |
| Laravel Socialite | Autenticación mediante Google. |
| DomPDF | Generación de reportes descargables en PDF. |
| PHPUnit | Pruebas automatizadas. |

## 2.8 Requerimientos funcionales

| Código | Requerimiento |
|---|---|
| RF-01 | El sistema debe permitir iniciar y cerrar sesión. |
| RF-02 | Debe permitir autenticación con Google y recuperación de contraseña. |
| RF-03 | El administrador debe gestionar usuarios y materiales. |
| RF-04 | Debe permitir registrar, editar, visualizar y eliminar proyectos según permisos. |
| RF-05 | Debe registrar cliente, ubicación, coordenadas, fechas y dimensiones constructivas. |
| RF-06 | Debe permitir cargar y analizar planos autorizados. |
| RF-07 | Debe calcular materiales de obra gruesa de forma referencial. |
| RF-08 | Debe visualizar y descargar reportes PDF. |
| RF-09 | Debe controlar entradas, salidas y stock mínimo de materiales. |

## 2.9 Requerimientos no funcionales

- Interfaz web clara y adaptable a pantallas pequeñas.
- Autenticación y autorización por roles `admin` y `usuario`.
- Validación de datos de entrada y archivos cargados.
- Persistencia de datos en MySQL.
- Zona horaria configurada para America/La_Paz.
- Reportes accesibles solo para el propietario del proyecto o un administrador.

---

# CAPÍTULO III. INGENIERÍA DEL PROYECTO

## 3.1 Requerimientos generales del sistema

### 3.1.1 Requerimientos funcionales generales

Los requisitos funcionales se describen en la sección 2.8 y se validan en los módulos de usuarios, proyectos, planos, cálculos, reportes e inventario.

### 3.1.2 Requerimientos no funcionales generales

Los requisitos no funcionales se describen en la sección 2.9 e incluyen seguridad, validación, usabilidad y persistencia de datos.

### 3.1.3 Identificación y descripción de los actores del sistema

| Actor | Responsabilidades |
|---|---|
| Administrador | Gestiona usuarios, catálogo de materiales, inventario y consulta todos los proyectos. |
| Usuario | Gestiona sus propios proyectos, planos, cálculos y reportes. |
| Servicio externo | Google autentica cuentas; Nominatim/Leaflet apoya la ubicación; Python/OpenCV procesa el plano. |

### 3.1.4 Administrador

Administra usuarios, materiales, movimientos de inventario y puede consultar toda la información registrada.

### 3.1.5 Usuario técnico

Registra y consulta sus proyectos, planos, cálculos y reportes autorizados.

### 3.1.6 Profesional de construcción

Utiliza los resultados como apoyo y valida técnicamente las cantidades antes de la ejecución de obra.

### 3.1.7 Diagrama de casos de uso general

**[Insertar diagrama de casos de uso general de ADASHA]**

- Iniciar sesión y recuperar contraseña.
- Acceder mediante cuenta Google.
- Gestionar usuarios (administrador).
- Crear y gestionar proyecto.
- Buscar ubicación y registrar coordenadas.
- Subir y analizar plano.
- Configurar características de obra y calcular materiales.
- Consultar o descargar reporte PDF.
- Registrar entrada o salida de inventario (administrador).

### 3.1.8 Diagrama de clases general

**[Insertar diagrama de clases con User, Proyecto, Plano, Material, CalculoMaterial y MovimientoMaterial]**

### 3.1.9 Diseño de base de datos general

| Entidad | Descripción |
|---|---|
| users | Usuarios, rol, datos de Google y avatar. |
| proyectos | Proyecto, cliente, ubicación, fechas, dimensiones de muros y ladrillo. |
| planos | Archivo, tipo de plano, datos procesados y configuración estructural. |
| materials | Catálogo, unidad, stock, stock mínimo, precio y descripción. |
| calculo_materials | Resultado de cantidades por proyecto, plano y material. |
| movimientos_materiales | Entrada o salida, cantidad, motivo, material y usuario responsable. |

Relaciones principales: un usuario posee muchos proyectos; un proyecto posee muchos planos; un plano genera muchos cálculos; un material participa en muchos cálculos y movimientos.

### 3.1.10 Arquitectura del sistema

```text
Usuario
   │ navegador web
   ▼
Vistas Blade + Bootstrap + JavaScript
   ▼
Rutas web y middleware de autenticación/rol
   ▼
Controladores Laravel
   ├── Modelos Eloquent ──► MySQL
   ├── DomPDF ──► Reporte PDF
   ├── Socialite ──► Google OAuth
   ├── Leaflet/Nominatim ──► Ubicación
   └── Python/OpenCV ──► Análisis de plano
```

## 3.2 Aplicación de la metodología ágil Scrum

### Sprint 1: Seguridad y usuarios

Se implementaron inicio de sesión, cierre de sesión, roles, gestión de usuarios, acceso con Google, avatar y recuperación de contraseña.

### Sprint 2: Proyectos y planos

Se implementó el registro de proyectos con cliente, fechas, ubicación, coordenadas, dimensiones de muro y formatos referenciales de ladrillo. También se implementó la carga y administración de planos.

### Sprint 3: Análisis y cálculo de materiales

Se integró el procesamiento de planos mediante Python/OpenCV. El usuario configura plantas, cimentación, columnas, vigas, cubierta y vanos; con ello se estiman ladrillos, cemento, arena, grava, piedra, fierro, alambre, madera, clavos y agua.

### Sprint 4: Reportes e inventario

Se desarrollaron reportes en pantalla y PDF, incluyendo ubicación y coordenadas. Se añadió control de inventario con precio unitario, stock mínimo y movimientos de entrada/salida.

## 3.6 Pruebas

Se ejecutaron pruebas unitarias y funcionales. La última verificación registró 8 pruebas aprobadas y 12 validaciones, incluyendo estados del proyecto, redirección de invitados, permisos de reportes y filtro de cálculos por proyecto.

Pruebas manuales recomendadas para anexar con capturas:

1. Inicio de sesión con correo y contraseña.
2. Inicio de sesión con Google.
3. Creación de un proyecto y selección de ladrillo.
4. Geolocalización de un proyecto en el mapa.
5. Carga y análisis de un plano real.
6. Configuración y cálculo de materiales.
7. Visualización y descarga del PDF.
8. Registro de entrada y salida de inventario.

---

# CONCLUSIONES

1. Se desarrolló un sistema web que centraliza la gestión de proyectos, planos, cálculos, reportes e inventario de la Constructora ADASHA.
2. La integración de procesamiento de planos mediante visión artificial permite apoyar la obtención de información geométrica para una estimación inicial.
3. La gestión de roles y la verificación de propiedad protegen proyectos, cálculos y reportes de accesos no autorizados.
4. El inventario registra movimientos y mejora el control de materiales disponibles.
5. Los resultados de materiales constituyen estimaciones de obra gruesa; antes de ejecutar una obra deben ser revisados y aprobados por profesionales competentes.

# RECOMENDACIONES

- Configurar un servidor SMTP real antes de publicar el sistema.
- Desactivar el modo de depuración y utilizar HTTPS en producción.
- Realizar copias de seguridad periódicas de MySQL y de los planos cargados.
- Mantener actualizadas las dependencias de Laravel, PHP y Python.
- Validar con un profesional la escala del plano y las cantidades estimadas antes de adquirir materiales.

# BIBLIOGRAFÍA

- Laravel. (2026). *Laravel documentation*. https://laravel.com/docs
- Laravel. (2026). *Laravel Socialite*. https://laravel.com/docs/socialite
- OpenCV. (2026). *OpenCV documentation*. https://docs.opencv.org/
- Leaflet. (2026). *Leaflet reference*. https://leafletjs.com/reference.html
- PHP Documentation Group. (2026). *PHP manual*. https://www.php.net/manual/es/
- Oracle. (2026). *MySQL documentation*. https://dev.mysql.com/doc/

# ANEXOS PROPUESTOS

- Capturas de las pantallas principales del sistema.
- Diagrama de casos de uso.
- Diagrama de clases.
- Diagrama entidad–relación de la base de datos.
- Resultados de pruebas PHPUnit.
- Ejemplo de reporte PDF de materiales.
