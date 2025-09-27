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
            <label for="plazo_dias" class="form-label">Plazo (días)</label>
            <input type="number" name="plazo_dias" id="plazo_dias" class="form-control" value="{{ old('plazo_dias', $credito->plazo_dias ?? 15) }}" min="1">
        </div>

        <div class="mb-3">
            <label for="fecha_vencimiento_ext" class="form-label">Extensión de Vencimiento (opcional)</label>
            <input type="date" name="fecha_vencimiento_ext" id="fecha_vencimiento_ext" class="form-control" value="{{ old('fecha_vencimiento_ext', $credito->fecha_vencimiento_ext) }}">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                @php $estados = ['pendiente','activo','vencido','pagado']; @endphp
                @foreach($estados as $e)
                    <option value="{{ $e }}" {{ $credito->estado == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('creditos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
