<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Créditos Vencidos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 12px;
            margin: 20px;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin: 10px 0 35px 0;
            border-bottom: 3px solid #dc3545;
            padding: 15px 0 25px 0;
        }

        .header h1 {
            color: #dc3545;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .header .date {
            color: #495057;
            font-size: 12px;
            font-weight: 500;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin: 20px 5px 30px 5px;
            background-color: #f8f9fa;
            padding: 20px 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-item .label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #dc3545;
        }

        .table-container {
            margin: 20px 5px 30px 5px;
            padding: 0;
        }

        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin: 15px 0 12px 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 25px 0;
            font-size: 11px;
        }

        th,
        td {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            line-height: 1.3;
        }

        th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding: 20px 10px 15px 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            color: #6c757d;
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" style="max-width: 100%; overflow-x: hidden;">
        <div class="header">
            <h1>📊 REPORTE DE CRÉDITOS VENCIDOS</h1>
            <div class="subtitle">Análisis de créditos con fecha de vencimiento cumplida</div>
            <div class="date">Generado el: {{ $fechaReporte }}</div>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="label">Total Créditos Vencidos</div>
                <div class="value">{{ number_format($totalCreditos) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Monto Total Vencido</div>
                <div class="value">${{ number_format($montoTotalVencido, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Saldo Pendiente</div>
                <div class="value">${{ number_format($saldoTotalPendiente, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Promedio por Crédito</div>
                <div class="value">${{ number_format($promedioVencimiento, 2) }}</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-title">📋 Detalle de Créditos Vencidos</div>

            @if ($creditosVencidos->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">Código</th>
                            <th style="width: 20%;">Cliente</th>
                            <th style="width: 12%;">Monto Total</th>
                            <th style="width: 12%;">Pagado</th>
                            <th style="width: 12%;">Saldo</th>
                            <th style="width: 12%;">Fecha Venc.</th>
                            <th style="width: 10%;">Días Venc.</th>
                            <th style="width: 14%;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($creditosVencidos as $credito)
                            <tr>
                                <td class="text-center">{{ $credito->codigo }}</td>
                                <td>{{ $credito->cliente->nombre ?? 'N/D' }}</td>
                                <td class="text-right">${{ number_format($credito->monto_total, 2) }}</td>
                                <td class="text-right">${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</td>
                                <td class="text-right text-danger">${{ number_format($credito->saldo_pendiente, 2) }}
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($credito->fecha_vencimiento_ext ?? $credito->fecha_vencimiento)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    @if ($credito->dias_vencido >= 30)
                                        <span class="badge badge-danger">{{ $credito->dias_vencido }} días</span>
                                    @elseif($credito->dias_vencido >= 15)
                                        <span class="badge badge-warning">{{ $credito->dias_vencido }} días</span>
                                    @else
                                        <span class="text-danger">{{ $credito->dias_vencido }} días</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger">{{ strtoupper($credito->estado) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($creditosVencidos->count() > 15)
                    <div class="page-break"></div>

                    <div class="table-container">
                        <div class="table-title">📈 Análisis por Días de Vencimiento</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Rango de Días</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-right">Monto Total</th>
                                    <th class="text-right">% del Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rangos = [
                                        '1-15 días' => $creditosVencidos->filter(
                                            fn($c) => $c->dias_vencido >= 1 && $c->dias_vencido <= 15,
                                        ),
                                        '16-30 días' => $creditosVencidos->filter(
                                            fn($c) => $c->dias_vencido >= 16 && $c->dias_vencido <= 30,
                                        ),
                                        '31-60 días' => $creditosVencidos->filter(
                                            fn($c) => $c->dias_vencido >= 31 && $c->dias_vencido <= 60,
                                        ),
                                        'Más de 60 días' => $creditosVencidos->filter(fn($c) => $c->dias_vencido > 60),
                                    ];
                                @endphp
                                @foreach ($rangos as $rango => $creditos)
                                    <tr>
                                        <td>{{ $rango }}</td>
                                        <td class="text-center">{{ $creditos->count() }}</td>
                                        <td class="text-right">
                                            ${{ number_format($creditos->sum('saldo_pendiente'), 2) }}
                                        </td>
                                        <td class="text-right">
                                            {{ $saldoTotalPendiente > 0 ? number_format(($creditos->sum('saldo_pendiente') / $saldoTotalPendiente) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="no-data">
                    🎉 ¡Excelente! No hay créditos vencidos en este momento.
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>📄 Reporte Automático</strong> | Sistema de Gestión de Créditos</p>
            <p>Este reporte se generó automáticamente el {{ $fechaReporte }}</p>
            <p><em>⚠️ Los créditos vencidos requieren atención inmediata para evitar pérdidas</em></p>
        </div>
    </div>
</body>

</html>
