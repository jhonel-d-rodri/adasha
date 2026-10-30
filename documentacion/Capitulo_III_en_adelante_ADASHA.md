# CAPÍTULO III: INGENIERÍA DEL PROYECTO

## 3.1 REQUERIMIENTOS GENERALES DEL SISTEMA

El sistema ADASHA se desarrolló para centralizar la gestión de proyectos de construcción, planos arquitectónicos, cálculo referencial de materiales de obra gruesa, reportes e inventario. La solución funciona como aplicación web y está dirigida a administradores y usuarios técnicos de la Constructora ADASHA.

### 3.1.1 Requerimientos funcionales generales

| Código | Requerimiento |
|---|---|
| RF-01 | Iniciar y cerrar sesión mediante credenciales locales. |
| RF-02 | Permitir autenticación con una cuenta de Google y recuperar contraseña. |
| RF-03 | Gestionar usuarios y roles de administrador o usuario. |
| RF-04 | Registrar, editar, visualizar y eliminar proyectos según permisos. |
| RF-05 | Registrar cliente, ubicación, coordenadas, fechas y dimensiones de construcción. |
| RF-06 | Cargar, listar, analizar y eliminar planos autorizados. |
| RF-07 | Estimar materiales de obra gruesa desde los datos del plano y del proyecto. |
| RF-08 | Visualizar y descargar reportes PDF de materiales. |
| RF-09 | Gestionar materiales, precios, stock mínimo y movimientos de inventario. |
| RF-10 | Restringir proyectos, planos, cálculos y reportes a su propietario o administrador. |

### 3.1.2 Requerimientos no funcionales generales

- Interfaz web responsiva elaborada con Blade, Bootstrap, CSS y JavaScript.
- Persistencia de datos en MySQL.
- Validación de formularios, archivos e información numérica.
- Autenticación, roles y protección de rutas mediante middleware Laravel.
- Zona horaria `America/La_Paz`.
- Cálculos expresados como estimaciones referenciales que requieren validación profesional antes de ejecutar una obra.

### 3.1.3 Identificación y descripción de los actores del sistema

| Actor | Descripción |
|---|---|
| Administrador | Gestiona usuarios, consulta todos los proyectos, administra materiales e inventario. |
| Usuario técnico | Registra y administra únicamente sus proyectos, planos, cálculos y reportes autorizados. |
| Profesional de construcción | Revisa las estimaciones y valida técnicamente los resultados antes de la ejecución. |
| Servicios externos | Google autentica cuentas; Leaflet/Nominatim apoya la ubicación; Python/OpenCV procesa planos. |

### 3.1.4 Diagrama de casos de uso general

> **AQUÍ VA LA IMAGEN 1:** Diagrama de casos de uso general del sistema ADASHA.

Casos principales: iniciar sesión, autenticarse con Google, recuperar contraseña, gestionar usuarios, gestionar proyectos, ubicar proyecto, cargar plano, analizar plano, calcular materiales, generar reporte, administrar materiales y registrar movimientos de inventario.

### 3.1.5 Diagrama de clases general

> **AQUÍ VA LA IMAGEN 2:** Diagrama de clases del sistema ADASHA. Archivo disponible: `diagrama-clases-adasha.svg`.

Las clases principales son `User`, `Proyecto`, `Plano`, `Material`, `CalculoMaterial` y `MovimientoMaterial`. Un usuario posee proyectos; un proyecto posee planos; un plano contiene cálculos; y un material participa en cálculos y movimientos de inventario.

### 3.1.6 Diseño de base de datos general

#### 3.1.6.1 Modelo relacional

```text
users 1 ─── N proyectos 1 ─── N planos 1 ─── N calculo_materials
                                    │                 │
                                    └─────────────────┘
materials 1 ─── N calculo_materials
materials 1 ─── N movimientos_materiales N ─── 1 users
```

#### 3.1.6.2 Nivel de normalización

