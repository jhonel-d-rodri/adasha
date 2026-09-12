<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Materiales - {{ $proyecto->nombre ?? 'Proyecto' }}</title>
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
            background: #fff;
        }
        /* Contenedor principal */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        /* Encabezado */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #d9534f;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-placeholder {
            width: 60px;
            height: 60px;
            background: #d9534f;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-transform: uppercase;
        }
        .header-title h1 {
            font-size: 22px;
            font-weight: 700;
            color: #d9534f;
            margin: 0;
            letter-spacing: 1px;
        }
        .header-title h3 {
            font-size: 14px;
            font-weight: 400;
            color: #666;
            margin: 2px 0 0 0;
        }
        .header-right {
            text-align: right;
            font-size: 11px;
            color: #888;
        }
        .header-right .fecha {
            font-weight: 600;
            color: #333;
        }
        /* Información del proyecto */
        .info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px 30px;
        }
        .info-item {
            flex: 1 1 200px;
        }
        .info-item strong {
            color: #555;
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-item span {
            font-size: 14px;
            font-weight: 500;
            color: #222;
        }
        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }
        table thead th {
            background: #343a40;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border: 1px solid #343a40;
        }
        table tbody td {
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        table tbody tr:hover {
            background: #e9ecef;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .cantidad {
            font-weight: 600;
            color: #d9534f;
        }
        /* Total general */
        .total {
            margin-top: 20px;
            padding: 12px 15px;
            background: #e9ecef;
            border-radius: 6px;
            display: flex;
            justify-content: flex-end;
            font-weight: 600;
            font-size: 13px;
        }
        .total span {
            color: #d9534f;
            margin-left: 10px;
        }
        /* Pie de página */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            font-size: 10px;
            color: #999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer .pagina {
            font-weight: 600;
            color: #555;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .header-right {
                text-align: left;
                width: 100%;
            }
            .info {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-left">
                <div class="logo-placeholder">A</div>
                <div class="header-title">
                    <h1>CONSTRUCTORA ADASHA</h1>
                    <h3>Reporte de Materiales</h3>
                </div>
            </div>
            <div class="header-right">
                <div>Fecha: <span class="fecha">{{ now()->format('d/m/Y H:i') }}</span></div>
                <div>Reporte #{{ $plano->id }}</div>
            </div>
        </div>

        <!-- Información del proyecto -->
        <div class="info">
            <div class="info-item">
                <strong>Proyecto</strong>
                <span>{{ $proyecto->nombre ?? 'Sin proyecto' }}</span>
            </div>
            <div class="info-item">
                <strong>Cliente</strong>
                <span>{{ $proyecto->cliente ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <strong>Ubicación</strong>
                <span>{{ $proyecto->ubicacion ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <strong>Plano</strong>
                <span>{{ $plano->nombre }}</span>
            </div>
            @if($plano->longitud_total)
            <div class="info-item">
                <strong>Longitud total</strong>
                <span>{{ number_format($plano->longitud_total, 2, ',', '.') }} m</span>
            </div>
            @endif
        </div>

        <!-- Tabla de materiales -->
        <h4 style="margin: 20px 0 10px 0; font-size: 14px; color: #555; border-bottom: 1px solid #dee2e6; padding-bottom: 8px;">Materiales Calculados</h4>

        <table>
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="35%">Material</th>
                    <th width="25%" class="text-right">Cantidad</th>
                    <th width="20%">Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calculos as $index => $calculo)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $calculo->material->nombre ?? 'N/A' }}</td>
                    <td class="text-right cantidad">
                        @if(str_contains($calculo->material->nombre ?? '', 'Ladrillo') || str_contains($calculo->material->nombre ?? '', 'Cemento'))
                            {{ number_format($calculo->cantidad, 0, ',', '.') }}
                        @else
                            {{ number_format($calculo->cantidad, 2, ',', '.') }}
                        @endif
                    </td>
                    <td>{{ $calculo->material->unidad ?? 'unidad' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total de materiales (opcional) -->
        <div class="total">
            Total de materiales: <span>{{ $calculos->count() }}</span>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <div>Reporte generado automáticamente por el sistema</div>
            <div class="pagina">Página 1 de 1</div>
        </div>
    </div>
</body>
</html>