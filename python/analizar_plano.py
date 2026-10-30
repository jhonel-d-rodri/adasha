import cv2
import json
import os
import re
import sys
from medidas_rotuladas import extraer_medidas_rotuladas

try:
    import pytesseract
except ImportError:
    pytesseract = None

# Verificar que se reciba la ruta
if len(sys.argv) < 2:
    print(json.dumps({
        "estado": "error",
        "mensaje": "No se recibió la ruta del plano."
    }))
    sys.exit()

# Ruta del plano
ruta = sys.argv[1]

# Leer imagen
imagen = cv2.imread(ruta)

if imagen is None:
    print(json.dumps({
        "estado": "error",
        "mensaje": "No se pudo abrir la imagen."
    }))
    sys.exit()

# OCR es opcional: si no esta disponible, el analisis geometrico anterior
# continua funcionando sin cambiar su resultado.
texto_ocr = ""
cotas_detectadas = []
if pytesseract is not None:
    ejecutable_ocr = r"C:\Program Files\Tesseract-OCR\tesseract.exe"
    if os.path.isfile(ejecutable_ocr):
        pytesseract.pytesseract.tesseract_cmd = ejecutable_ocr

    gris_ocr = cv2.cvtColor(imagen, cv2.COLOR_BGR2GRAY)
    try:
        texto_ocr = pytesseract.image_to_string(
            gris_ocr,
            config="--psm 11",
            lang="eng",
        ).strip()
    except (pytesseract.TesseractNotFoundError, pytesseract.TesseractError):
        texto_ocr = ""

    for valor, unidad in re.findall(
        r"(?<!\d)(\d+(?:[.,]\d+)?)\s*(m|cm)?\b",
        texto_ocr,
        flags=re.IGNORECASE,
    ):
        numero = float(valor.replace(",", "."))
        if 0.10 <= numero <= 100:
            cotas_detectadas.append({
                "valor": round(numero, 2),
                "unidad": (unidad or "sin unidad").lower(),
            })

# Dimensiones
alto, ancho = imagen.shape[:2]

# Escala de grises
gris = cv2.cvtColor(imagen, cv2.COLOR_BGR2GRAY)

# Suavizado
gris = cv2.GaussianBlur(gris, (5, 5), 0)

# Umbral
_, umbral = cv2.threshold(
    gris,
    180,
    255,
    cv2.THRESH_BINARY_INV
)

# Eliminar ruido
kernel = cv2.getStructuringElement(
    cv2.MORPH_RECT,
    (3, 3)
)

umbral = cv2.morphologyEx(
    umbral,
    cv2.MORPH_CLOSE,
    kernel,
    iterations=2
)

# Detectar contornos
contornos, _ = cv2.findContours(
    umbral,
    cv2.RETR_EXTERNAL,
    cv2.CHAIN_APPROX_SIMPLE
)

cantidad_muros = 0
longitud_pixeles = 0

# Analizar contornos
for contorno in contornos:

    area = cv2.contourArea(contorno)

    if area < 500:
        continue

    cantidad_muros += 1

    x, y, w, h = cv2.boundingRect(contorno)

    # Dibujar detección
    cv2.rectangle(
        imagen,
        (x, y),
        (x + w, y + h),
        (0, 255, 0),
        2
    )

    # Longitud aproximada
    longitud_pixeles += max(w, h)

# Guardar imagen analizada
ruta_salida = os.path.splitext(ruta)[0] + "_analizado.png"

cv2.imwrite(
    ruta_salida,
    imagen
)

# Las sugerencias proceden del rótulo, no de una medición geométrica.
medidas_sugeridas = extraer_medidas_rotuladas(texto_ocr)

# Crear JSON
resultado = {
    "estado": "ok",
    "ancho": ancho,
    "alto": alto,
    "muros": cantidad_muros,
    "longitud_pixeles": round(longitud_pixeles, 2),
    "longitud_total": None,
    "medidas_sugeridas": medidas_sugeridas,
    "imagen_analizada": ruta_salida,
    "texto_ocr": texto_ocr,
    "cotas_detectadas": cotas_detectadas,
    "mensaje": (
        "Se leyó la longitud por planta indicada en el plano. Revise el dato propuesto antes de calcular."
        if medidas_sugeridas else
        "No se pudo leer una longitud por planta explícita. Se requiere una medida verificada para calcular."
    )
}

# Enviar JSON a Laravel
print(json.dumps(resultado))
