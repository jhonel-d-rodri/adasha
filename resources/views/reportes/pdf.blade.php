<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Materiales - {{ $proyecto->nombre ?? 'Proyecto' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #222;
            background: #f5f5f5;
            padding: 24px;
        }

        .material-name {
            font-size: 13px;
            font-weight: 700;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px 26px 18px;
            border: 1px solid #e6e6e6;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #d9534f;
            padding-bottom: 16px;
            margin-bottom: 22px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-placeholder {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d9534f, #b52b2b);
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-title h1 {
            margin: 0;
            color: #d9534f;
            font-size: 23px;
            letter-spacing: 0.8px;
            font-weight: 700;
        }

        .header-title h3 {
            margin: 4px 0 0;
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }

        .header-right {
            text-align: right;
            font-size: 11px;
            color: #666;
            line-height: 1.8;
        }

        .header-right .fecha {
            color: #222;
            font-weight: 700;
        }

        .info {
            background: #f8f9fa;
            border: 1px solid #edf1f3;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px 24px;
        }

        .info-item {
            flex: 1 1 180px;
            min-width: 150px;
        }

        .info-item strong {
            display: block;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 4px;
        }

        .info-item span {
            font-size: 13px;
            font-weight: 600;
            color: #1f1f1f;
        }

        .section-title {
            margin: 0 0 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            color: #444;
            font-size: 14px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 12px;
        }

        thead th {
            background: #2f2f2f;
            color: #fff;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border: 1px solid #2f2f2f;
            text-align: left;
        }

        tbody td {
            padding: 10px 12px;
            border: 1px solid #e6e6e6;
            vertical-align: middle;
            background: #fff;
        }

        tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .cantidad {
            font-weight: 700;
            color: #d9534f;
        }

        .total {
            margin-top: 18px;
            padding: 12px 14px;
            background: #f3f4f6;
            border-radius: 7px;
            border: 1px solid #ececec;
            display: flex;
            justify-content: flex-end;
            font-size: 12px;
            font-weight: 700;
            color: #333;
        }

        .total span {
            margin-left: 8px;
            color: #d9534f;
        }

        .footer {
            margin-top: 26px;
            padding-top: 12px;
            border-top: 2px solid #ededed;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #777;
        }

        .footer .pagina {
            font-weight: 700;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <div class="logo-placeholder">A</div>
                <div class="header-title">
                    <h1>CONSTRUCTORA ADASHA</h1>
                    <h3>Reporte de materiales</h3>
                </div>
            </div>
            <div class="header-right">
                <div>Fecha: <span class="fecha">{{ now()->format('d/m/Y H:i') }}</span></div>
                <div>Reporte #{{ $plano->id }}</div>
            </div>
        </div>

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
            @if($proyecto && $proyecto->latitud !== null && $proyecto->longitud !== null)
            <div class="info-item">
                <strong>Coordenadas</strong>
                <span>{{ number_format($proyecto->latitud, 7, '.', '') }}, {{ number_format($proyecto->longitud, 7, '.', '') }}</span>
            </div>
            <div class="info-item">
                <strong>Mapa</strong>
                <span>https://www.google.com/maps?q={{ $proyecto->latitud }},{{ $proyecto->longitud }}</span>
            </div>
            @endif
        </div>

        <h4 class="section-title">Materiales calculados</h4>

        <table>
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="40%">Material</th>
                    <th width="25%" class="text-right">Cantidad</th>
                    <th width="20%">Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calculos as $index => $calculo)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="material-name">{{ $calculo->material->nombre ?? 'N/A' }}</td>
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

        <div class="total">
            Total de materiales: <span>{{ $calculos->count() }}</span>
        </div>

        <div class="footer">
            <div>Reporte generado automáticamente por el sistema.</div>
            <div class="pagina">Página 1 de 1</div>
        </div>
    </div>
</body>
</html>
