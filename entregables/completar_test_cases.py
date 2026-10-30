from copy import deepcopy
from pathlib import Path
import zipfile
import xml.etree.ElementTree as ET

SOURCE = Path(r'C:\Users\jhonl\Downloads\Test cases Inicio de Sesion-Gestion de Usuarios.docx')
TARGET = Path(__file__).parent / 'Test cases Inicio de Sesion-Gestion de Usuarios ADASHA.docx'
W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
NS = {'w': W}
ET.register_namespace('w', W)

# title, priority, precondition, description, steps, expected result
cases = [
('Verificar inicio de sesión con contraseña incorrecta', 'Alta', 'Cuenta usuario@example.test registrada con contraseña ClavePrueba123!; sin sesión iniciada.', 'Ingresar el correo registrado con contraseña incorrecta.', ['Abrir /login.', 'Introducir usuario@example.test.', 'Introducir Incorrecta123! como contraseña.', 'Presionar Iniciar sesión.'], 'Se rechaza el acceso y se muestra: El correo electrónico o la contraseña son incorrectos.'),
('Verificar inicio de sesión con correo no registrado', 'Alta', 'Sin sesión iniciada; noexiste@example.test no está registrado.', 'Usar un correo de formato válido no registrado y la contraseña de una cuenta existente.', ['Abrir /login.', 'Introducir noexiste@example.test.', 'Introducir ClavePrueba123!.', 'Presionar Iniciar sesión.'], 'Se rechaza el acceso con el mensaje: El correo electrónico o la contraseña son incorrectos. No corresponde un error de formato de correo.'),
('Verificar inicio de sesión con credenciales incorrectas', 'Alta', 'Sin sesión iniciada; desconocido@example.test no está registrado.', 'Introducir un correo inexistente y una contraseña incorrecta.', ['Abrir /login.', 'Introducir desconocido@example.test.', 'Introducir Incorrecta123!.', 'Presionar Iniciar sesión.'], 'El visitante permanece sin autenticar y se muestra: El correo electrónico o la contraseña son incorrectos.'),
('Verificar inicio de sesión con credenciales correctas', 'Crítica', 'Cuenta usuario@example.test registrada con rol Usuario y contraseña ClavePrueba123!; sin sesión iniciada.', 'Ingresar con las credenciales correctas de la cuenta de prueba.', ['Abrir /login.', 'Introducir usuario@example.test.', 'Introducir ClavePrueba123!.', 'Presionar Iniciar sesión.'], 'El sistema autentica la cuenta, redirige a /dashboard y muestra: Bienvenido al sistema.'),
('Verificar registro sin correo electrónico', 'Alta', 'Administrador autenticado.', 'Dejar vacío el correo y completar los demás campos.', ['Abrir /usuarios y presionar Nuevo Usuario.', 'Introducir María López como nombre.', 'Dejar vacío el correo.', 'Introducir ClavePrueba123! en contraseña y confirmación.', 'Seleccionar Usuario y presionar Guardar Usuario.'], 'El formulario solicita completar el correo y no registra la cuenta. El texto de la validación del navegador puede variar según su idioma.'),
('Verificar registro sin contraseña', 'Alta', 'Administrador autenticado; maria@example.test no registrado.', 'Dejar vacía la contraseña y completar los demás campos.', ['Abrir /usuarios/create.', 'Introducir María López y maria@example.test.', 'Dejar vacía la contraseña.', 'Introducir ClavePrueba123! en confirmación y seleccionar Usuario.', 'Presionar Guardar Usuario.'], 'El formulario solicita completar la contraseña y no registra la cuenta.'),
('Verificar registro sin confirmación de contraseña', 'Alta', 'Administrador autenticado; maria@example.test no registrado.', 'Dejar vacía la confirmación y completar los demás campos.', ['Abrir /usuarios/create.', 'Introducir María López y maria@example.test.', 'Introducir ClavePrueba123! como contraseña.', 'Dejar vacía la confirmación y seleccionar Usuario.', 'Presionar Guardar Usuario.'], 'El formulario solicita completar la confirmación y no registra la cuenta.'),
('Verificar registro con todos los campos vacíos', 'Alta', 'Administrador autenticado.', 'Intentar registrar sin completar el formulario.', ['Abrir /usuarios/create.', 'Dejar vacíos nombre, correo, contraseña y confirmación.', 'Mantener Seleccione un rol.', 'Presionar Guardar Usuario.'], 'El navegador señala el primer campo obligatorio pendiente e impide enviar el formulario. No se crea ninguna cuenta.'),
('Verificar registro con contraseñas diferentes', 'Alta', 'Administrador autenticado; maria@example.test no registrado.', 'Introducir una confirmación diferente de la contraseña.', ['Abrir /usuarios/create.', 'Introducir María López y maria@example.test.', 'Introducir ClavePrueba123! como contraseña.', 'Introducir OtraClave123! como confirmación.', 'Seleccionar Usuario y presionar Guardar Usuario.'], 'El sistema muestra un error de validación por contraseñas diferentes y no registra la cuenta.'),
('Verificar registro con datos correctos', 'Crítica', 'Administrador autenticado; maria@example.test no registrado.', 'Registrar una cuenta con todos los datos válidos.', ['Abrir /usuarios/create.', 'Introducir María López y maria@example.test.', 'Introducir ClavePrueba123! en contraseña y confirmación.', 'Seleccionar Usuario.', 'Presionar Guardar Usuario y revisar el listado.'], 'Se registra la cuenta, se redirige a /usuarios y se muestra: Usuario registrado correctamente. La cuenta aparece con rol Usuario.'),
('Verificar registro con correo duplicado', 'Alta', 'Administrador autenticado; maria@example.test ya registrado.', 'Intentar registrar otra cuenta con un correo existente.', ['Abrir /usuarios/create.', 'Introducir Carlos Mendoza y maria@example.test.', 'Introducir ClavePrueba123! en contraseña y confirmación.', 'Seleccionar Usuario y presionar Guardar Usuario.'], 'Se muestra un error de validación del correo y no se crea otra cuenta con la misma dirección.'),
('Verificar edición sin cambiar contraseña', 'Alta', 'Administrador autenticado; cuenta de prueba disponible; maria.actualizada@example.test no registrado.', 'Modificar nombre y correo conservando la contraseña anterior.', ['Abrir /usuarios y presionar Editar en la cuenta de prueba.', 'Cambiar el nombre a María López Pérez y el correo a maria.actualizada@example.test.', 'Conservar el rol Usuario y dejar vacíos los campos de nueva contraseña.', 'Guardar y comprobar los datos en el listado.', 'Cerrar sesión e ingresar con el nuevo correo y la contraseña anterior.'], 'Se muestra: Usuario actualizado correctamente. Los datos cambian y el acceso funciona con el correo actualizado y la contraseña anterior.'),
('Verificar eliminación de otro usuario', 'Alta', 'Administrador autenticado; cuenta desechable distinta de la propia, sin proyectos ni otros datos asociados.', 'Eliminar una cuenta de prueba desde el listado.', ['Abrir /usuarios.', 'Localizar la cuenta desechable y presionar Eliminar.', 'Aceptar la confirmación.', 'Revisar el listado actualizado.'], 'Se muestra: Usuario eliminado correctamente. La cuenta deja de aparecer en el listado.'),
('Verificar restricción de eliminación de la propia cuenta', 'Crítica', 'Administrador autenticado.', 'Intentar eliminar la cuenta con la que se realiza la prueba.', ['Abrir /usuarios.', 'Localizar la cuenta del administrador autenticado.', 'Presionar Eliminar en su propia fila.', 'Aceptar la confirmación y revisar el resultado.'], 'Se muestra: No puedes eliminar tu propio usuario. La cuenta se conserva y la sesión continúa.'),
('Verificar restricción de acceso con rol Usuario', 'Crítica', 'Cuenta registrada con rol Usuario.', 'Intentar abrir directamente el módulo reservado al administrador.', ['Cerrar la sesión de administrador si existe.', 'Iniciar sesión con una cuenta de rol Usuario.', 'Escribir directamente la dirección base seguida de /usuarios.', 'Presionar Enter.'], 'El sistema devuelve HTTP 403, muestra No tienes permiso para acceder a esta sección. y no presenta el listado de usuarios.'),
]

