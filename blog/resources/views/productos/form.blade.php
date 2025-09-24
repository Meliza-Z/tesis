@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-box-open me-2 text-primary"></i>
                        {{ isset($producto->id) ? 'Editar Producto' : 'Nuevo Producto' }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ isset($producto->id) ? 'Actualizar la información del producto existente' : 'Registrar un nuevo producto en el inventario' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
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
                <i class="fas fa-cubes me-2"></i>
                Detalles del Producto
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ isset($producto->id) ? route('productos.update', $producto) : route('productos.store') }}" method="POST">
                @csrf
                @if(isset($producto->id))
                    @method('PUT')
                @endif

                <!-- Campo de Categoría - Siempre visible -->
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                        <option value="">Seleccionar categoría...</option>
                        @foreach($categorias as $key => $categoria)
                            <option value="{{ $key }}" {{ old('categoria', $producto->categoria ?? '') == $key ? 'selected' : '' }}>
                                {{ $categoria }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campos del producto - Solo visibles después de seleccionar categoría -->
                <div id="campos-producto" class="campos-ocultos" style="{{ old('categoria', $producto->categoria ?? '') ? 'display: block;' : 'display: none;' }}">
                    
                    <!-- Mensaje de instrucción cuando no hay categoría seleccionada -->
                    <div id="mensaje-categoria" class="alert alert-info" style="{{ old('categoria', $producto->categoria ?? '') ? 'display: none;' : 'display: block;' }}">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Selecciona una categoría</strong> para continuar completando la información del producto.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" id="precio" step="0.01" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $producto->precio ?? '') }}" required>
                                </div>
                                @error('precio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" placeholder="Describe las características principales del producto...">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('productos.index') }}'">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ isset($producto->id) ? 'Actualizar Producto' : 'Guardar Producto' }}
                        </button>
                    </div>
                </div>
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

    /* Animaciones para mostrar/ocultar campos */
    .campos-ocultos {
        transition: all 0.4s ease-in-out;
        opacity: 0;
        transform: translateY(-10px);
    }

    .campos-ocultos.mostrar {
        opacity: 1;
        transform: translateY(0);
        display: block !important;
    }

    .input-group-text {
        background-color: #f8f9fc;
        border-color: #d1d3e2;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoria');
        const camposProducto = document.getElementById('campos-producto');
        const mensajeCategoria = document.getElementById('mensaje-categoria');

        // Función para mostrar/ocultar campos
        function toggleCampos() {
            if (categoriaSelect.value) {
                // Mostrar campos
                camposProducto.style.display = 'block';
                setTimeout(() => {
                    camposProducto.classList.add('mostrar');
                }, 10);
                mensajeCategoria.style.display = 'none';
                
                // Enfocar el primer campo
                document.getElementById('nombre').focus();
            } else {
                // Ocultar campos
                camposProducto.classList.remove('mostrar');
                setTimeout(() => {
                    if (!categoriaSelect.value) {
                        camposProducto.style.display = 'none';
                        mensajeCategoria.style.display = 'block';
                    }
                }, 400);
            }
        }

        // Evento cuando cambia la categoría
        categoriaSelect.addEventListener('change', toggleCampos);

        // Si ya hay una categoría seleccionada (edición o validación), mostrar campos
        if (categoriaSelect.value) {
            camposProducto.classList.add('mostrar');
            mensajeCategoria.style.display = 'none';
        }

        // Auto-dismiss alerts
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
            }, 7000);
        }

        // Validación del formulario antes del envío
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const categoria = categoriaSelect.value;
            const nombre = document.getElementById('nombre').value.trim();
            const precio = document.getElementById('precio').value;

            if (!categoria) {
                e.preventDefault();
                alert('Por favor, selecciona una categoría.');
                categoriaSelect.focus();
                return false;
            }

            if (!nombre) {
                e.preventDefault();
                alert('Por favor, ingresa el nombre del producto.');
                document.getElementById('nombre').focus();
                return false;
            }

            if (!precio || precio <= 0) {
                e.preventDefault();
                alert('Por favor, ingresa un precio válido.');
                document.getElementById('precio').focus();
                return false;
            }
        });
    });
</script>
@endsection