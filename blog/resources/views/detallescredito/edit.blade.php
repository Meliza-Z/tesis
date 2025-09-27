@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Editar Detalle #{{ $detalle_credito->id }} (Crédito #{{ $detalle_credito->credito_id }})
                </h4>
                <p class="text-muted mb-0">Cliente: {{ optional($detalle_credito->credito->cliente)->nombre ?? 'N/D' }}</p>
            </div>
            <div>
                <a href="{{ route('detalle_credito.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-box me-2"></i>Detalle</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('detalle_credito.update', $detalle_credito) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Producto</label>
                        <select name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}" {{ old('producto_id', $detalle_credito->producto_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} (${{ number_format(($p->precio_base_centavos ?? 0)/100, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" min="1" name="cantidad" value="{{ old('cantidad', $detalle_credito->cantidad) }}" class="form-control @error('cantidad') is-invalid @enderror" required>
                        @error('cantidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio unitario ($)</label>
                        <input type="number" step="0.01" min="0" name="precio_unitario" value="{{ old('precio_unitario', $detalle_credito->precio_unitario) }}" class="form-control @error('precio_unitario') is-invalid @enderror" required>
                        @error('precio_unitario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="form-control">{{ old('observaciones', $detalle_credito->observaciones) }}</textarea>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Actualizar</button>
                    <a href="{{ route('detalle_credito.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
