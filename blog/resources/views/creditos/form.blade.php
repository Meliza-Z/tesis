@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-credit-card me-2 text-primary"></i>
                        {{ isset($credito->id) ? 'Editar Crédito' : 'Nuevo Crédito' }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ isset($credito->id) ? 'Actualizar la información del crédito existente' : 'Registrar un nuevo crédito para un cliente' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Detalles del Crédito
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ $route }}" method="POST">
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $credito->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} (Cédula: {{ $cliente->cedula ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fecha_credito" class="form-label">Fecha de Crédito <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_credito" id="fecha_credito" class="form-control @error('fecha_credito') is-invalid @enderror" value="{{ old('fecha_credito', $credito->fecha_credito ?? '') }}" required>
                    @error('fecha_credito')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento', $credito->fecha_vencimiento ?? '') }}" required>
                    @error('fecha_vencimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="plazo" class="form-label">Plazo (días) <span class="text-danger">*</span></label>
                    <input type="number" name="plazo" id="plazo" class="form-control @error('plazo') is-invalid @enderror" value="{{ old('plazo', $credito->plazo ?? '') }}" readonly required>
                    @error('plazo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="monto_total" class="form-label">Límite de crédito <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="monto_total" id="monto_total" class="form-control @error('monto_total') is-invalid @enderror" value="{{ old('monto_total', $credito->monto_total ?? '') }}" required>
                    @error('monto_total')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        <option value="pendiente" {{ old('estado', $credito->estado ?? '') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ old('estado', $credito->estado ?? '') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        {{-- Add other states if applicable, e.g.: --}}
                        {{-- <option value="aprobado" {{ old('estado', $credito->estado ?? '') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ old('estado', $credito->estado ?? '') == 'rechazado' ? 'selected' : '' }}>Rechazado</option> --}}
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    {{ isset($credito->id) ? 'Actualizar Crédito' : 'Guardar Crédito' }}
                </button>
                <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times-circle me-2"></i>
                    Cancelar
                </a>
            </form>
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
    const fechaCreditoInput = document.getElementById('fecha_credito');
    const fechaVencimientoInput = document.getElementById('fecha_vencimiento');
    const plazoInput = document.getElementById('plazo');

    function calcularPlazo() {
        const inicio = new Date(fechaCreditoInput.value);
        const fin = new Date(fechaVencimientoInput.value);

        // Ensure both dates are valid and the end date is after the start date
        if (fechaCreditoInput.value && fechaVencimientoInput.value && fin > inicio) {
            const diferencia = (fin - inicio) / (1000 * 60 * 60 * 24); // Difference in milliseconds converted to days
            plazoInput.value = Math.round(diferencia);
        } else {
            plazoInput.value = ''; // Clear if dates are invalid or mismatched
        }
    }

    // Add event listeners
    fechaCreditoInput.addEventListener('change', calcularPlazo);
    fechaVencimientoInput.addEventListener('change', calcularPlazo);

    // Call on page load in case of old values being present (e.g., after validation error)
    document.addEventListener('DOMContentLoaded', calcularPlazo);

    // Auto-close alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alertSuccess = document.querySelector('.alert-success');
        if (alertSuccess) {
            setTimeout(() => {
                alertSuccess.style.transition = 'opacity 0.5s ease';
                alertSuccess.style.opacity = '0';
                setTimeout(() => {
                    alertSuccess.remove();
                }, 500);
            }, 5000);
        }

        const alertDanger = document.querySelector('.alert-danger');
        if (alertDanger) {
            setTimeout(() => {
                alertDanger.style.transition = 'opacity 0.5s ease';
                alertDanger.style.opacity = '0';
                setTimeout(() => {
                    alertDanger.remove();
                }, 500);
            }, 7000); // Give error alerts a bit more time
        }
    });
</script>
@endsection