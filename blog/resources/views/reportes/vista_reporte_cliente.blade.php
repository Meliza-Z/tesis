@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header del reporte -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>
                                    Reporte de Crédito
                                </h3>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <a href="{{ route('reporte.cliente.pdf', $credito->id) }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>
                                        Descargar PDF
                                    </a>
                                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-light btn-sm">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    {{ $credito->cliente->nombre }}
                                </h4>
                                <div class="info-group">
                                    <p class="mb-2">
                                        <i class="fas fa-calendar-alt text-muted me-2"></i>
                                        <strong>Fecha del Crédito:</strong>
                                        <span
                                            class="badge bg-info">{{ \Carbon\Carbon::parse($credito->fecha_credito)->format('d/m/Y') }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-calendar-times text-muted me-2"></i>
                                        <strong>Fecha de Vencimiento:</strong>
                                        <span
                                            class="badge bg-warning">{{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-info-circle text-muted me-2"></i>
                                        <strong>Estado:</strong>
                                        <span
                                            class="badge bg-{{ $credito->estado === 'activo' ? 'success' : ($credito->estado === 'vencido' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($credito->estado) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Resumen rápido -->
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="card bg-light border-0 text-center">
                                            <div class="card-body py-2">
                                                <h6 class="text-muted mb-1">Total Productos</h6>
                                                <h5 class="text-primary mb-0">${{ number_format($credito->monto_total, 2) }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-light border-0 text-center">
                                            <div class="card-body py-2">
                                                <h6 class="text-muted mb-1">Total Pagado</h6>
                                                <h5 class="text-success mb-0">
                                                    ${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos adquiridos -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2 text-primary"></i>
                            Productos Adquiridos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">
                                            <i class="fas fa-box me-1"></i>
                                            Producto
                                        </th>
                                        <th class="border-0 text-center">
                                            <i class="fas fa-hashtag me-1"></i>
                                            Cantidad
                                        </th>
                                        <th class="border-0 text-end">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            Precio Unitario
                                        </th>
                                        <th class="border-0 text-end">
                                            <i class="fas fa-calculator me-1"></i>
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($credito->detalles as $detalle)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $detalle->producto->nombre }}</strong>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-secondary">{{ $detalle->cantidad }}</span>
                                            </td>
                                            <td class="text-end align-middle">
                                                ${{ number_format($detalle->precio_unitario, 2) }}
                                            </td>
                                            <td class="text-end align-middle">
                                                <strong class="text-primary">
                                                    ${{ number_format($detalle->subtotal, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end border-0">
                                            <strong>Total en Productos:</strong>
                                        </th>
                                        <th class="text-end border-0">
                                            <strong class="text-primary fs-5">
                                                ${{ number_format($credito->monto_total, 2) }}
                                            </strong>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagos realizados -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>
                            Historial de Pagos
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($credito->pagos as $pago)
                            <div class="row align-items-center py-2 border-bottom">
                                <div class="col-md-6">
                                    <i class="fas fa-calendar-check text-success me-2"></i>
                                    <span
                                        class="fw-bold">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        {{ number_format($pago->monto_pago, 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                <p class="text-muted">No hay pagos registrados para este crédito.</p>
                            </div>
                        @endforelse

                        @if ($credito->pagos->count() > 0)
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-md-6">
                                    <strong>Total Pagado:</strong>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-success fs-5">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        {{ number_format($totalPagado, 2) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resumen financiero -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Resumen Financiero
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-shopping-bag text-info fa-2x mb-2"></i>
                                    <h6 class="text-muted">Total en Productos</h6>
                                    <h4 class="text-info mb-0">${{ number_format($credito->monto_total, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <h6 class="text-muted">Total Pagado</h6>
                                    <h4 class="text-success mb-0">
                                        ${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i
                                        class="fas fa-{{ $credito->saldo_pendiente > 0 ? 'exclamation-triangle text-warning' : 'check-circle text-success' }} fa-2x mb-2"></i>
                                    <h6 class="text-muted">Saldo Pendiente</h6>
                                    <h4 class="text-{{ $credito->saldo_pendiente > 0 ? 'warning' : 'success' }} mb-0">
                                        ${{ number_format($credito->saldo_pendiente, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        @if ($credito->monto_total > 0)
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progreso de Pago</span>
                                    <span
                                        class="badge bg-primary">{{ round(($credito->pagos->sum('monto_pago') / $credito->monto_total) * 100, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ ($credito->pagos->sum('monto_pago') / $credito->monto_total) * 100 }}%"
                                        aria-valuenow="{{ $credito->pagos->sum('monto_pago') }}" aria-valuemin="0"
                                        aria-valuemax="{{ $credito->monto_total }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver a Reportes
                    </a>
                    <div class="btn-group">
                        <a href="{{ route('reporte.cliente.pdf', $credito->id) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i>
                            Descargar PDF
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-group p {
            transition: all 0.3s ease;
        }

        .info-group p:hover {
            transform: translateX(5px);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        @media print {

            .btn,
            .btn-group {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
@endsection
