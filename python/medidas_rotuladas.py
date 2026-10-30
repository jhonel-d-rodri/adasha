"""Lee valores explícitamente rotulados; no deduce longitudes de cotas aisladas."""

import re
import unicodedata


def extraer_medidas_rotuladas(texto):
    texto = ''.join(c for c in unicodedata.normalize('NFKD', texto.lower()) if not unicodedata.combining(c))
    numero = r'(\d+(?:[.,]\d+)?)'
    metro = r'\s*m\b(?![ \t]*[²2?])'

    def unico(patron, minimo, maximo):
        valores = {float(v.replace(',', '.')) for v in re.findall(patron, texto)}
        if len(valores) != 1:
            return None
        valor = valores.pop()
        return valor if minimo <= valor <= maximo else None

    longitud = unico(r'\blongitud\s+(?:total\s+)?(?:de\s+muros\s+)?por\s+planta\s*[:=]\s*' + numero + metro, 0.01, 100000)
    if longitud is None:
        return {}

    datos = {'longitud_por_planta': longitud, 'origen': 'rotulo_ocr'}
    plantas = unico(r'(?:^|[\n·|;\-])\s*(?:cantidad\s+de\s+)?plantas\s*:\s*(\d+)\b', 1, 2)
    if plantas is not None:
        datos['plantas'] = int(plantas)
    vanos = unico(r'\bvanos\s+de\s+ambas\s+plantas\s*:\s*' + numero + r'\s*m\s*[²2?]', 0, 100000)
    if vanos is not None and plantas == 2:
        datos['area_vanos'] = vanos
    return datos
