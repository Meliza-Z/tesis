@extends('layouts.app')

@section('content')
<div class="container-fluid py-4"> {{-- Usamos container-fluid para más espacio y py-4 para padding vertical --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                        Agregar Productos al Crédito
                    </h1>
                    <p class="text-muted mb-0">Asocia productos a un crédito específico de un cliente</p>
                </div>
                <div>
                    <a href="{{ route('detalle_credito.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a los Detalles
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes de éxito y error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4 animate__animated animate__fadeInUp"> {{-- Sombra y animación al cargar --}}
        <div class="card-header bg-primary text-white py-3"> {{-- Cabecera de la tarjeta con color primario --}}
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-plus-circle me-2"></i>
                Formulario de Registro de Detalle
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('detalle_credito.store') }}" method="POST" id="formulario-detalles">
                @csrf

                <div class="mb-4 p-3 border rounded bg-light"> {{-- Sección de Crédito con un marco suave --}}
                    <label for="credito_id" class="form-label mb-2 text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Seleccionar Crédito <span class="text-danger">*</span>
                    </label>
                    <select name="credito_id" id="credito_id" class="form-select form-select-lg @error('credito_id') is-invalid @enderror" required>
                        <option value="">-- Seleccione un crédito --</option>
                        @foreach ($creditos as $credito)
                            <option value="{{ $credito->id }}" {{ old('credito_id') == $credito->id ? 'selected' : '' }}>
                                #{{ $credito->id }} - Cliente: {{ $credito->cliente->nombre ?? 'Desconocido' }} (Monto: ${{ number_format($credito->monto_total, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('credito_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4"> {{-- Separador --}}

                <h5 class="mb-3 text-gray-800">
                    <i class="fas fa-boxes me-2 text-info"></i>
                    Añadir Productos al Detalle
                </h5>
                <div class="row g-3 align-items-end mb-4 p-3 border rounded"> {{-- Sección de añadir productos --}}
                    <div class="col-md-6">
                        <label for="producto_id" class="form-label">Producto</label>
                        <select id="producto_id" class="form-select @error('producto_id_temp') is-invalid @enderror">
                            <option value="">Seleccione un producto</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}">
                                {{ $producto->nombre }} - ${{ number_format($producto->precio, 2) }}
                            </option>
                        @endforeach
                        </select>
                        @error('producto_id_temp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" id="cantidad" class="form-control @error('cantidad_temp') is-invalid @enderror" min="1" value="1">
                        @error('cantidad_temp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="btn-agregar" class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus me-2"></i>
                            Agregar
                        </button>
                    </div>
                </div>

                <h5 class="mb-3 text-gray-800">
                    <i class="fas fa-clipboard-list me-2 text-success"></i>
                    Productos Añadidos
                </h5>
                <div class="table-responsive mb-4 shadow-sm rounded"> {{-- Tabla responsiva con sombra --}}
                    <table class="table table-bordered table-hover table-striped" id="tabla-productos">
                        <thead class="table-dark"> {{-- Cabecera oscura para la tabla --}}
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unitario</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Los productos agregados dinámicamente irán aquí --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total General:</strong></td>
                                <td class="text-end"><strong id="total-general">$0.00</strong></td>
                                <td></td> {{-- Columna vacía para la acción --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-save me-2"></i>
                        Guardar Detalles del Crédito
                    </button>
                    <a href="{{ route('detalle_credito.index') }}" class="btn btn-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Estilos personalizados (pueden ir en un archivo CSS separado) --}}
<style>
    /* Asegúrate de tener Font Awesome en tu proyecto o agrega el CDN en layouts/app.blade.php */
    /* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> */

    body {
        background-color: #f8f9fa; /* Color de fondo suave */
    }

    .h3.text-gray-800 {
        color: #343a40 !important; /* Un gris más oscuro para los títulos */
    }

    .card {
        border-radius: 0.75rem; /* Bordes más redondeados para las tarjetas */
        border: none; /* Eliminar borde predeterminado */
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-3px); /* Pequeño efecto hover */
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important; /* Sombra más pronunciada al hover */
    }

    .card-header {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        font-size: 1.1rem;
    }

    .form-label {
        font-weight: 600; /* Etiquetas más negritas */
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 0.5rem; /* Bordes redondeados para inputs y selects */
        padding: 0.75rem 1rem;
    }

    .btn {
        border-radius: 0.5rem; /* Bordes redondeados para botones */
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #1e7e34;
        border-color: #1e7e34;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-danger:hover {
        background-color: #bd2130;
        border-color: #bd2130;
    }

    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    .table {
        border-radius: 0.75rem;
        overflow: hidden; /* Asegura que los bordes redondeados se apliquen al contenido */
    }

    .table thead {
        background-color: #343a40; /* Fondo oscuro para el encabezado de la tabla */
        color: white;
    }

    .table th, .table td {
        vertical-align: middle; /* Alineación vertical en el medio */
        padding: 1rem;
    }

    .table tbody tr:hover {
        background-color: #e2e6ea; /* Efecto hover en filas */
    }

    .alert {
        border-radius: 0.5rem;
        padding: 1.25rem 1.5rem;
        font-size: 1.05rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Clases de texto personalizadas */
    .text-gray-800 {
        color: #343a40 !important;
    }
    .text-primary {
        color: #007bff !important;
    }
    .text-info {
        color: #17a2b8 !important;
    }
    .text-success {
        color: #28a745 !important;
    }
</style>
{{-- Para animaciones sutiles, podrías añadir Animate.css si lo deseas --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/> --}}

@endsection

@section('scripts')
<script>
    let detalleContador = 0; // Renombrado para evitar confusión con `total`
    let totalGeneral = 0; // Renombrado para mayor claridad

    document.getElementById('btn-agregar').addEventListener('click', function () {
        const productoSelect = document.getElementById('producto_id');
        const productoId = productoSelect.value;
        // Obtener solo el nombre del producto, eliminando el precio y el stock
        const productoNombreCompleto = productoSelect.options[productoSelect.selectedIndex].text;
        const productoNombre = productoNombreCompleto.split(' - ')[0].trim();

        const precio = parseFloat(productoSelect.options[productoSelect.selectedIndex].getAttribute('data-precio'));
        const cantidadInput = document.getElementById('cantidad');
        const cantidad = parseInt(cantidadInput.value);

        // Validación de inputs y manejo de errores
        if (!productoId) {
            alert('Por favor, seleccione un producto.');
            productoSelect.classList.add('is-invalid');
            return;
        } else {
            productoSelect.classList.remove('is-invalid');
        }

        if (isNaN(cantidad) || cantidad <= 0) {
            alert('La cantidad debe ser un número positivo.');
            cantidadInput.classList.add('is-invalid');
            return;
        } else {
            cantidadInput.classList.remove('is-invalid');
        }

        // Sin validación de stock: el producto no maneja stock en el modelo actual

        const subtotal = precio * cantidad;
        totalGeneral += subtotal;
        document.getElementById('total-general').innerText = `$${totalGeneral.toFixed(2)}`;

        const tablaBody = document.querySelector('#tabla-productos tbody');
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>${productoNombre}</td>
            <td class="text-center">${cantidad}</td>
            <td class="text-end">$${precio.toFixed(2)}</td>
            <td class="text-end"><strong>$${subtotal.toFixed(2)}</strong></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>

            <input type="hidden" name="productos[${detalleContador}][producto_id]" value="${productoId}">
            <input type="hidden" name="productos[${detalleContador}][cantidad]" value="${cantidad}">
            <input type="hidden" name="productos[${detalleContador}][precio_unitario]" value="${precio}">
        `;

        tablaBody.appendChild(fila);
        detalleContador++; // Incremento para el atributo `name` de la siguiente fila

        // Restablecer inputs
        productoSelect.selectedIndex = 0;
        cantidadInput.value = 1;
    });

    // Eliminar producto del carrito
    document.querySelector('#tabla-productos').addEventListener('click', function (e) {
        if (e.target.classList.contains('eliminar') || e.target.closest('.eliminar')) {
            const fila = e.target.closest('tr');
            const subtotalText = fila.children[3].innerText;
            // Eliminar el signo de dólar y parsear como float
            const subtotal = parseFloat(subtotalText.replace('$', ''));

            totalGeneral -= subtotal;
            document.getElementById('total-general').innerText = `$${totalGeneral.toFixed(2)}`;
            fila.remove();
        }
    });

    // Auto-cerrar alertas
    document.addEventListener('DOMContentLoaded', function() {
        const alertSuccess = document.querySelector('.alert-success');
        if (alertSuccess) {
            setTimeout(() => {
                alertSuccess.style.transition = 'opacity 0.5s ease';
                alertSuccess.style.opacity = '0';
                setTimeout(() => {
                    alertSuccess.remove();
                }, 500);
            }, 5000); // 5 segundos
        }

        const alertDanger = document.querySelector('.alert-danger');
        if (alertDanger) {
            setTimeout(() => {
                alertDanger.style.transition = 'opacity 0.5s ease';
                alertDanger.style.opacity = '0';
                setTimeout(() => {
                    alertDanger.remove();
                }, 500);
            }, 7000); // 7 segundos
        }
    });
</script>
@endsection
