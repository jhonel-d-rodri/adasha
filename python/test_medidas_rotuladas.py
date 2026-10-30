import unittest
from medidas_rotuladas import extraer_medidas_rotuladas


class MedidasRotuladasTest(unittest.TestCase):
    def test_rotulo_ejemplo(self):
        datos = extraer_medidas_rotuladas('Longitud por planta: 46 m - Plantas: 2 - Total: 92m\nVanos de ambas plantas: 26,85 m?')
        self.assertEqual(datos['longitud_por_planta'], 46)
        self.assertEqual(datos['plantas'], 2)
        self.assertEqual(datos['area_vanos'], 26.85)

    def test_no_inventa_longitud_a_partir_de_cotas_o_total(self):
        self.assertEqual(extraer_medidas_rotuladas('8,00 m 6,00 m Total: 92 m'), {})

    def test_rechaza_conflictos_unidades_y_ceros(self):
        for texto in ['Longitud por planta: 46 m\nLongitud por planta: 48 m',
                      'Longitud por planta: 46 cm', 'Longitud por planta: 46 m²',
                      'Longitud por planta: 0 m', '']:
            self.assertEqual(extraer_medidas_rotuladas(texto), {})

    def test_decimal_y_saltos_de_linea(self):
        self.assertEqual(extraer_medidas_rotuladas('LONGITUD DE MUROS POR PLANTA:\n46,50 m')['longitud_por_planta'], 46.5)


if __name__ == '__main__':
    unittest.main()
