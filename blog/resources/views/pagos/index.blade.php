@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave me-2 text-success"></i>
            Gestión de Pagos
        </h1>
        <a href="{{ route('pagos.create') }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus me-1"></i>
            Nuevo Pago
        </a>
    </div>
    <p class="text-muted mb-4">Registro y seguimiento de pagos recibidos</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Pagos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pagos->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monto Total Pagado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($pagos->sum('monto_pago'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pagos Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pagos->filter(function($pago) { return \Carbon\Carbon::parse($pago->fecha_pago)->isToday(); })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Promedio por Pago
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ $pagos->count() > 0 ? number_format($pagos->avg('monto_pago'), 2) : '0.00' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Pagos</h6>
            <div class="d-flex align-items-center">
                <div class="btn-group me-3" role="group" aria-label="Filtro de fecha">
                    <input type="radio" class="btn-check" name="filtroFecha" id="filtroTodos" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="filtroTodos">Todos</label>
                    <input type="radio" class="btn-check" name="filtroFecha" id="filtroHoy" autocomplete="off">
                    <label class="btn btn-outline-info" for="filtroHoy">Hoy</label>
                </div>
                <div class="input-group input-group-sm" style="max-width: 200px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Buscar cliente..." id="buscarCliente">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaPagos">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Cliente / Crédito</th>
                            <th class="border-0 text-center">Fecha</th>
                            <th class="border-0 text-center">Monto</th>
                            <th class="border-0 text-center">Método</th>
                            <th class="border-0 text-center">Estado</th>
                            <th class="border-0 text-center">WhatsApp</th>
                            <th class="border-0 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            @php
                                $esHoy = \Carbon\Carbon::parse($pago->fecha_pago)->isToday();
                                $estadoBadgeClass = '';
                                switch(strtolower($pago->estado_pago)) {
                                    case 'pagado':
                                        $estadoBadgeClass = 'bg-success';
                                        break;
                                    case 'pendiente':
                                        $estadoBadgeClass = 'bg-warning';
                                        break;
                                    default:
                                        $estadoBadgeClass = 'bg-secondary';
                                        break;
                                }

                                $metodoBadgeClass = '';
                                $metodoIcon = '';
                                switch(strtolower($pago->metodo_pago)) {
                                    case 'efectivo':
                                        $metodoBadgeClass = 'bg-success';
                                        $metodoIcon = 'fas fa-money-bill';
                                        break;
                                    case 'transferencia':
                                        $metodoBadgeClass = 'bg-info';
                                        $metodoIcon = 'fas fa-exchange-alt';
                                        break;
                                    case 'tarjeta':
                                        $metodoBadgeClass = 'bg-primary';
                                        $metodoIcon = 'fas fa-credit-card';
                                        break;
                                    default:
                                        $metodoBadgeClass = 'bg-secondary';
                                        $metodoIcon = 'fas fa-question';
                                        break;
                                }

                                // Calcular días para vencimiento
                                $credito = $pago->credito;
                                $fechaVencimiento = $credito->fecha_vencimiento_ext ?? $credito->fecha_vencimiento;
                                $hoy = \Carbon\Carbon::now();
                                $diasParaVencimiento = $hoy->diffInDays($fechaVencimiento, false);
                                $mostrarWhatsApp = $diasParaVencimiento >= 0 && $diasParaVencimiento <= 3 && $credito->estado !== 'pagado';
                                
                                // Preparar datos para WhatsApp
                                if ($mostrarWhatsApp && $credito->cliente) {
                                    $cliente = $credito->cliente;
                                    $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono ?? '');
                                    $saldoPendiente = number_format($credito->saldo_pendiente, 2);
                                    $fechaVencimientoFormato = $fechaVencimiento->format('d/m/Y');
                                    
                                    $mensaje = "Hola {$cliente->nombre}, te recordamos que tu crédito #{$credito->codigo} con saldo pendiente de \${$saldoPendiente} vence el {$fechaVencimientoFormato}";
                                    if ($diasParaVencimiento == 0) {
                                        $mensaje .= " (hoy). ";
                                    } elseif ($diasParaVencimiento == 1) {
                                        $mensaje .= " (mañana). ";
                                    } else {
                                        $mensaje .= " (en {$diasParaVencimiento} días). ";
                                    }
                                    $mensaje .= "Por favor, coordina tu pago. ¡Gracias!";
                                    
                                    $mensajeCodificado = urlencode($mensaje);
                                    $urlWhatsApp = "https://wa.me/{$telefono}?text={$mensajeCodificado}";
                                }
                            @endphp
                            <tr class="pago-row"
                                data-cliente="{{ strtolower($pago->credito->cliente->nombre ?? '') }}"
                                data-fecha="{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') }}"
                                data-estado="{{ strtolower($pago->estado_pago) }}">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-receipt text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">#{{ $pago->id }}</div>
                                            @if($esHoy)
                                                <span class="badge bg-info mt-1">Hoy</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold">{{ $pago->credito->cliente->nombre ?? 'Cliente no encontrado' }}</div>
                                    <div class="text-muted small">Crédito #{{ $pago->credito->id ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="fw-bold text-success fs-5">
                                        ${{ number_format($pago->monto_pago, 2) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $metodoBadgeClass }} fs-6">
                                        <i class="{{ $metodoIcon }} me-1"></i>
                                        {{ ucfirst($pago->metodo_pago ?? 'No especificado') }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $estadoBadgeClass }} fs-6">
                                        <i class="fas fa-circle me-1" style="font-size: 0.7em;"></i> {{-- Icono de círculo --}}
                                        {{ ucfirst($pago->estado_pago) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    @if($mostrarWhatsApp)
                                        <a href="{{ $urlWhatsApp }}" 
                                           target="_blank" 
                                           class="btn btn-whatsapp btn-sm shadow-sm" 
                                           title="Enviar recordatorio por WhatsApp (vence en {{ $diasParaVencimiento }} {{ $diasParaVencimiento == 1 ? 'día' : 'días' }})">
                                            <i class="fab fa-whatsapp me-1"></i>
                                            <span class="badge bg-danger rounded-pill">{{ $diasParaVencimiento }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('pagos.edit', $pago) }}" class="btn btn-outline-primary" title="Editar Pago">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger" onclick="eliminarPago({{ $pago->id }})" title="Eliminar Pago">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-info-circle me-2"></i> No se encontraron pagos.
                                    <a href="{{ route('pagos.create') }}" class="btn btn-link p-0">Crear nuevo pago</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmarEliminacionModal" tabindex="-1" aria-labelledby="confirmarEliminacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmarEliminacionModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este pago?</p>
                    <p class="text-muted">Esta acción es irreversible y afectará los saldos de la cuenta por cobrar asociada.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">
                        <i class="fas fa-trash me-1"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- Sección para estilos específicos de esta vista --}}