La base de datos se organiza en tercera forma normal de manera referencial: cada tabla posee clave primaria, los datos de usuarios, proyectos, planos, materiales y movimientos se mantienen separados y las relaciones se establecen mediante claves foráneas. Esto evita duplicar datos de materiales o usuarios en cada cálculo.

> **AQUÍ VA LA IMAGEN 3:** Diagrama entidad–relación o modelo relacional de la base de datos.

#### 3.1.6.3 Estructura general de la base de datos

##### 3.1.6.3.1 Tablas de origen Laravel

| Tabla | Finalidad |
|---|---|
| users | Usuarios, credenciales, rol, Google ID y avatar. |
| password_reset_tokens | Tokens para recuperación de contraseña. |
| sessions | Sesiones, cuando se configura el controlador de sesiones en base de datos. |
| cache / jobs | Caché y trabajos en cola de Laravel. |

##### 3.1.6.3.2 Tablas de origen Spatie

No aplica. ADASHA no utiliza el paquete Spatie para permisos; implementa los roles `admin` y `usuario` con middleware y lógica propia de Laravel. Esta sección debe mantenerse para aclarar que no se crearon tablas `roles`, `permissions` ni tablas pivote de Spatie.

##### 3.1.6.3.3 Tablas creadas por el programador

| Tabla | Finalidad |
|---|---|
| proyectos | Datos del proyecto, cliente, ubicación, coordenadas, fechas y dimensiones. |
| planos | Archivos de planos, resultados de análisis y configuración constructiva. |
| materials | Catálogo de materiales, unidad, stock, precio y stock mínimo. |
| calculo_materials | Cantidades calculadas por proyecto, plano y material. |
| movimientos_materiales | Entradas y salidas de inventario, cantidad, motivo y responsable. |

### 3.1.7 Arquitectura del sistema

#### 3.1.7.1 Capa de presentación (vista)

Las vistas Blade presentan formularios, tablas, mapas, reportes e interfaces responsivas. Bootstrap y CSS propio aportan estilos; JavaScript gestiona interacciones, selección de dimensiones de ladrillo y mapa de ubicación.

#### 3.1.7.2 Capa de lógica del sistema (controlador)

Los controladores reciben solicitudes, validan datos, aplican permisos y coordinan operaciones. Entre los principales se encuentran `ProyectoController`, `PlanoController`, `CalculoMaterialController`, `ReporteController`, `MaterialController` y `GoogleAuthController`.

#### 3.1.7.3 Capa de acceso a datos (modelo)

Los modelos Eloquent representan las entidades y relaciones de MySQL: usuarios, proyectos, planos, materiales, cálculos y movimientos. Las migraciones definen las tablas y sus relaciones.

#### 3.1.7.4 Capa de servicios externos

- Google OAuth: acceso mediante cuenta Google.
- Nominatim y Leaflet: geocodificación y mapa de ubicación.
- Python/OpenCV: análisis asistido de imágenes de planos.
- DomPDF: generación de reportes descargables.

#### 3.1.7.5 Comunicación entre capas

El usuario interactúa con una vista. La solicitud llega a una ruta protegida por middleware; el controlador valida y consulta o actualiza modelos Eloquent; MySQL guarda los datos; finalmente el controlador devuelve una vista, redirección o PDF. Para el análisis de plano, el controlador ejecuta el servicio Python y conserva los resultados asociados al plano.

> **AQUÍ VA LA IMAGEN 4:** Diagrama de arquitectura del sistema ADASHA. Archivo disponible: `arquitectura-sistema-adasha.svg`.

## 3.2 APLICACIÓN DE LA METODOLOGÍA ÁGIL SCRUM

### 3.2.1 Roles en Scrum

El Product Owner representa las necesidades operativas de la Constructora ADASHA. El equipo de desarrollo implementa, prueba y documenta los incrementos. El tutor académico acompaña la revisión del proyecto. Los usuarios aportan retroalimentación funcional.

