# Tablas de apoyo retiradas de ADASHA

Se retiraron de la base `adasha` las tablas vacías `sessions`, `jobs`,
`job_batches` y `failed_jobs` mediante la migración
`2026_10_13_000000_remove_unused_infrastructure_tables` (lote 10).

Se mantienen `migrations`, `password_reset_tokens`, `cache` y `cache_locks`.
`cache_locks` pertenece al almacenamiento de caché en base de datos y no se
elimina únicamente por estar vacía.

## Configuración actual

```dotenv
SESSION_DRIVER=file
CACHE_STORE=database
QUEUE_CONNECTION=sync
QUEUE_FAILED_DRIVER=null
```

Las sesiones se conservan en archivos. Las tareas que se despachen en el futuro
se ejecutarán en la misma solicitud mediante `sync`; no habrá cola persistente
ni registro de fallos en `failed_jobs`. El análisis Python actual sigue siendo
una ejecución directa. Se retiró el proceso `queue:listen` de `composer dev`.

## Restaurar las cuatro tablas

Desde la carpeta del proyecto, ejecutar:

```powershell
& 'C:\xampp\php\php.exe' artisan migrate:rollback --path=database/migrations/2026_10_13_000000_remove_unused_infrastructure_tables.php --batch=10
```

La ruta limita la reversión a esta migración y el lote identifica su ejecución
en esta instalación. No utilizar `migrate:fresh`, `migrate:reset` ni una reversión
general para recuperar estas tablas.

La reversión recrea las cuatro estructuras sin borrar las tablas que ya existan.
No modifica usuarios, proyectos, planos, materiales ni cálculos. La configuración
permanece en archivos/síncrona; restaurar estructuras no activa las colas.

Si posteriormente se decide usar sesiones o colas en base de datos, restaurar
primero las tablas y configurar los controladores correspondientes. Para usar
colas también será necesario ejecutar un worker y diseñar las tareas.

## Respaldo SQL de esta instalación

Antes de eliminar se guardó la estructura exacta de las cuatro tablas en:

```text
storage/app/private/db-backups/unused-infrastructure-20261013-160525-649887.sql
```

El respaldo está fuera de la carpeta pública y excluido del control de versiones.
Puede importarse desde phpMyAdmin seleccionando exclusivamente `adasha`, si las
cuatro tablas no existen. La opción preferida es la reversión de la migración,
porque también actualiza el historial de migraciones. Una importación SQL manual
solo restaura las estructuras y no actualiza ese historial.

Las cuatro tablas tenían cero registros; por eso el respaldo contiene únicamente
estructuras. No es un respaldo completo de ADASHA y no recupera datos eliminados
de otras tablas.

## Verificación realizada

- 24 pruebas aprobadas, con 100 aserciones, en SQLite en memoria.
- Se verificó la restauración y posterior retirada de las estructuras en pruebas.
- Se verificó que la migración rechaza tablas con registros y una cola activa
  configurada en base de datos.
- Tras aplicar la migración en MySQL se confirmó la ausencia de las cuatro tablas.
- Los registros de las tablas conservadas no cambiaron: 4 usuarios, 14 proyectos,
  10 planos, 13 materiales, 65 cálculos, 0 movimientos, 1 token y 10 entradas de caché.
- `migrations` pasó de 22 a 23 registros al registrar este cambio.