def set_cell(cell, lines):
    proto = cell.find('w:p', NS)
    props = deepcopy(proto.find('w:pPr', NS)) if proto is not None and proto.find('w:pPr', NS) is not None else None
    for child in list(cell):
        if child.tag != f'{{{W}}}tcPr':
            cell.remove(child)
    for line in lines if isinstance(lines, list) else [lines]:
        p = ET.SubElement(cell, f'{{{W}}}p')
        if props is not None:
            p.append(deepcopy(props))
        r = ET.SubElement(p, f'{{{W}}}r')
        t = ET.SubElement(r, f'{{{W}}}t')
        t.text = line

def para(text, bold=False):
    p = ET.Element(f'{{{W}}}p')
    r = ET.SubElement(p, f'{{{W}}}r')
    if bold:
        ET.SubElement(ET.SubElement(r, f'{{{W}}}rPr'), f'{{{W}}}b')
    ET.SubElement(r, f'{{{W}}}t').text = text
    return p

with zipfile.ZipFile(SOURCE) as zin:
    root = ET.fromstring(zin.read('word/document.xml'))
    body = root.find('w:body', NS)
    tables = body.findall('w:tbl', NS)
    summary = deepcopy(tables[0])
    template = deepcopy(tables[1])
    sect = deepcopy(body.find('w:sectPr', NS))
    for child in list(body):
        body.remove(child)
    body.append(para('INFORME TEST CASES — SISTEMA ADASHA', True))
    body.append(para('QA: ________________________________________'))
    body.append(para('Estado: Pendiente de ejecución manual. Registrar el resultado después de comprobar cada caso.'))
    rows = summary.findall('w:tr', NS)
    row_proto = deepcopy(rows[1])
    for row in rows[1:]:
        summary.remove(row)
    for i, case in enumerate(cases, 1):
        row = deepcopy(row_proto)
        for cell, value in zip(row.findall('w:tc', NS), [f'TC-{i:03}', case[0], 'Pendiente']):
            set_cell(cell, value)
        summary.append(row)
    body.append(summary)
    body.append(para('Preparación del entorno', True))
    body.append(para('Sistema disponible en http://localhost/SistemaADASHA/public. Si utiliza php artisan serve, sustituya la dirección base por http://127.0.0.1:8000. Las rutas indicadas se agregan a esta dirección base.'))
    body.append(para('Crear previamente una cuenta ficticia usuario@example.test con rol Usuario y contraseña ClavePrueba123!. Utilizar una cuenta de administrador para las pruebas de gestión.'))
    body.append(para('Las contraseñas nuevas deben tener al menos 8 caracteres. El formulario actual indica Mínimo 6 caracteres, pero la validación del servidor exige 8; se debe corregir esa indicación visual.'))
    for i, case in enumerate(cases, 1):
        if i in (1, 5):
            body.append(para('TEST CASES DE INICIO DE SESIÓN' if i == 1 else 'TEST CASES DE GESTIÓN DE USUARIOS', True))
        table = deepcopy(template)
        cells = table.findall('w:tr/w:tc', NS)
        title, priority, pre, desc, steps, result = case
        for idx, val in {1:title, 2:f'TC-{i:03}', 4:priority, 6:pre, 8:desc, 10:[f'{j}. {s}' for j,s in enumerate(steps,1)], 12:result}.items():
            set_cell(cells[idx], val)
        body.append(table)
        body.append(para(''))
    if sect is not None:
        body.append(sect)
    payload = ET.tostring(root, encoding='utf-8', xml_declaration=True)
    with zipfile.ZipFile(TARGET, 'w', zipfile.ZIP_DEFLATED) as zout:
        for item in zin.infolist():
            zout.writestr(item, payload if item.filename == 'word/document.xml' else zin.read(item.filename))
with zipfile.ZipFile(TARGET) as check:
    assert check.testzip() is None
    document = ET.fromstring(check.read('word/document.xml'))
    assert len(document.findall('w:body/w:tbl', NS)) == 16
print(TARGET.resolve())
print('Verificado: 15 casos de prueba y tabla resumen; estructura DOCX válida.')
