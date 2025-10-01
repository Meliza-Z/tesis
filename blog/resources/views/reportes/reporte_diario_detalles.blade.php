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
            <span class="label">Créditos Nuevos:</span>
            <span class="value">${{ number_format($totalCreditosDelDia, 2) }}</span>
        </div>
        <div class="row">
            <span class="label">Pagos Recibidos:</span>
            <span class="value">${{ number_format($totalPagosDelDia, 2) }}</span>
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

    @if($creditosData->count() > 0 || $pagosData->count() > 0)
        
        {{-- Sección de Créditos Nuevos --}}
        @if($creditosData->count() > 0)
            <h2 style="color: #007bff; margin-top: 30px; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                Créditos Otorgados ({{ $creditosData->count() }})
            </h2>
            @foreach($creditosData as $credito)
                <div class="cliente-section">
                    <div class="cliente-header">
                        {{ $credito['cliente_nombre'] }} - Crédito {{ $credito['credito_codigo'] }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%">Producto</th>
                                <th style="width: 15%" class="text-center">Cantidad</th>
                                <th style="width: 17%" class="text-right">Precio Unit.</th>
                                <th style="width: 18%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credito['detalles'] as $detalle)
                                <tr>
                                    <td>{{ $detalle['producto'] }}</td>
                                    <td class="text-center">{{ $detalle['cantidad'] }}</td>
                                    <td class="text-right">${{ number_format($detalle['precio_unitario'], 2) }}</td>
                                    <td class="text-right">${{ number_format($detalle['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="cliente-total">
                                <td colspan="3"><strong>Total Crédito:</strong></td>
                                <td class="text-right"><strong>${{ number_format($credito['monto'], 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

        {{-- Sección de Pagos Recibidos --}}
        @if($pagosData->count() > 0)
            <h2 style="color: #28a745; margin-top: 30px; border-bottom: 2px solid #28a745; padding-bottom: 5px;">
                Pagos Recibidos ({{ $pagosData->count() }})
            </h2>
            <table style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th style="width: 30%">Cliente</th>
                        <th style="width: 25%">Crédito</th>
                        <th style="width: 20%" class="text-right">Monto</th>
                        <th style="width: 25%" class="text-center">Método de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagosData as $pago)
                        <tr>
                            <td>{{ $pago['cliente_nombre'] }}</td>
                            <td>{{ $pago['credito_codigo'] }}</td>
                            <td class="text-right">${{ number_format($pago['monto'], 2) }}</td>
                            <td class="text-center">{{ $pago['metodo_pago'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="cliente-total">
                        <td colspan="2"><strong>Total Pagos del Día:</strong></td>
                        <td class="text-right"><strong>${{ number_format($totalPagosDelDia, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div class="grand-total">
            <h2>RESUMEN DEL DÍA: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
            <p>Créditos Otorgados: ${{ number_format($totalCreditosDelDia, 2) }} | 
               Pagos Recibidos: ${{ number_format($totalPagosDelDia, 2) }}</p>
            <p>{{ $totalTransacciones }} transacciones realizadas | {{ $clientesUnicos }} clientes atendidos</p>
        </div>
    @else
        <div class="no-data">
            <h3>No se encontraron transacciones para la fecha seleccionada</h3>
            <p>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        </div>
        <div class="grand-total">
            <h2>SIN ACTIVIDAD EN EL DÍA</h2>
            <p>No se registraron créditos nuevos ni pagos recibidos</p>
        </div>
    @endif
</body>
</html>