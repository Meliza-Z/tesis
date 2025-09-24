<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Diario de Detalles de Crédito</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header .business-name {
            color: #666;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .fecha {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-box .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-box .label {
            font-weight: bold;
            color: #333;
        }
        .summary-box .value {
            color: #007bff;
            font-weight: bold;
        }
        .cliente-section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .cliente-header {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .cliente-total {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .grand-total {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-name">D'Margarita's</div>
        <h1>Reporte Diario de Ventas a Crédito</h1>
        <p class="fecha"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="row">
            <span class="label">Total Vendido:</span>
            <span class="value">${{ number_format($totalDelDia, 2) }}</span>
        </div>
        <div class="row">
            <span class="label">Total Transacciones:</span>
            <span class="value">{{ $totalTransacciones }}</span>
        </div>
        <div class="row">
            <span class="label">Clientes Atendidos:</span>
            <span class="value">{{ $clientesUnicos }}</span>
        </div>
    </div>

    @if($detallesPorFecha->count() > 0)
        @foreach($detallesPorCliente as $cliente => $detalles)
            <div class="cliente-section">
                <div class="cliente-header">
                    {{ $cliente }} ({{ $detalles->count() }} productos)
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35%">Producto</th>
                            <th style="width: 15%" class="text-center">Cantidad</th>
                            <th style="width: 20%" class="text-right">Precio Unit.</th>
                            <th style="width: 20%" class="text-right">Subtotal</th>
                            <th style="width: 10%" class="text-center">Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto_nombre }}</td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($detalle->created_at)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                        <tr class="cliente-total">
                            <td colspan="3"><strong>Total para {{ $cliente }}:</strong></td>
                            <td class="text-right"><strong>${{ number_format($detalles->sum('subtotal'), 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="grand-total">
            <h2>TOTAL GENERAL DEL DÍA: ${{ number_format($totalDelDia, 2) }}</h2>
            <p>{{ $totalTransacciones }} transacciones realizadas | {{ $clientesUnicos }} clientes atendidos</p>
        </div>
    @else
        <div class="no-data">
            <h3>No se encontraron ventas para la fecha seleccionada</h3>
            <p>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        </div>
        <div class="grand-total">
            <h2>TOTAL GENERAL DEL DÍA: $0.00</h2>
        </div>
    @endif
</body>
</html>