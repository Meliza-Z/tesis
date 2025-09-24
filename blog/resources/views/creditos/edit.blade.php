@extends('layouts.app')

@section('title', 'Editar Crédito')

@section('content')
<div class="container">
    <h1>Editar Crédito #{{ $credito->codigo }}</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('creditos.update', $credito->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-control" required>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $credito->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_credito" class="form-label">Fecha Crédito</label>
            <input type="date" name="fecha_credito" id="fecha_credito" class="form-control" value="{{ $credito->fecha_credito }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ $credito->fecha_vencimiento }}" required>
        </div>

        <div class="mb-3">
            <label for="monto_total" class="form-label">Monto Total</label>
            <input type="number" step="0.01" name="monto_total" id="monto_total" class="form-control" value="{{ $credito->monto_total }}" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="pendiente" {{ $credito->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="pagado" {{ $credito->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('creditos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
