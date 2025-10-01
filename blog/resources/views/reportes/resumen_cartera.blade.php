<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Cartera - Cierre de Caja</title>
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
            border-bottom: 3px solid #28a745;
            padding: 15px 0 25px 0;
        }

        .header h1 {
            color: #28a745;
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

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0 35px 0;
            padding: 0 5px;
        }

        .summary-card {
            background-color: #f8f9fa;
            padding: 18px 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 5px 0;
        }

        .summary-card.warning {
            border-left-color: #ffc107;
        }

        .summary-card.danger {
            border-left-color: #dc3545;
        }

        .summary-card.info {
            border-left-color: #17a2b8;
        }

        .card-title {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }

        .card-subtitle {
            font-size: 10px;
            color: #6c757d;
        }

        .overview-table {
            width: 100%;
            margin: 15px 0 35px 0;
        }

        .overview-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .overview-table th,
        .overview-table td {
            padding: 14px 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            line-height: 1.3;
        }

        .overview-table th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .overview-table td {
            font-size: 12px;
        }

        .table-container {
            margin: 20px 0 30px 0;
            padding: 0 5px;
        }

        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .clients-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .clients-table th,
        .clients-table td {
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .clients-table th {
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }

        .text-info {
            color: #17a2b8;
            font-weight: bold;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 5px 0;
        }

        .progress-fill {
            height: 100%;
            background-color: #28a745;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: bold;
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

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-paid {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" style="max-width: 100%; overflow-x: hidden;">
        <div class="header">
            <h1>💼 RESUMEN GENERAL DE CARTERA</h1>
            <div class="subtitle">Estado consolidado del portafolio de créditos - Tipo Cierre de Caja</div>
            <div class="date">Generado el: {{ $fechaReporte }}</div>
        </div>

        <!-- Resumen Ejecutivo -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-title">💰 Valor Total de Cartera</div>
                <div class="card-value text-success">${{ number_format($montoTotalCartera, 2) }}</div>
                <div class="card-subtitle">Monto total de todos los créditos otorgados</div>
            </div>

            <div class="summary-card info">
                <div class="card-title">💳 Total Pagado</div>
                <div class="card-value text-info">${{ number_format($montoTotalPagado, 2) }}</div>
                <div class="card-subtitle">{{ number_format($porcentajePagado, 1) }}% del total recuperado</div>
            </div>

            <div class="summary-card warning">
                <div class="card-title">⏳ Saldo Pendiente</div>
                <div class="card-value text-warning">${{ number_format($saldoPendienteTotal, 2) }}</div>
                <div class="card-subtitle">Monto por cobrar de créditos activos</div>
            </div>

            <div class="summary-card danger">
                <div class="card-title">⚠️ Créditos Vencidos</div>
                <div class="card-value text-danger">{{ number_format($creditosVencidos) }}</div>
                <div class="card-subtitle">Requieren atención inmediata</div>
            </div>
        </div>

        <!-- Barra de Progreso General -->
        <div class="table-container">
            <div class="table-title">📊 Nivel de Recuperación de Cartera</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $porcentajePagado }}%;">
                    {{ number_format($porcentajePagado, 1) }}% Recuperado
                </div>
            </div>
            <p style="font-size: 11px; color: #6c757d; margin-top: 5px;">
                <strong>${{ number_format($montoTotalPagado, 2) }}</strong> de
                <strong>${{ number_format($montoTotalCartera, 2) }}</strong>
                (Pendiente: <strong class="text-warning">${{ number_format($saldoPendienteTotal, 2) }}</strong>)
            </p>
        </div>

        <!-- Tabla de Estado General -->
        <div class="table-container">
            <div class="table-title">📋 Distribución de Créditos por Estado</div>
            <div class="overview-table">
                <table>
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Monto Total</th>
                            <th class="text-right">% del Portafolio</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>💚 Pagados</td>
                            <td class="text-center">{{ number_format($creditosPagados) }}</td>
                            <td class="text-right text-success">${{ number_format($montoPorEstado['pagados'], 2) }}
                            </td>
                            <td class="text-right">
                                {{ $montoTotalCartera > 0 ? number_format(($montoPorEstado['pagados'] / $montoTotalCartera) * 100, 1) : 0 }}%
                            </td>
                            <td class="text-center"><span class="status-badge status-paid">PAGADO</span></td>
                        </tr>
                        <tr>
                            <td>🟢 Activos</td>
                            <td class="text-center">
                                {{ number_format(App\Models\Credito::where('estado', 'activo')->count()) }}</td>
                            <td class="text-right text-success">${{ number_format($montoPorEstado['activos'], 2) }}
                            </td>
                            <td class="text-right">
                                {{ $montoTotalCartera > 0 ? number_format(($montoPorEstado['activos'] / $montoTotalCartera) * 100, 1) : 0 }}%
                            </td>
                            <td class="text-center"><span class="status-badge status-active">ACTIVO</span></td>
                        </tr>
                        <tr>
                            <td>🟡 Pendientes</td>
                            <td class="text-center">{{ number_format($creditosPendientes) }}</td>
                            <td class="text-right text-warning">${{ number_format($montoPorEstado['pendientes'], 2) }}
                            </td>
                            <td class="text-right">
                                {{ $montoTotalCartera > 0 ? number_format(($montoPorEstado['pendientes'] / $montoTotalCartera) * 100, 1) : 0 }}%
                            </td>
                            <td class="text-center"><span class="status-badge status-pending">PENDIENTE</span></td>
                        </tr>
                        <tr style="background-color: #fff5f5;">
                            <td>🔴 Vencidos</td>
                            <td class="text-center">{{ number_format($creditosVencidos) }}</td>
                            <td class="text-right text-danger">${{ number_format($montoPorEstado['vencidos'], 2) }}
                            </td>
                            <td class="text-right">
                                {{ $montoTotalCartera > 0 ? number_format(($montoPorEstado['vencidos'] / $montoTotalCartera) * 100, 1) : 0 }}%
                            </td>
                            <td class="text-center"><span class="status-badge status-overdue">VENCIDO</span></td>
                        </tr>
                    </tbody>
                    <tfoot style="border-top: 2px solid #28a745; font-weight: bold; background-color: #f8f9fa;">
                        <tr>
                            <td><strong>TOTAL GENERAL</strong></td>
                            <td class="text-center"><strong>{{ number_format($totalCreditos) }}</strong></td>
                            <td class="text-right"><strong>${{ number_format($montoTotalCartera, 2) }}</strong></td>
                            <td class="text-right"><strong>100.0%</strong></td>
                            <td class="text-center">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($clientesConDeuda->count() > 0)
            <div class="page-break"></div>

            <!-- Top Clientes con Mayor Deuda -->
            <div class="table-container">
                <div class="table-title">🏆 Top 10 - Clientes con Mayor Deuda Pendiente</div>
                <table class="clients-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Pos.</th>
                            <th style="width: 35%;">Cliente</th>
                            <th style="width: 20%;">Deuda Total</th>
                            <th style="width: 15%;">Créditos</th>
                            <th style="width: 22%;">% del Saldo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientesConDeuda as $index => $cliente)
                            <tr>
                                <td class="text-center">
                                    @if ($index + 1 <= 3)
                                        <span class="text-warning">🏅</span>
                                    @endif
                                    {{ $index + 1 }}
                                </td>
                                <td>{{ $cliente->cliente->nombre ?? 'N/D' }}</td>
                                <td class="text-right text-danger">${{ number_format($cliente->deuda_total, 2) }}</td>
                                <td class="text-center">
                                    {{ App\Models\Credito::where('cliente_id', $cliente->cliente_id)->where('estado', '!=', 'pagado')->count() }}
                                </td>
                                <td class="text-right">
                                    {{ $saldoPendienteTotal > 0 ? number_format(($cliente->deuda_total / $saldoPendienteTotal) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Indicadores Clave -->
        <div class="table-container">
            <div class="table-title">📈 Indicadores Clave de Rendimiento (KPI)</div>
            <div class="summary-grid" style="grid-template-columns: 1fr 1fr 1fr 1fr;">
                <div class="summary-card">
                    <div class="card-title">🎯 Tasa de Recuperación</div>
                    <div class="card-value text-success">{{ number_format($porcentajePagado, 1) }}%</div>
                    <div class="card-subtitle">Porcentaje de cartera recuperada</div>
                </div>

                <div class="summary-card warning">
                    <div class="card-title">⚡ Eficiencia de Cobro</div>
                    <div class="card-value text-warning">
                        {{ $totalCreditos > 0 ? number_format(($creditosPagados / $totalCreditos) * 100, 1) : 0 }}%
                    </div>
                    <div class="card-subtitle">Créditos pagados vs total</div>
                </div>

                <div class="summary-card info">
                    <div class="card-title">💸 Promedio por Crédito</div>
                    <div class="card-value text-info">
                        ${{ $totalCreditos > 0 ? number_format($montoTotalCartera / $totalCreditos, 2) : '0.00' }}
                    </div>
                    <div class="card-subtitle">Valor promedio de créditos</div>
                </div>

                <div class="summary-card danger">
                    <div class="card-title">🚨 Riesgo de Cartera</div>
                    <div class="card-value text-danger">
                        {{ $totalCreditos > 0 ? number_format(($creditosVencidos / $totalCreditos) * 100, 1) : 0 }}%
                    </div>
                    <div class="card-subtitle">Porcentaje de créditos vencidos</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>💼 Resumen de Cartera - Cierre de Caja</strong> | Sistema de Gestión de Créditos</p>
            <p>Este reporte consolida el estado actual de toda la cartera de créditos al {{ $fechaReporte }}</p>
            <p><em>📊 Utilice esta información para tomar decisiones estratégicas sobre el portafolio</em></p>
        </div>
    </div>
</body>

</html>
