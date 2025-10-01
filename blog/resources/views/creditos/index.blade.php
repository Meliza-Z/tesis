@extends('layouts.app')

@section('title', 'Lista de Créditos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Lista de Créditos
                    </h4>
                    <p class="text-muted mb-0">Gestión y seguimiento de créditos de clientes</p>
                </div>
                {{-- <a href="{{ route('creditos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Nuevo Crédito
                </a> --}}
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Créditos Registrados
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       id="search-input" 
                                       class="form-control border-start-0" 
                                       placeholder="Buscar crédito..."
                                       style="background-color: white;">
                                <span class="input-group-text bg-white border-start-0 pe-3">
                                    <small class="text-muted">Total: <span id="total-count">{{ $creditos->count() }}</span> crédito(s)</small>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-white"><i class="fas fa-user me-1"></i> Cliente</th>
                                    <th class="text-white"><i class="fas fa-calendar me-1"></i> Fecha Crédito</th>
                                    <th class="text-white"><i class="fas fa-calendar-times me-1"></i> Vencimiento</th>
                                    <th class="text-white"><i class="fas fa-clock me-1"></i> Plazo</th>
                                    <th class="text-white"><i class="fas fa-dollar-sign me-1"></i> Monto</th>
                                    <th class="text-white"><i class="fas fa-info-circle me-1"></i> Estado</th>
                                    <th class="text-white"><i class="fas fa-cogs me-1"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="creditos-table-body">
                                @forelse($creditos as $credito)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    {{ strtoupper(substr($credito->cliente->nombre, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $credito->cliente->nombre }}</h6>
                                                    <small class="text-muted">Código: {{ $credito->codigo }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($credito->fecha_credito)->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}</span>
                                            @if(\Carbon\Carbon::parse($credito->fecha_vencimiento)->isPast() && $credito->estado !== 'pagado')
                                                <br><span class="badge bg-danger badge-sm">Vencido</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $dias = \Carbon\Carbon::parse($credito->fecha_credito)->diffInDays(\Carbon\Carbon::parse($credito->fecha_vencimiento));
                                            @endphp
                                            <span class="text-muted">{{ $dias }} días</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">${{ number_format($credito->monto_total, 2) }}</span>
                                            <br><small class="text-muted">Pendiente: ${{ number_format($credito->saldo_pendiente, 2) }}</small>
                                        </td>
                                        <td>
                                            @if($credito->estado === 'pendiente')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($credito->estado === 'pagado')
                                                <span class="badge bg-success">Pagado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('creditos.show', $credito->id) }}" 
                                                   class="btn btn-outline-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('creditos.edit', $credito->id) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('creditos.pdf', $credito->id) }}" 
                                                   class="btn btn-outline-secondary" 
                                                   title="Exportar PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        onclick="confirmarEliminacion({{ $credito->id }})"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e0' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'%3E%3C/path%3E%3Cpolyline points='14,2 14,8 20,8'%3E%3C/polyline%3E%3Cline x1='12' y1='18' x2='12' y2='12'%3E%3C/line%3E%3Cline x1='9' y1='15' x2='15' y2='15'%3E%3C/line%3E%3C/svg%3E" alt="No hay datos" class="mb-3">
                                                <h5 class="text-muted mb-2">No hay créditos registrados que coincidan con la búsqueda</h5>
                                                {{-- Botón de crear crédito oculto por decisión de UX --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.card {
    border-radius: 8px;
}

.card-header {
    border-bottom: none;
    border-radius: 8px 8px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 12px 15px;
}

.table td {
    padding: 15px;
    border-color: #f1f3f4;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.775rem;
}

.badge-sm {
    font-size: 0.7em;
}

.empty-state img {
    opacity: 0.5;
    margin-bottom: 1rem;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.table-responsive {
    position: relative;
}
</style>

<script>
$(document).ready(function() {
    // Configurar CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let searchTimeout;
    
    // Búsqueda dinámica
    $('#search-input').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        // Mostrar loading
        showLoading();
        
        searchTimeout = setTimeout(function() {
            buscarCreditos(searchTerm);
        }, 300);
    });

    function showLoading() {
        if ($('.loading-overlay').length === 0) {
            $('.table-responsive').append(`
                <div class="loading-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `);
        }
        $('.loading-overlay').show();
    }

    function hideLoading() {
        $('.loading-overlay').hide();
    }

    function buscarCreditos(searchTerm) {
        $.ajax({
            url: "{{ route('creditos.index') }}",
            method: 'GET',
            data: { 
                search: searchTerm,
                ajax: true 
            },
            success: function(response) {
                actualizarTabla(response.creditos);
                $('#total-count').text(response.creditos.length);
                hideLoading();
            },
            error: function() {
                hideLoading();
                Swal.fire('Error', 'Error al realizar la búsqueda', 'error');
            }
        });
    }

    function actualizarTabla(creditos) {
        const tbody = $('#creditos-table-body');
        tbody.empty();

        if (creditos.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-state">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e0' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'%3E%3C/path%3E%3Cpolyline points='14,2 14,8 20,8'%3E%3C/polyline%3E%3Cline x1='12' y1='18' x2='12' y2='12'%3E%3C/line%3E%3Cline x1='9' y1='15' x2='15' y2='15'%3E%3C/line%3E%3C/svg%3E" alt="No hay datos" class="mb-3">
                            <h5 class="text-muted mb-2">No se encontraron resultados</h5>
                            <p class="text-muted">Intenta con otros términos de búsqueda</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        creditos.forEach(function(credito) {
            const fechaCredito = new Date(credito.fecha_credito).toLocaleDateString('es-ES');
            const fechaVencimiento = new Date(credito.fecha_vencimiento).toLocaleDateString('es-ES');
            
            // Calcular días de plazo
            const fechaC = new Date(credito.fecha_credito);
            const fechaV = new Date(credito.fecha_vencimiento);
            const dias = Math.ceil((fechaV - fechaC) / (1000 * 60 * 60 * 24));
            
            let estadoBadge = '';
            if (credito.estado === 'pendiente') {
                estadoBadge = '<span class="badge bg-warning">Pendiente</span>';
            } else if (credito.estado === 'pagado') {
                estadoBadge = '<span class="badge bg-success">Pagado</span>';
            }

            let vencidoBadge = '';
            if (credito.is_expired_and_unpaid) {
                vencidoBadge = '<br><span class="badge bg-danger badge-sm">Vencido</span>';
            }

            const iniciales = credito.cliente.nombre.substring(0, 2).toUpperCase();
            
            tbody.append(`
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                ${iniciales}
                            </div>
                            <div>
                                <h6 class="mb-0">${credito.cliente.nombre}</h6>
                                <small class="text-muted">Código: ${credito.codigo}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="fw-medium">${fechaCredito}</span></td>
                    <td><span class="fw-medium">${fechaVencimiento}</span>${vencidoBadge}</td>
                    <td><span class="text-muted">${dias} días</span></td>
                    <td>
                        <span class="fw-bold text-success">$${parseFloat(credito.monto_total).toLocaleString('es-ES', {minimumFractionDigits: 2})}</span>
                        <br><small class="text-muted">Pendiente: $${parseFloat(credito.saldo_pendiente).toLocaleString('es-ES', {minimumFractionDigits: 2})}</small>
                    </td>
                    <td>${estadoBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="/creditos/${credito.id}" class="btn btn-outline-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/creditos/${credito.id}/edit" class="btn btn-outline-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="/creditos/${credito.id}/pdf" class="btn btn-outline-secondary" title="Exportar PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="confirmarEliminacion(${credito.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    }
});

// Confirmación de eliminación
function confirmarEliminacion(creditoId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarCredito(creditoId);
        }
    });
}

function eliminarCredito(creditoId) {
    const form = $('<form>', {
        'method': 'POST',
        'action': `/creditos/${creditoId}`
    });
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content')
    }));
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
    }));
    
    $('body').append(form);
    form.submit();
}

// Mensajes flash
@if(session('success'))
    Swal.fire('Éxito', '{{ session('success') }}', 'success');
@endif

@if(session('error'))
    Swal.fire('Error', '{{ session('error') }}', 'error');
@endif
</script>
@endsection
