@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-money-check-alt me-2 text-primary"></i>
                        Cuentas por Cobrar
                    </h1>
                    <p class="text-muted mb-0">Gestión y seguimiento de cuentas pendientes</p>
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

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Cuentas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cuentas->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Al Día
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cuentas->filter(function($cuenta) { return $cuenta->saldoPendienteReal <= 0; })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Progreso
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cuentas->filter(function($cuenta) { return $cuenta->saldoPendienteReal > 0; })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Pendiente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($cuentas->sum('saldoPendienteReal'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros rápidos -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Filtros Rápidos</h6>
                </div>
                <div class="col-auto">
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="filtro" id="todos" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="todos">Todos</label>

                        <input type="radio" class="btn-check" name="filtro" id="alDia" autocomplete="off">
                        <label class="btn btn-outline-success" for="alDia">Al Día</label>

                        <input type="radio" class="btn-check" name="filtro" id="enMora" autocomplete="off">
                        <label class="btn btn-outline-info" for="enMora">En Progreso</label>

                        <input type="radio" class="btn-check" name="filtro" id="proximoVencer" autocomplete="off">
                        <label class="btn btn-outline-warning" for="proximoVencer">Próximo a Vencer</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Tabla de cuentas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>
                        Listado de Cuentas
                    </h6>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Buscar cliente..." id="buscarCliente">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaCuentas">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">
                                <i class="fas fa-user me-1"></i>
                                Cliente
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Monto Adeudado
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-balance-scale me-1"></i>
                                Saldo Pendiente
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Vencimiento
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-chart-line me-1"></i>
                                Progreso
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-cogs me-1"></i>
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuentas as $cuenta)
                            @php
                                $porcentajePagado = $cuenta->totalProductos > 0 ? ($cuenta->totalProductos - $cuenta->saldoPendienteReal) / $cuenta->totalProductos * 100 : 0;
                                
                                // Usar la fecha de vencimiento correcta (del crédito si existe, sino de la cuenta)
                                $fechaVencimiento = $cuenta->fecha_vencimiento_real ?? $cuenta->fecha_vencimiento;
                                $diasVencimiento = \Carbon\Carbon::parse($fechaVencimiento)->diffInDays(now(), false);
                                $estadoVencimiento = $diasVencimiento > 0 ? 'vencido' : ($diasVencimiento > -7 ? 'proximo' : 'normal');
                            @endphp
                            <tr class="cuenta-row" data-estado="{{ $cuenta->saldoPendienteReal <= 0 ? 'al-dia' : 'mora' }}" data-vencimiento="{{ $estadoVencimiento }}">
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $cuenta->cliente->nombre ?? 'No encontrado' }}</div>
                                            <div class="text-muted small">ID: {{ $cuenta->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="fw-bold text-primary">
                                        ${{ number_format($cuenta->totalProductos, 2) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="fw-bold text-{{ $cuenta->saldoPendienteReal > 0 ? 'danger' : 'success' }}">
                                        ${{ number_format($cuenta->saldoPendienteReal, 2) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($fechaVencimiento)->format('d/m/Y') }}</span>
                                        @if($estadoVencimiento === 'vencido')
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                            </small>
                                        @elseif($estadoVencimiento === 'proximo')
                                            <small class="text-warning">
                                                
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                   
                                <td class="text-center align-middle">
                                    <div class="progress-container">
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $porcentajePagado == 100 ? 'success' : ($porcentajePagado > 50 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $porcentajePagado }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ round($porcentajePagado) }}%</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('cuenta_cobrar.edit', $cuenta) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
    
    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .progress-container {
        min-width: 80px;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
        transition: all 0.3s ease;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
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
</style>

<script>
    // Filtros
    document.querySelectorAll('input[name="filtro"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const filtro = this.id;
            const filas = document.querySelectorAll('.cuenta-row');
            
            filas.forEach(fila => {
                fila.style.display = 'table-row';
                
                if (filtro === 'alDia' && fila.dataset.estado !== 'al-dia') {
                    fila.style.display = 'none';
                } else if (filtro === 'enMora' && fila.dataset.estado !== 'mora') {
                    fila.style.display = 'none';
                } else if (filtro === 'proximoVencer' && fila.dataset.vencimiento !== 'proximo') {
                    fila.style.display = 'none';
                }
            });
        });
    });
    
    // Búsqueda
    document.getElementById('buscarCliente').addEventListener('input', function() {
        const busqueda = this.value.toLowerCase();
        const filas = document.querySelectorAll('.cuenta-row');
        
        filas.forEach(fila => {
            const cliente = fila.querySelector('td:first-child .fw-bold').textContent.toLowerCase();
            fila.style.display = cliente.includes(busqueda) ? 'table-row' : 'none';
        });
    });
    
    // Función para sincronizar con crédito
    function sincronizarConCredito(cuentaId) {
        if (confirm('¿Desea sincronizar esta cuenta con los datos del crédito asociado?')) {
            fetch(`/cuenta-cobrar/${cuentaId}/sincronizar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error al sincronizar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al sincronizar la cuenta');
            });
        }
    }
    
    // Establecer fecha actual por defecto
    document.getElementById('fechaPago').valueAsDate = new Date();
</script>
@endsection