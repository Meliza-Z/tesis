@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-handshake me-2 text-primary"></i>
            Detalle del Crédito #{{ $credito->id }}
        </h1>
        <div class="d-flex">
            <a href="{{ route('creditos.pdf', $credito->id) }}" class="btn btn-danger shadow-sm me-2">
                <i class="fas fa-file-pdf me-1"></i>
                Descargar PDF
            </a>
            <a href="{{ route('creditos.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Volver a Créditos
            </a>
        </div>
    </div>
    <p class="text-muted mb-4">Información detallada sobre el crédito y sus transacciones.</p>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Datos del Crédito
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Cliente:</div>
                        <div class="col-md-7">{{ $credito->cliente->nombre }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Fecha de Crédito:</div>
                        <div class="col-md-7">{{ \Carbon\Carbon::parse($credito->fecha_credito)->format('d/m/Y') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Fecha de Vencimiento:</div>
                        <div class="col-md-7">
                            @if ($credito->fecha_vencimiento)
                                {{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Plazo:</div>
                        <div class="col-md-7">{{ $credito->plazo }} días</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Monto Total del Crédito:</div>
                        <div class="col-md-7 fw-bold text-success">${{ number_format($credito->monto_total, 2) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 text-muted fw-bold">Estado:</div>
                        <div class="col-md-7">
                            @php
                                $estadoBadgeClass = '';
                                switch(strtolower($credito->estado)) {
                                    case 'activo':
                                        $estadoBadgeClass = 'bg-success';
                                        break;
                                    case 'inactivo':
                                        $estadoBadgeClass = 'bg-danger';
                                        break;
                                    case 'pagado':
                                        $estadoBadgeClass = 'bg-primary';
                                        break;
                                    default:
                                        $estadoBadgeClass = 'bg-secondary';
                                        break;
                                }
                            @endphp
                            <span class="badge {{ $estadoBadgeClass }}">{{ ucfirst($credito->estado) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i> Resumen de Pagos
                    </h6>
                </div>
                <div class="card-body">
                    @if($credito->pagos->isNotEmpty())
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($credito->pagos as $pago)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                                    </span>
                                    <span class="fw-bold text-success">${{ number_format($pago->monto_pago, 2) }}</span>
                                    {{-- Opcional: podrías añadir un enlace para ver el detalle del pago --}}
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="h6 mb-0 text-dark">Total Pagado:</span>
                            <span class="h5 mb-0 fw-bold text-success">${{ number_format($credito->pagos->sum('monto_pago'), 2) }}</span>
                        </div>
                    @else
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle me-2"></i> No hay pagos registrados para este crédito.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-boxes me-2"></i> Productos en Crédito
            </h6>
        </div>
        <div class="card-body p-0">
            @if($credito->detalles->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Producto</th>
                                <th class="border-0 text-center">Cantidad</th>
                                <th class="border-0 text-end">Precio Unitario ($)</th>
                                <th class="border-0 text-end">Subtotal ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalProductos = 0; @endphp
                            @foreach($credito->detalles as $detalle)
                                @php
                                    $subtotal = $detalle->cantidad * $detalle->precio_unitario;
                                    $totalProductos += $subtotal;
                                @endphp
                                <tr>
                                    <td class="align-middle fw-bold">{{ $detalle->producto->nombre ?? 'Producto Desconocido' }}</td>
                                    <td class="align-middle text-center">{{ $detalle->cantidad }}</td>
                                    <td class="align-middle text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="align-middle text-end fw-bold">${{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end py-3">Monto Total de Productos:</th>
                                <th class="text-end py-3 text-success fs-5">${{ number_format($totalProductos, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-warning m-4 text-center" role="alert">
                    <i class="fas fa-box-open me-2"></i> No hay productos asociados a este crédito.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('styles')
<style>
    /* Estilos adicionales si los necesitas para esta vista específica */
    /* Puedes usar las mismas clases de 'border-left-*' si aplicas tarjetas de resumen aquí */
    .card {
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,.07) !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .font-weight-bold {
        font-weight: 700;
    }
    .text-gray-800 {
        color: #5a5c69;
    }
    .text-gray-300 {
        color: #dddfeb;
    }
    .badge {
        font-size: 0.8rem;
        padding: 0.4em 0.6em;
    }
    .fs-5 {
        font-size: 1.3rem !important;
    }
</style>
@endsection