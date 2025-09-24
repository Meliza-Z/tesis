@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Lista de Clientes
                    </h1>
                    <p class="text-muted mb-0">Gestión y administración de clientes</p>
                </div>
                <div>
                    <a href="{{ route('clientes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Nuevo Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    

    <!-- Tabla de Clientes -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-list me-2"></i>
                    Clientes Registrados
                </h6>
                <small class="text-white-50">Total: {{ $clientes->count() }} cliente(s)</small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <i class="fas fa-user me-1"></i>
                                Nombre
                            </th>
                            <th>
                                <i class="fas fa-id-card me-1"></i>
                                Cédula
                            </th>
                            <th>
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Dirección
                            </th>
                            <th>
                                <i class="fas fa-phone me-1"></i>
                                Teléfono
                            </th>
                            <th>
                                <i class="fas fa-envelope me-1"></i>
                                Correo
                            </th>
                            <th>
                                <i class="fas fa-credit-card me-1"></i>
                                Máximo Crédito
                            </th>
                            <th>
                                <i class="fas fa-cogs me-1"></i>
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary text-white rounded-circle">
                                                {{ substr($cliente->nombre, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $cliente->nombre }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $cliente->cedula }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $cliente->direccion }}</small>
                                </td>
                                <td>
                                    <a href="tel:{{ $cliente->telefono }}" class="text-decoration-none">
                                        {{ $cliente->telefono }}
                                    </a>
                                </td>
                                <td>
                                    <a href="mailto:{{ $cliente->email }}" class="text-decoration-none">
                                        {{ $cliente->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        ${{ number_format($cliente->limite_credito ?? 0, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clientes.edit', $cliente) }}" 
                                           class="btn btn-warning btn-sm" 
                                           title="Editar cliente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="confirmarEliminacion({{ $cliente->id }}, '{{ $cliente->nombre }}')"
                                                title="Eliminar cliente">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Formulario oculto para eliminar -->
                                    <form id="delete-form-{{ $cliente->id }}" 
                                          action="{{ route('clientes.destroy', $cliente) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 text-gray-300"></i>
                                    <p class="mb-0">No hay clientes registrados</p>
                                    <a href="{{ route('clientes.create') }}" class="btn btn-success mt-2">
                                        <i class="fas fa-plus me-1"></i>
                                        Crear primer cliente
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
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
                        <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                        <p class="mb-0">¿Estás seguro de que deseas eliminar al cliente:</p>
                        <strong id="clienteNombre" class="text-danger"></strong>
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
                        Eliminar Cliente
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
    let clienteIdEliminar = null;
    
    function confirmarEliminacion(id, nombre) {
        clienteIdEliminar = id;
        document.getElementById('clienteNombre').textContent = nombre;
        
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
    }
    
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (clienteIdEliminar) {
            document.getElementById('delete-form-' + clienteIdEliminar).submit();
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
    });
</script>

@endsection