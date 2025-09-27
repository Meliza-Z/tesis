@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-receipt me-2 text-primary"></i>
                            Listado de Detalles de Crédito
                        </h1>
                        <p class="text-muted mb-0">Visualización de ítems vendidos a crédito, agrupados por fecha</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="buscarClienteDetalle" class="form-control" placeholder="Buscar cliente...">
                        </div>
                        <a href="{{ route('detalle_credito.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Detalle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @forelse($detallesPorFecha as $fecha => $detalles)
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Fecha: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-user me-1"></i> Cliente</th>
                                    <th><i class="fas fa-box me-1"></i> Producto</th>
                                    <th class="text-center"><i class="fas fa-sort-numeric-up me-1"></i> Cantidad</th>
                                    <th class="text-end"><i class="fas fa-dollar-sign me-1"></i> Precio Unitario</th>
                                    <th class="text-end"><i class="fas fa-money-bill-wave me-1"></i> Subtotal</th>
                                    <th class="text-center"><i class="fas fa-cogs me-1"></i> Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalPorFecha = 0; @endphp
                                @php $porCliente = $detalles->groupBy('cliente'); @endphp
                                @foreach($porCliente as $clienteNombre => $items)
                                    <tr class="table-secondary" data-client="{{ $clienteNombre }}">
                                        <td colspan="6"><strong><i class="fas fa-user me-2"></i>Cliente: {{ $clienteNombre }}</strong></td>
                                    </tr>
                                    @php $totalCliente = 0; @endphp
                                    @foreach($items as $detalle)
                                        @php
                                            $subtotal = $detalle->cantidad * $detalle->precio_unitario;
                                            $totalCliente += $subtotal;
                                            $totalPorFecha += $subtotal;
                                        @endphp
                                        <tr data-client="{{ $clienteNombre }}">
                                            <td></td>
                                            <td>{{ $detalle->producto }}</td>
                                            <td class="text-center">{{ $detalle->cantidad }}</td>
                                            <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                            <td class="text-end"><strong>${{ number_format($subtotal, 2) }}</strong></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('detalle_credito.edit', $detalle->id) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Editar detalle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm"
                                                            onclick="confirmarEliminacion({{ $detalle->id }}, '{{ $detalle->producto }} ({{ $clienteNombre }})')"
                                                            title="Eliminar detalle">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <form id="delete-form-{{ $detalle->id }}"
                                                      action="{{ route('detalle_credito.destroy', $detalle->id) }}"
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr data-client="{{ $clienteNombre }}">
                                        <td colspan="4" class="text-end"><em>Total de {{ $clienteNombre }}:</em></td>
                                        <td class="text-end"><strong>${{ number_format($totalCliente, 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total para esta fecha:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($totalPorFecha, 2) }}</strong></td>
                                    <td></td> {{-- Empty cell for actions --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info py-4 text-center">
                <i class="fas fa-info-circle fa-3x mb-3 text-gray-400"></i>
                <p class="mb-0">No hay detalles de crédito registrados. ¡Comienza agregando uno!</p>
                <a href="{{ route('detalle_credito.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Primer Detalle
                </a>
            </div>
        @endforelse

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                            <p class="mb-0">¿Estás seguro de que deseas eliminar este detalle de crédito para:</p>
                            <strong id="detalleInfo" class="text-danger"></strong>
                            <p class="text-muted mt-2 mb-0">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash me-1"></i>
                            Eliminar Detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        .avatar-sm {
            width: 2.5rem;
            height: 2.5rem;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .btn-group .btn {
            margin-right: 0.25rem;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.7em;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .table th {
            border-color: #dee2e6;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table td {
            border-color: #dee2e6;
            vertical-align: middle;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }
    </style>

    <script>
        let detalleIdEliminar = null;

        function confirmarEliminacion(id, info) {
            detalleIdEliminar = id;
            document.getElementById('detalleInfo').textContent = info;

            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (detalleIdEliminar) {
                document.getElementById('delete-form-' + detalleIdEliminar).submit();
            }
        });

        // Cerrar alerta automáticamente después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            }

            // Filtro por cliente
            const input = document.getElementById('buscarClienteDetalle');
            function filtrarPorCliente(termino) {
                const t = (termino || '').toLowerCase().trim();
                document.querySelectorAll('table.table tbody').forEach(tb => {
                    let anyShownInTable = false;
                    const headers = tb.querySelectorAll('tr.table-secondary');
                    headers.forEach(header => {
                        const nombre = (header.dataset.client || header.textContent).toLowerCase();
                        const match = !t || nombre.includes(t);
                        // Reunir filas hasta el siguiente header o fin de tbody
                        let rows = [];
                        let r = header.nextElementSibling;
                        while (r && !r.classList.contains('table-secondary')) {
                            rows.push(r);
                            r = r.nextElementSibling;
                        }
                        header.style.display = match ? '' : 'none';
                        rows.forEach(x => x.style.display = match ? '' : 'none');
                        if (match) anyShownInTable = true;
                    });
                    const card = tb.closest('.card');
                    if (card) card.style.display = anyShownInTable ? '' : 'none';
                });
            }
            if (input) {
                input.addEventListener('input', e => filtrarPorCliente(e.target.value));
            }
        });
    </script>
@endsection