### 3.2.2 Planificación y gestión de sprints

#### 3.2.2.1 Product Backlog

El Product Backlog incluyó: autenticación, usuarios, proyectos, geolocalización, planos, visión artificial, cálculo de materiales, reportes, inventario y pruebas.

#### 3.2.2.2 Planificación de sprints

Cada sprint prioriza un incremento usable, define tareas técnicas, criterios de aceptación y pruebas. Al finalizar se revisa la funcionalidad y se ajusta el backlog.

### 3.2.3 Implementación por sprints

#### 3.2.3.1 Sprint 1: Iniciar sesión y gestión de usuarios

##### 3.2.3.1.1 Objetivo del sprint

Implementar acceso seguro al sistema y administración de usuarios según rol.

##### 3.2.3.1.2 Sprint Backlog: iniciar sesión y gestión de usuarios

- Formulario de inicio y cierre de sesión.
- Roles administrador y usuario.
- CRUD de usuarios para administrador.
- Inicio de sesión mediante Google.
- Recuperación de contraseña y avatar de Google.

##### 3.2.3.1.3 Diseño y arquitectura

Se utilizaron rutas protegidas, middleware de rol, modelo `User`, controladores de usuario y autenticación, además de Laravel Socialite para Google.

##### 3.2.3.1.4 Diseño e implementación de interfaces

> **AQUÍ VA LA IMAGEN 5:** Pantalla de inicio de sesión.
>
> **AQUÍ VA LA IMAGEN 6:** Pantalla de recuperación de contraseña o acceso con Google.
>
> **AQUÍ VA LA IMAGEN 7:** Pantalla de gestión de usuarios.

##### 3.2.3.1.5 Pruebas de testing

Verificar redirección de invitados al login, acceso permitido por rol y protección de rutas administrativas.

#### 3.2.3.2 Sprint 2: Gestión de proyectos, clientes y planos

##### 3.2.3.2.1 Objetivo del sprint

Centralizar la información de obras, clientes, ubicación y planos arquitectónicos.

##### 3.2.3.2.2 Sprint Backlog: gestión de proyectos, clientes y planos

- Crear, editar, visualizar y eliminar proyectos.
- Registrar fechas, coordenadas, dimensiones de muros y ladrillos.
- Buscar ubicación en el mapa.
- Cargar, listar y eliminar planos autorizados.
- Consultar historial de clientes.

##### 3.2.3.2.3 Diseño y arquitectura

Se aplicaron relaciones entre `User`, `Proyecto` y `Plano`, control de propiedad y almacenamiento público de archivos.

##### 3.2.3.2.4 Diseño e implementación de interfaces

> **AQUÍ VA LA IMAGEN 8:** Listado de proyectos.
>
> **AQUÍ VA LA IMAGEN 9:** Formulario para crear proyecto con dimensiones de muro y ladrillo.
>
> **AQUÍ VA LA IMAGEN 10:** Mapa de ubicación del proyecto.
>
> **AQUÍ VA LA IMAGEN 11:** Gestión o carga de planos.
>
> **AQUÍ VA LA IMAGEN 12:** Historial de clientes.

##### 3.2.3.2.5 Pruebas de testing

Comprobar validación de fechas, dimensiones, carga de archivos y restricción de acceso a proyectos ajenos.

#### 3.2.3.3 Sprint 3: Análisis de planos y cálculo de materiales

##### 3.2.3.3.1 Objetivo del sprint

Obtener una longitud referencial del plano y generar estimaciones de materiales de obra gruesa.

##### 3.2.3.3.2 Sprint Backlog: análisis y cálculo de materiales

- Ejecutar procesamiento de plano mediante Python/OpenCV.
- Registrar longitud total detectada.
- Configurar plantas, cubierta, cimentación, columnas, vigas y vanos.
- Calcular ladrillo, cemento, arena, grava, piedra, fierro, alambre, madera, clavos y agua.