@section('styles')
<style>
    /* Estilos de las cards de resumen */
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }

    /* Estilos generales de tabla y componentes */
    .avatar-sm {
        width: 40px;
        height: 40px;
        line-height: 40px; /* Centrar verticalmente icono */
        text-align: center;
        font-size: 1.2rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
        transition: all 0.2s ease-in-out;
    }
    
    .card {
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,.07) !important;
    }
    
    .btn-group-sm .btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
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
        font-size: 0.8rem; /* Tamaño de fuente un poco más grande para los badges */
        padding: 0.4em 0.6em;
    }
    
    .fs-6 {
        font-size: 0.9rem !important; /* Ajuste para badges y textos pequeños */
    }
    
    .fs-5 {
        font-size: 1.3rem !important; /* Ajuste para montos */
    }

    /* Ajuste para el encabezado de la tabla para que los iconos no se peguen */
    .table thead th {
        vertical-align: middle;
        white-space: nowrap;
    }
    .table thead th i {
        margin-right: 0.3em;
    }
    
    /* Pequeño margen para los botones en la barra superior */
    .d-sm-flex .btn {
        margin-left: 0.75rem;
    }
    
    /* Estilos para el botón de WhatsApp */
    .btn-whatsapp {
        background-color: #25d366;
        color: white;
        border: none;
        padding: 0.4rem 0.8rem;
        transition: all 0.3s ease;
    }
    
    .btn-whatsapp:hover {
        background-color: #128c7e;
        color: white;
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(37, 211, 102, 0.3);
    }
    
    .btn-whatsapp .badge {
        font-size: 0.7rem;
        margin-left: 0.2rem;
    }
</style>
@endsection

{{-- Sección para scripts específicos de esta vista --}}
@section('scripts')
<script>
    let pagoAEliminarId = null;

    // Lógica de filtros y búsqueda combinada
    function aplicarFiltros() {
        const busqueda = document.getElementById('buscarCliente').value.toLowerCase();
        const filtroHoyActivo = document.getElementById('filtroHoy').checked;
        const hoy = new Date().toISOString().split('T')[0];

        document.querySelectorAll('.pago-row').forEach(fila => {
            const cliente = fila.dataset.cliente;
            const fechaPago = fila.dataset.fecha;
            
            let mostrarPorBusqueda = cliente.includes(busqueda);
            let mostrarPorFiltroHoy = true;

            if (filtroHoyActivo && fechaPago !== hoy) {
                mostrarPorFiltroHoy = false;
            }

            fila.style.display = (mostrarPorBusqueda && mostrarPorFiltroHoy) ? 'table-row' : 'none';
        });
    }

    // Event Listeners para filtros
    document.querySelectorAll('input[name="filtroFecha"]').forEach(radio => {
        radio.addEventListener('change', aplicarFiltros);
    });

    // Event Listener para búsqueda
    document.getElementById('buscarCliente').addEventListener('input', aplicarFiltros);

    // Función para abrir el modal de confirmación de eliminación
    function eliminarPago(id) {
        pagoAEliminarId = id;
        const confirmarModal = new bootstrap.Modal(document.getElementById('confirmarEliminacionModal'));
        confirmarModal.show();
    }

    // Event Listener para el botón de confirmar eliminación dentro del modal
    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        if (pagoAEliminarId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/pagos/${pagoAEliminarId}`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    });

    // Ejecutar filtros iniciales al cargar la página para asegurar que el estado "Todos" o "Hoy" se aplique si se refresca.
    document.addEventListener('DOMContentLoaded', aplicarFiltros);

</script>
@endsection