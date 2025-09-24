@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-users me-2 text-primary"></i>
                        {{ isset($cliente->id) ? 'Editar Cliente' : 'Nuevo Cliente' }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ isset($cliente->id) ? 'Actualizar la información del cliente existente' : 'Crear un nuevo registro de cliente' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
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
                <i class="fas fa-edit me-2"></i>
                Datos del Cliente
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ isset($cliente->id) ? route('clientes.update', $cliente) : route('clientes.store') }}" method="POST">
                @csrf
                @if(isset($cliente->id))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $cliente->nombre ?? '') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                    <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula', $cliente->cedula ?? '') }}" maxlength="10" pattern="\d{10}" title="La cédula debe contener exactamente 10 dígitos numéricos." required>
                    @error('cedula')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                    <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $cliente->telefono ?? '') }}" maxlength="10" pattern="\d{10}" title="El teléfono debe contener exactamente 10 dígitos numéricos." required>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $cliente->direccion ?? '') }}">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $cliente->email ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="limite_credito" class="form-label">Límite Máximo de Crédito</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="limite_credito" id="limite_credito" class="form-control @error('limite_credito') is-invalid @enderror" value="{{ old('limite_credito', $cliente->limite_credito ?? '') }}" step="0.01" min="0" placeholder="0.00">
                    </div>
                    @error('limite_credito')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    {{ isset($cliente->id) ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tu estilo CSS existente */
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
    // Cerrar alerta automáticamente después de 5 segundos
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

        // --- Lógica de validación de 10 dígitos y solo números ---
        const cedulaInput = document.getElementById('cedula');
        const telefonoInput = document.getElementById('telefono');

        // Función para limitar a 10 dígitos y solo números
        function enforceTenDigitsAndNumbers(event) {
            let input = event.target;
            let value = input.value.replace(/\D/g, ''); // Elimina cualquier caracter que no sea un dígito
            
            if (value.length > 10) {
                value = value.substring(0, 10); // Corta a 10 dígitos si se excede
            }
            input.value = value; // Asigna el valor limpio y cortado
        }

        // Añadir event listeners
        if (cedulaInput) {
            cedulaInput.addEventListener('input', enforceTenDigitsAndNumbers);
            cedulaInput.addEventListener('paste', enforceTenDigitsAndNumbers); // Para pegar texto
        }
        if (telefonoInput) {
            telefonoInput.addEventListener('input', enforceTenDigitsAndNumbers);
            telefonoInput.addEventListener('paste', enforceTenDigitsAndNumbers); // Para pegar texto
        }
    });
</script>
@endsection