##### 3.2.3.3.3 Diseño y arquitectura

`PlanoController` coordina la ejecución del script Python. `CalculoMaterialController` verifica permisos, valida datos y guarda los resultados en `calculo_materials`.

##### 3.2.3.3.4 Diseño e implementación de interfaces

> **AQUÍ VA LA IMAGEN 13:** Resultado del análisis de plano arquitectónico.
>
> **AQUÍ VA LA IMAGEN 14:** Configuración estructural para el cálculo de materiales.
>
> **AQUÍ VA LA IMAGEN 15:** Resultado del cálculo de materiales de obra gruesa.

##### 3.2.3.3.5 Pruebas de testing

Validar valores positivos, descuento de vanos, permisos sobre planos y filtro de cálculos por proyecto.

#### 3.2.3.4 Sprint 4: Reportes técnicos e inventario

##### 3.2.3.4.1 Objetivo del sprint

Presentar resultados de materiales, generar PDF y controlar las existencias de inventario.

##### 3.2.3.4.2 Sprint Backlog: reportes técnicos e inventario

- Visualizar reportes con información del proyecto y coordenadas.
- Descargar PDF de materiales.
- Registrar materiales, precio unitario y stock mínimo.
- Registrar entradas y salidas de inventario sin permitir stock negativo.

##### 3.2.3.4.3 Diseño y arquitectura

`ReporteController` protege la consulta según propietario del plano. `MaterialController` registra movimientos dentro de una transacción y actualiza el stock.

##### 3.2.3.4.4 Diseño e implementación de interfaces

> **AQUÍ VA LA IMAGEN 16:** Vista previa del reporte de materiales.
>
> **AQUÍ VA LA IMAGEN 17:** Reporte PDF descargado.
>
> **AQUÍ VA LA IMAGEN 18:** Catálogo de materiales e inventario.
>
> **AQUÍ VA LA IMAGEN 19:** Registro de entrada o salida de material.

##### 3.2.3.4.5 Pruebas de testing

Las pruebas automatizadas validan estados de proyecto, acceso de invitados, protección de reportes y filtro de cálculos. La verificación más reciente registró 8 pruebas aprobadas y 12 validaciones.

# CAPÍTULO IV: CONCLUSIONES Y RECOMENDACIONES

## 4.1 Conclusiones

1. Se desarrolló un sistema web que integra proyectos, planos, estimación de materiales, reportes e inventario.
2. El análisis asistido por visión artificial permite obtener información de apoyo desde planos arquitectónicos.
3. El control de roles y propiedad protege la información de cada usuario.
4. El inventario permite registrar existencias, precios, mínimos y movimientos.
5. Las estimaciones requieren validación profesional antes de emplearse en una obra real.

## 4.2 Recomendaciones

- Configurar SMTP, HTTPS y `APP_DEBUG=false` antes de publicar el sistema.
- Realizar copias de seguridad de MySQL y archivos de planos.
- Registrar capturas reales para los anexos y diagramas UML.
- Validar escala, calidad del plano y resultados con un profesional de construcción.

# BIBLIOGRAFÍA

- Laravel. *Laravel Documentation*. https://laravel.com/docs
- OpenCV. *OpenCV Documentation*. https://docs.opencv.org/
- Leaflet. *Leaflet Reference*. https://leafletjs.com/reference.html
- PHP Documentation Group. *PHP Manual*. https://www.php.net/manual/es/
- Oracle. *MySQL Documentation*. https://dev.mysql.com/doc/

# ANEXOS

- Anexo A. Capturas del sistema ADASHA.
- Anexo B. Diagrama de casos de uso.
- Anexo C. Diagrama de clases.
- Anexo D. Diagrama entidad–relación.
- Anexo E. Resultados de PHPUnit.
- Anexo F. Ejemplo de reporte PDF.
