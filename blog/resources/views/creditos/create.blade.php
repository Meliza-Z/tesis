@extends('layouts.app')

@section('title', 'Nuevo Crédito')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-credit-card me-2 text-primary"></i>
                    Nuevo Crédito
                </h4>
                <p class="text-muted mb-0">
                    Registrar un nuevo crédito para un cliente
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

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0">
                <i class="fas fa-plus me-2"></i>Formulario de Nuevo Crédito
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('creditos.store') }}" method="POST">
                @csrf

                {{-- Campo para el Cliente --}}
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                        <option value="">Seleccione un cliente</option>
                        {{-- La variable $clientes debe ser pasada desde el controlador --}}
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} (Cédula: {{ $cliente->cedula ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo para la Fecha de Crédito --}}
                <div class="mb-3">
                    <label for="fecha_credito" class="form-label">Fecha de Crédito <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_credito" id="fecha_credito" class="form-control @error('fecha_credito') is-invalid @enderror" value="{{ old('fecha_credito', date('Y-m-d')) }}" required>
                    @error('fecha_credito')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo para la Fecha de Vencimiento --}}
                <div class="mb-3">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento') }}" required>
                    @error('fecha_vencimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Plazo y extensión opcional --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="plazo_dias" class="form-label">Plazo (días)</label>
                        <input type="number" name="plazo_dias" id="plazo_dias" class="form-control @error('plazo_dias') is-invalid @enderror" value="{{ old('plazo_dias', 15) }}" min="1">
                        @error('plazo_dias')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_vencimiento_ext" class="form-label">Extensión de Vencimiento (opcional)</label>
                        <input type="date" name="fecha_vencimiento_ext" id="fecha_vencimiento_ext" class="form-control @error('fecha_vencimiento_ext') is-invalid @enderror" value="{{ old('fecha_vencimiento_ext') }}">
                        @error('fecha_vencimiento_ext')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Campo para el Estado --}}
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                        @php $estados = ['pendiente','activo','vencido','pagado']; @endphp
                        @foreach($estados as $e)
                            <option value="{{ $e }}" {{ old('estado') == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Guardar Crédito
                    </button>
                    <a href="{{ route('creditos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times-circle me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script></script>
@endsection
