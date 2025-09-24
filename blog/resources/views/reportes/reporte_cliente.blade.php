<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Crédito - {{ $credito->cliente->nombre }}</title>
    <style>
        /* Fuentes */
        body {
            font-family: DejaVu Sans, sans-serif; /* Mejor para compatibilidad con caracteres especiales en PDF */
            font-size: 11px;
            margin: 20px;
            color: #333;
        }
        
        /* Encabezado del Reporte */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff; /* Línea de color para el encabezado */
        }
        .header h1 {
            margin: 0;
            color: #003366; /* Azul oscuro para el título principal */
            font-size: 24px;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #777;
        }

        /* Títulos de Sección */
        .section-title {
            font-size: 16px;
            color: #007bff; /* Azul para títulos de sección */
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            font-weight: bold;
        }

        /* Información General del Crédito (Formato de tabla para alineación) */
        .info-grid {
            display: table; /* Simula un layout de dos columnas para asegurar alineación en PDF */
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label, .info-value {
            display: table-cell;
            padding: 4px 0;
        }
        .info-label {
            font-weight: bold;
            width: 160px; /* Ancho fijo para las etiquetas */
            color: #555;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1); /* Sombra ligera para las tablas */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e9f5ff; /* Fondo azul claro para encabezados de tabla */
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
        /* Fila de totales en tablas */
        .total-row th, .total-row td {
            background-color: #d1ecf1; /* Fondo diferente para filas de total */
            color: #003366;
            font-size: 12px;
            font-weight: bold;
        }

        /* Resumen Financiero */
        .summary-box {
            border: 1px solid #007bff; /* Borde azul principal */
            padding: 15px;
            background-color: #eaf6ff; /* Fondo azul muy claro */
            margin-top: 30px;
            width: 60%; /* Ajustado para que no ocupe todo el ancho si no es necesario */
            margin-left: auto;
            margin-right: auto; /* Centrar la caja de resumen */
        }
        .summary-item {
            /* Usar display table para alineación robusta en PDF */
            display: table;
            width: 100%;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .summary-item .label, .summary-item .value {
            display: table-cell;
            padding-bottom: 3px;
        }
        .summary-item .label {
            font-weight: bold;
            color: #003366;
            width: 70%; /* Ajuste para el ancho de la etiqueta */
        }
        .summary-item .value {
            text-align: right;
            font-weight: bold;
            color: #28a745; /* Color verde para montos positivos */
        }
        .summary-item.balance .value {
            font-size: 18px;
            color: #dc3545; /* Color rojo para el saldo pendiente */
        }

        /* Badges de Estado */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }
        /* Colores para los estados */
        .status-activo { background-color: #28a745; } /* green */
        .status-inactivo { background-color: #dc3545; } /* red */
        .status-pagado { background-color: #007bff; } /* blue */
        .status-pendiente { background-color: #ffc107; color: #333; } /* yellow */
        .status-vencido { background-color: #6c757d; } /* gray */
    </style>
</head>
<body>

    <div class="header">
        <h1>D'Margarita's</h1>
        <p>Reporte Detallado de Crédito</p>
        <p>Cliente: <strong>{{ $credito->cliente->nombre }}</strong></p>
        <p>Fecha de Emisión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">Información General del Crédito #{{ $credito->id }}</div>
    <div class="info-grid">
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
            <span class="info-label">Estado del Crédito:</span>
            <span class="info-value">
                @php
                    $estadoClass = strtolower($credito->estado);
                    // Lógica para marcar como "vencido" si la fecha de vencimiento es pasada y no está pagado
                    if ($credito->estado != 'pagado' && $credito->fecha_vencimiento && \Carbon\Carbon::parse($credito->fecha_vencimiento)->isPast()) {
                         $estadoClass = 'vencido';
                    }
                @endphp
                <span class="status-badge status-{{ $estadoClass }}">{{ ucfirst($credito->estado) }}</span>
            </span>
        </div>
    </div>

    <div class="section-title">Productos Adquiridos</div>
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
            @forelse($credito->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre ?? 'Producto Desconocido' }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay productos registrados en este crédito.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" class="text-right">Total Crédito en Productos:</th>
                <td class="text-right">${{ number_format($totalProductos, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Pagos Realizados</div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Fecha de Pago</th>
                <th class="text-right">Monto ($)</th>
                <th class="text-center">Método de Pago</th> {{-- Asumiendo que existe $pago->metodo_pago --}}
                <th class="text-center">Estado del Pago</th> {{-- Asumiendo que existe $pago->estado_pago --}}
            </tr>
        </thead>
        <tbody>
            @forelse($credito->pagos as $pago)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                    <td class="text-right">${{ number_format($pago->monto_pago, 2) }}</td>
                    <td class="text-center">{{ ucfirst($pago->metodo_pago ?? 'N/A') }}</td> 
                    <td class="text-center">{{ ucfirst($pago->estado_pago ?? 'N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay pagos registrados para este crédito.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="1" class="text-right">Total Pagado:</th>
                <td class="text-right">${{ number_format($totalPagado, 2) }}</td>
                <th colspan="2"></th> {{-- Espacios vacíos para alinear las columnas --}}
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Resumen Financiero</div>
    <div class="summary-box">
        <div class="summary-item">
            <span class="label">Límite de Crédito:</span>
            <span class="value">${{ number_format($totalCredito, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Total Crédito en Productos:</span>
            <span class="value">${{ number_format($totalProductos, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Total Pagado:</span>
            <span class="value">${{ number_format($totalPagado, 2) }}</span>
        </div>
        <div class="summary-item balance">
            <span class="label">Saldo Pendiente:</span>
            <span class="value" style="color: {{ $saldoPendiente > 0 ? '#dc3545' : '#28a745' }};">
                ${{ number_format($saldoPendiente, 2) }}
            </span>
        </div>
    </div>

</body>
</html>