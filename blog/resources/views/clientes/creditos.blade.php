@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Créditos de {{ $cliente->nombre }}
                </h4>
                <p class="text-muted mb-0">
                    Límite: ${{ number_format(($limiteCentavos/100), 2) }} · Usado: ${{ number_format(($expuestoCentavos/100), 2) }}
                </p>
            </div>
            <div>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver a Clientes
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center {{ $bloqueado ? 'bg-secondary' : 'bg-primary' }} text-white">
            <h6 class="mb-0"><i class="fas fa-plus me-2"></i>Crear nuevo crédito</h6>
            @if($bloqueado)
                <span class="badge bg-dark">Bloqueado ({{ $tieneVencido ? 'tiene vencido' : 'límite de 2 abiertos' }})</span>
            @else
                <span class="badge bg-light text-dark">Abiertos: {{ $abiertos }}/2</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('clientes.creditos.store', ['cliente' => $cliente->id]) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha crédito</label>
                        <input type="date" name="fecha_credito" id="fecha_credito" class="form-control" value="{{ old('fecha_credito', now()->toDateString()) }}" required {{ $bloqueado ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha vencimiento</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}" required {{ $bloqueado ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Plazo (días)</label>
                        <input type="number" name="plazo_dias" id="plazo_dias" class="form-control" value="{{ old('plazo_dias', 15) }}" min="1" {{ $bloqueado ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Extensión (opcional)</label>
                        <input type="date" name="fecha_vencimiento_ext" id="fecha_vencimiento_ext" class="form-control" value="{{ old('fecha_vencimiento_ext') }}" {{ $bloqueado ? 'disabled' : '' }}>
                        <small class="text-muted">No puede ser anterior a la fecha de vencimiento.</small>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" {{ $bloqueado ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Créditos</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Vence</th>
                            <th class="text-end">Monto</th>
                            <th class="text-end">Pagado</th>
                            <th class="text-end">Saldo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditos as $c)
                            <tr>
                                <td>{{ $c->codigo }}</td>
                                <td>{{ \Carbon\Carbon::parse($c->fecha_credito)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($c->fecha_vencimiento)->format('d/m/Y') }}</td>
                                <td class="text-end">${{ number_format($c->monto_total, 2) }}</td>
                                <td class="text-end">${{ number_format($c->total_pagado_centavos/100, 2) }}</td>
                                <td class="text-end">${{ number_format($c->saldo_pendiente, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $c->estado === 'pagado' ? 'success' : ($c->estado === 'vencido' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($c->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Sin créditos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fechaCredito = document.getElementById('fecha_credito');
        const plazoDias = document.getElementById('plazo_dias');
        const fechaVencimiento = document.getElementById('fecha_vencimiento');
        const fechaVencimientoExt = document.getElementById('fecha_vencimiento_ext');

        function toISODate(d) {
            const pad = (n) => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        }

        function recomputeVencimiento() {
            if (!fechaCredito || !plazoDias || !fechaVencimiento) return;
            const base = fechaCredito.value;
            const plazo = parseInt(plazoDias.value || '0', 10);
            if (!base || isNaN(plazo) || plazo < 1) { return; }
            const parts = base.split('-');
            if (parts.length !== 3) { return; }
            const d = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
            d.setDate(d.getDate() + plazo);
            const iso = toISODate(d);
            fechaVencimiento.value = iso;
            if (fechaVencimientoExt) {
                fechaVencimientoExt.min = iso;
                // Si la extensión quedó por debajo del mínimo, corrígela
                if (fechaVencimientoExt.value && fechaVencimientoExt.value < iso) {
                    fechaVencimientoExt.value = iso;
                }
            }
        }

        if (fechaCredito) fechaCredito.addEventListener('change', recomputeVencimiento);
        if (plazoDias) plazoDias.addEventListener('input', recomputeVencimiento);

        // Validación: extensión no puede ser antes del vencimiento calculado
        if (fechaVencimientoExt) {
            fechaVencimientoExt.addEventListener('change', function() {
                if (!fechaVencimiento.value) return;
                const min = fechaVencimiento.value;
                if (fechaVencimientoExt.value && fechaVencimientoExt.value < min) {
                    alert('La fecha de extensión no puede ser anterior a la fecha de vencimiento.');
                    fechaVencimientoExt.value = min;
                }
            });
        }

        // Inicializar si no hay valor existente
        if (!fechaVencimiento.value) {
            recomputeVencimiento();
        } else if (fechaVencimientoExt) {
            // Asegurar mínimo al cargar
            fechaVencimientoExt.min = fechaVencimiento.value;
        }
    });
    </script>
@endsection
