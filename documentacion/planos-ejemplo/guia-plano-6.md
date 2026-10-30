# Ejemplo adaptado al calculo actual de ADASHA

Imagen: plano-6-adaptado-con-vanos.png. Generada con image_gen; prompt en prompt-plano-6.txt.

## Revision realizada

Se revisaron el formulario de proyectos, el de configuracion de materiales, los modelos Proyecto y Plano, los controladores de planos, calculos y reportes, y el analizador Python. La revision se centra en el flujo de carga, analisis, parametros, formulas y visualizacion del calculo; no equivale a certificar todo el sistema ni el diseno constructivo.

El sistema admite PNG/JPG de hasta 5 MB. El analizador identifica contornos y hace OCR opcional, pero no vincula cotas con muros ni calcula la longitud real. Se debe pulsar Analizar y luego introducir los parametros en Configurar y calcular materiales.

## Parametros para copiar

En el proyecto: altura de muro 2.40 m; espesor 0.15 m; largo de ladrillo 0.24 m, ancho 0.12 m y alto 0.09 m. Los campos de nombre, cliente, ubicacion y fechas pueden contener los datos de su proyecto de prueba.

En la configuracion del plano:

| Campo | Valor |
|---|---:|
| Longitud total de muros | 46 m |
| Plantas | 2 |
| Tipo de cubierta | Losa |
| Area construida aplicada a cubierta | 48 m2 |
| Area de puertas y ventanas | 26.85 m2 |
| Cimentacion | Activada para probar la formula |
| Columnas | Activadas para probar la formula |
| Vigas | Activadas para probar la formula |

## Como se obtienen los valores

Las dos plantas repiten ejes de muros. Cada una tiene perimetro 2*(8+6)=28 m, dos divisiones longitudinales de 6 m y dos transversales de 3 m: 28+12+6=46 m. Los tramos incluyen el espacio ocupado por los vanos; no reste el ancho de puertas o ventanas a esa longitud, porque se descuentan por area. Total nominal entre ambas plantas: 92 m. Ingrese 46, no 92, pues el sistema multiplica por dos.

Los vanos se suman para AMBAS plantas:

| Tipo | Cantidad total | Ancho por alto | Area total |
|---|---:|---|---:|
| Entrada P1 | 1 | 0.90 x 2.10 m | 1.89 m2 |
| Puertas interiores P2 | 8 | 0.80 x 2.10 m | 13.44 m2 |
| Ventanas V1 | 8 | 1.20 x 1.20 m | 11.52 m2 |
| Total | | | 26.85 m2 |

Area bruta de muros: 46*2.40*2=220.80 m2. Area neta segun el sistema: 220.80-26.85=193.95 m2. Con el ladrillo especificado, junta de 1 cm y desperdicio de 10%, el programa debe guardar 8534 ladrillos. Es un resultado de su formula, no un computo constructivo certificado.

La planta nominal mide 48 m2 y ambos niveles 96 m2. En el campo usado para calcular UNA losa de cubierta se introduce 48, no 96.

## Alcance y limitaciones encontradas

- Las medidas anotadas definen este ejemplo; la imagen generada no debe medirse en pixeles como si fuera CAD exacto. Las longitudes son entre ejes y no corrigen intersecciones de muros.
- Las puertas y ventanas se usan para descontar mamposteria; el sistema no calcula carpinteria, vidrio ni herrajes.
- Cimentacion, columnas y vigas se calculan con dimensiones y coeficientes fijos del codigo. Activarlas ejercita esas formulas, no valida un diseno estructural del plano.
- La losa se calcula una sola vez. No incluye la losa de entrepiso ni el concreto y acero de la escalera. El plano no convierte el resultado en un presupuesto completo de dos pisos.
- Con cubierta losa, el sistema no genera madera ni clavos. Para probar esa rama puede seleccionar liviana conservando 48 m2: obtiene 1.44 m3 de madera y 4.32 kg de clavos segun la formula actual. Es un escenario alternativo, no una partida que se agrega a la cubierta de losa.
- Los reportes llaman Longitud total al valor almacenado por planta; en este caso mostraran 46 m, no los 92 m de ambas plantas.

## Verificacion

Prueba automatizada: tests/Feature/CorreccionesSistemaTest.php, test_ejemplo_dos_plantas_descuenta_vanos_y_calcula_ambas_cubiertas. Ejecutada con base de datos de pruebas: 14 comprobaciones aprobadas. Verifica peticiones al controlador, descuento global de vanos, cantidad de ladrillos, ocho materiales con losa, diez con cubierta liviana y acceso al reporte. No se modificaron los calculos de proyectos reales.
