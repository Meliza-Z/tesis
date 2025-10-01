<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crédito #{{ $credito->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 20px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #777;
        }

        .section-title {
            font-size: 16px;
            color: #007bff;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-grid {
            display: table;
            /* Usar display table para simular columnas en PDF */
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label,
        .info-value {
            display: table-cell;
            padding: 4px 0;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            /* Ancho fijo para las etiquetas */
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #333;
            text-align: center;
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

        .total-row th {
            background-color: #e9ecef;
            color: #000;
            font-size: 13px;
        }

        .total-row td {
            background-color: #e9ecef;
            color: #000;
            font-size: 13px;
            font-weight: bold;
        }

        .summary-box {
            border: 1px solid #007bff;
            padding: 15px;
            background-color: #eaf6ff;
            margin-top: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-item strong {
            color: #007bff;
        }

        .summary-item span {
            font-weight: bold;
        }

        .summary-item.total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #a3d2ff;
            padding-top: 10px;
            margin-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .status-activo {
            background-color: #28a745;
        }

        /* green */
        .status-inactivo {
            background-color: #dc3545;
        }

        /* red */
        .status-pagado {
            background-color: #007bff;
        }

        /* blue */
        .status-pendiente {
            background-color: #ffc107;
            color: #333;
        }

        /* yellow */
    </style>
</head>

<body>
    <div class="header">
        <h1>D'Margarita's</h1>
        <p>Reporte de Crédito</p>
        <p>Fecha de Emisión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">Detalle del Crédito #{{ $credito->id }}</div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span class="info-value">{{ $credito->cliente->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Crédito:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($credito->fecha_credito)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Vencimiento:</span>
            <span class="info-value">
                @if ($credito->fecha_vencimiento)
                    {{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}
                @else
                    N/A
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Plazo:</span>
            <span class="info-value">{{ $credito->plazo_dias ?? ($credito->dias_plazo ?? 'N/A') }} días</span>
        </div>
        <div class="info-row">
            <span class="info-label">Monto Total del Crédito:</span>
            <span class="info-value">${{ number_format($credito->monto_total, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                @php
                    $estadoClass = strtolower($credito->estado);
                    // Asegúrate de que el estado en la base de datos coincida con las clases CSS (activo, inactivo, pagado, pendiente)
                    // Si tu estado es 'finalizado' y quieres que sea como 'pagado', ajusta aquí.
                @endphp
                <span class="status-badge status-{{ $estadoClass }}">{{ ucfirst($credito->estado) }}</span>
            </span>
        </div>
    </div>

    <div class="section-title">Pagos Realizados</div>
    @if ($credito->pagos->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th class="text-center">Fecha de Pago</th>
                    <th class="text-center">Monto ($)</th>
                    <th class="text-center">Método de Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($credito->pagos as $pago)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td class="text-right">${{ number_format($pago->monto_pago, 2) }}</td>
                        <td class="text-center">{{ ucfirst($pago->metodo_pago ?? 'N/A') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="2" class="text-right">Total Pagado:</th>
                    <td class="text-right">${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p>No hay pagos registrados para este crédito.</p>
    @endif

    <div class="section-title">Productos en Crédito</div>
    @if ($credito->detalles->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio Unitario ($)</th>
                    <th class="text-right">Subtotal ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($credito->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre ?? 'Producto Desconocido' }}</td>
                        <td class="text-center">{{ $detalle->cantidad }}</td>
                        <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="3" class="text-right">Total Productos:</th>
                    <td class="text-right">${{ number_format($credito->monto_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p>No hay productos registrados en este crédito.</p>
    @endif

    <div class="summary-box">
        <div class="summary-item">
            <strong>Monto Total del Crédito:</strong>
            <span>${{ number_format($credito->monto_total, 2) }}</span>
        </div>
        <div class="summary-item">
            <strong>Pagos Realizados:</strong>
            <span>${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</span>
        </div>
        <div class="summary-item total">
            <strong>Saldo Pendiente:</strong>
            <span>${{ number_format($credito->saldo_pendiente, 2) }}</span>
        </div>
    </div>

</body>

</html>
