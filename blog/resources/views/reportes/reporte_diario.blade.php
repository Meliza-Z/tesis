
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
    <h2>Reporte Diario de Créditos</h2>
    <p class="fecha"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>

    @if($creditos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Monto Crédito</th>
                    <th>Pagado</th>
                    <th>Saldo Pendiente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($creditos as $credito)
                    @php
                        $pagado = $credito->pagos->sum('monto_pago');
                        $saldo = $credito->monto_total - $pagado; // Usar monto_total
                    @endphp
                    <tr>
                        <td>{{ $credito->cliente->nombre }}</td>
                        <td>${{ number_format($credito->monto_total, 2) }}</td>
                        <td>${{ number_format($pagado, 2) }}</td>
                        <td>${{ number_format($saldo, 2) }}</td>
                        <td>{{ ucfirst($credito->estado) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <h3>Total Acreditado del Día: ${{ number_format($totalDelDia, 2) }}</h3>
            <p>Total de créditos: {{ $creditos->count() }}</p>
        </div>
    @else
        <div class="no-data">
            <p>No se encontraron transacciones para la fecha seleccionada.</p>
        </div>
        <div class="total">
            <h3>Total Acreditado del Día: $0.00</h3>
        </div>
    @endif
</body>
</html>