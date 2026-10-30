# Despliegue de ADASHA en Render (preparación)

## Estado

La configuración preliminar está en `Dockerfile`, `.dockerignore`, `docker/entrypoint.sh` y `render.yaml`. No se publicó ni desplegó. El listado de rutas de Laravel no produjo salida en esta revisión, por lo que se debe confirmar la ruta `/up` antes de usarla como health check. No se debe presentar una URL de servicio como activa hasta completar el despliegue y verificarla.

Repositorio configurado localmente: https://github.com/jhonel-d-rodri/adasha

## Pasos para publicar

1. Revisar y guardar los cambios locales intencionales; hay modificaciones pendientes previas a esta tarea. No hacer `git add .` sin revisar, porque podría incluir archivos que no se desean publicar.
2. La configuración actual es solo una demo con SQLite bajo `/tmp`; Render elimina ese archivo al reiniciar, desplegar o dormir el servicio. No cargar datos reales. Antes de ponerla al servicio de trabajadores, validar todas las migraciones sobre PostgreSQL de prueba y usar una base con persistencia y copias de seguridad.
3. Confirmar que `.env` está ignorado y que nunca se añade al repositorio. En GitHub, publicar los cambios revisados en la rama que Render leerá. La consulta al remoto falló por conectividad desde este entorno.
4. En Render, usar **New → Blueprint** y seleccionar el repositorio. `render.yaml` crea un Web Service Docker sin base de datos independiente. Configurar `APP_KEY` y `APP_URL` como variables de servicio; generar la clave localmente con `php artisan key:generate --show`, sin pegarla en el repositorio ni en este documento. `APP_URL` será el host `https://…onrender.com` asignado por Render.
5. Confirmar en el registro de compilación el build Docker, la migración y la ruta de salud `/up`. Abrir el host generado, comprobar login y flujos principales y guardar capturas del Dashboard (Settings/Environment con valores ocultos) y del deploy exitoso.
6. Antes de usar con datos reales, migrar SQLite si se requiere conservar datos y almacenar los archivos subidos en almacenamiento persistente externo. El plan gratuito no conserva disco local.

## Cambios preparados

- Imagen Docker PHP 8.3/Apache con extensiones PDO SQLite y PostgreSQL, Composer, compilación de assets Vite y `public/` como raíz web.
- `render.yaml` para crear un servicio de demostración con SQLite efímera; migraciones al iniciar el servicio.
- Variables sensibles marcadas para configurar en el Dashboard, nunca en Git.
- El árbol de trabajo ya contenía numerosos cambios de usuario. No se hizo commit/push y el remoto no respondió desde este entorno; por eso faltan URL pública, capturas reales y validación desde fuera.
- La interoperabilidad de todas las migraciones con PostgreSQL y la persistencia de planos/avatar aún deben implementarse y validarse antes del uso real. La SQLite en `/tmp` es solo para una demostración descartable.
- La imagen Docker no se construyó en este entorno; no está instalado Docker. Tampoco se pudo confirmar el listado de rutas de Laravel, así que `/up` sigue pendiente de comprobación local.

## Diferencias y respuestas

**Localhost y hosting.** En `localhost` el servidor, los archivos, la base y las credenciales residen en la computadora del desarrollador; normalmente solo esa máquina puede acceder. En hosting, el proceso corre en un servidor accesible por red, con DNS/HTTPS, variables de producción, persistencia y operación administradas por el proveedor. `localhost` en el navegador de cada trabajador apuntaría a su propio dispositivo, no al computador del programador.

**Por qué excluir `.env`.** Contiene contraseñas, tokens, claves de cifrado y configuración privada. GitHub puede ser público, y hasta un repositorio privado no es un almacén de secretos; el historial conserva valores y la filtración exige revocar/rotar credenciales. Se guardan en el gestor de variables del hosting y se mantiene `.env.example` sin valores reales.

**`APP_KEY`.** Clave criptográfica que Laravel usa para cifrar/descifrar datos protegidos, incluidos valores cifrados y cookies/sesiones según configuración. Debe ser secreta y estable en el entorno: regenerarla puede invalidar datos cifrados existentes.

**Problemas típicos de despliegue.** Diferencias de versión de PHP/extensiones, dependencias o build Vite ausentes, permisos de `storage`/`bootstrap/cache`, variables de entorno incorrectas, migraciones incompatibles con el motor destino, `APP_DEBUG` activo, HTTPS/proxy, callbacks OAuth y URLs, límites de memoria/tiempo, rutas sensibles a mayúsculas, filesystem efímero y tareas de cola/cron no ejecutándose.

**Límites del plan gratuito Render (consultado 30-10-2026).** La web duerme tras 15 minutos sin tráfico y tarda cerca de un minuto en despertar; su filesystem es efímero y no admite disco persistente ni escalado horizontal. Hay 750 horas de instancia gratis mensuales por workspace; al agotarlas se suspenden los servicios gratis. La Postgres gratis limita a 1 GB, expira a los 30 días, no ofrece backups y después del periodo de gracia puede borrarse. Adecuado para demostración/validación, no para producción con información de trabajadores.

**Migración a pago.** Elegir web service y PostgreSQL con disponibilidad, recursos, copias de seguridad y retención apropiados; añadir almacenamiento persistente u objeto para planos/avatar, configurar backups y monitorización, dominio propio/TLS, correo transaccional, colas/cron y escalado según carga. Pasar variables secretas a producción, fijar `APP_ENV=production`, `APP_DEBUG=false`, ajustar `APP_URL` y OAuth, ejecutar y verificar migraciones; planificar dump/import de SQLite con validación y rollback.

## Informe breve y defensa

Se eligió Render porque permite ejecutar PHP a través de una imagen Docker y ofrece una URL `onrender.com` sin comprar dominio. Docker explicita runtime, extensiones, compilación de Vite y raíz pública de Laravel. La configuración lista sirve únicamente como demo con datos descartables en SQLite efímera; deliberadamente no conecta una base gratuita que caduca. No hubo despliegue ni prueba pública: GitHub no fue accesible desde la red de trabajo y las modificaciones existentes no se publicaron a ciegas. Para trabajadores y datos del negocio será necesario pasar a una base y almacenamiento persistentes y validar las migraciones.

## Capturas

Pendientes de crear en el Dashboard real: servicio con Dockerfile `./Dockerfile`, rama desplegada, variables mostrando solo nombres (ocultar todos los valores), deploy exitoso y ruta `/up` con estado saludable. No se incluyen capturas simuladas.
