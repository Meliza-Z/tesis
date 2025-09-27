@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 text-gray-800">
                        <i class="fas fa-boxes me-2 text-primary"></i>
                        Gestión de Productos
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-chart-bar me-1"></i>
                        {{ $productos->count() }} productos en {{ $productosPorCategoria->count() }} categorías
                    </p>
                </div>
                <div>
                    <a href="{{ route('productos.create') }}" class="btn btn-success btn-lg shadow">
                        <i class="fas fa-plus me-2"></i>
                        Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros en card separado -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <div class="d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                <h6 class="mb-0">Filtros de Búsqueda</h6>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar productos..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="categoria" class="form-select">
                        <option value="">📂 Todas las categorías</option>
                        @foreach($categorias as $key => $categoria)
                            <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                {{ $categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>
                            Buscar
                        </button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Dashboard de Categorías -->
    @if($productosPorCategoria->count() > 0)
        <div class="row">
            @foreach($productosPorCategoria as $nombreCategoria => $productosCategoria)
                @php
                    $primerProducto = $productosCategoria->first();
                    $color = $primerProducto->categoria_color ?? '#6B7280';
                    $icono = $primerProducto->categoria_icono ?? 'fas fa-tag';
                @endphp
                
                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                    <div class="card categoria-card h-100 shadow-sm" data-categoria="{{ $nombreCategoria }}">
                        <!-- Header de Categoría -->
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background: linear-gradient(135deg, {{ $color }}15, {{ $color }}25); border-left: 4px solid {{ $color }};">
                            <div class="d-flex align-items-center">
                                <div class="categoria-icon me-3" style="background: {{ $color }}20; color: {{ $color }};">
                                    <i class="{{ $icono }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $nombreCategoria }}</h6>
                                    <small class="text-muted">{{ $productosCategoria->count() }} productos</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lista de Productos -->
                        <div class="card-body p-0">
                            <div class="productos-lista" style="max-height: 300px; overflow-y: auto;">
                                @foreach($productosCategoria as $index => $producto)
                                    <div class="producto-item {{ $index >= 3 ? 'd-none producto-oculto' : '' }}" 
                                         data-categoria="{{ $nombreCategoria }}">
                                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center">
                                                    <div class="producto-avatar me-3" style="background: {{ $color }}10;">
                                                        <span style="color: {{ $color }};">
                                                            {{ substr($producto->nombre, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 producto-nombre">{{ $producto->nombre }}</h6>
                                                        @if($producto->descripcion)
                                                            <small class="text-muted">{{ Str::limit($producto->descripcion, 40) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-dark fw-bold">${{ number_format(($producto->precio_base_centavos ?? 0)/100, 2) }}</span>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('productos.edit', $producto) }}" 
                                                       class="btn btn-outline-warning btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            onclick="confirmarEliminacion({{ $producto->id }}, '{{ $producto->nombre }}')"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Botón Ver más/menos si hay más de 3 productos -->
                            @if($productosCategoria->count() > 3)
                                <div class="card-footer bg-light text-center">
                                    <button class="btn btn-link btn-sm text-decoration-none toggle-productos" 
                                            data-categoria="{{ $nombreCategoria }}">
                                        <span class="mostrar-mas">
                                            <i class="fas fa-chevron-down me-1"></i>
                                            Ver {{ $productosCategoria->count() - 3 }} productos más
                                        </span>
                                        <span class="mostrar-menos d-none">
                                            <i class="fas fa-chevron-up me-1"></i>
                                            Ver menos
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Forms ocultos para eliminar -->
                        @foreach($productosCategoria as $producto)
                            <form id="delete-form-{{ $producto->id }}" 
                                  action="{{ route('productos.destroy', $producto) }}" 
                                  method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado vacío -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No hay productos registrados</h4>
                            <p class="text-muted mb-4">Comienza agregando tu primer producto al inventario</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Crear Primer Producto
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
            <div class="modal-body text-center">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <p>¿Estás seguro de que deseas eliminar el producto:</p>
                <strong id="productoNombre" class="text-danger"></strong>
                <p class="text-muted mt-2">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>
                    Eliminar Producto
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.categoria-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
}

.categoria-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.categoria-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.producto-avatar {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.producto-item {
    transition: all 0.2s ease;
}

.producto-item:hover {
    background-color: #f8f9fc;
}

.producto-nombre {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
}

.productos-lista {
    border-radius: 0 0 12px 12px;
}

.toggle-productos {
    font-size: 0.875rem;
    font-weight: 500;
    color: #4a5568;
}

.toggle-productos:hover {
    color: #2d3748;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.8rem;
}

.alert {
    border-radius: 10px;
    border: none;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    border-bottom: 1px solid #e2e8f0;
}

.empty-state i {
    opacity: 0.5;
}

.input-group-text {
    border-color: #e2e8f0;
    background-color: #f7fafc;
}

.form-control:focus, .form-select:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
}
</style>

<script>
let productoIdEliminar = null;

// Toggle productos ocultos
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-productos').forEach(btn => {
        btn.addEventListener('click', function() {
            const categoria = this.dataset.categoria;
            const productosOcultos = document.querySelectorAll(`.producto-oculto[data-categoria="${categoria}"]`);
            const mostrarMas = this.querySelector('.mostrar-mas');
            const mostrarMenos = this.querySelector('.mostrar-menos');
            
            productosOcultos.forEach(item => {
                item.classList.toggle('d-none');
            });
            
            mostrarMas.classList.toggle('d-none');
            mostrarMenos.classList.toggle('d-none');
        });
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    productoIdEliminar = id;
    document.getElementById('productoNombre').textContent = nombre;
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

// Confirmar eliminación
document.getElementById('confirmDelete').addEventListener('click', function() {
    if (productoIdEliminar) {
        document.getElementById('delete-form-' + productoIdEliminar).submit();
    }
});

// Auto-dismiss alert
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.querySelector('.alert-success');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }
});
</script>
@endsection
