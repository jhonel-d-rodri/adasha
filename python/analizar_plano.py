import cv2
import json
import sys

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

# Conversión provisional
# 100 píxeles = 1 metro
longitud_metros = longitud_pixeles / 100

# Guardar imagen analizada
ruta_salida = ruta.replace(
    ".",
    "_analizado."
)

cv2.imwrite(
    ruta_salida,
    imagen
)

# Crear JSON
resultado = {
    "estado": "ok",
    "ancho": ancho,
    "alto": alto,
    "muros": cantidad_muros,
    "longitud_pixeles": round(longitud_pixeles, 2),
    "longitud_total": round(longitud_metros, 2),
    "imagen_analizada": ruta_salida,
    "mensaje": "Plano analizado correctamente."
}

# Enviar JSON a Laravel
print(json.dumps(resultado))