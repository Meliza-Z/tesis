<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Créditos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- ¡¡AGREGADO: TOKEN CSRF ESENCIAL PARA ELIMINAR!! --}}

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome para iconos (versión 6) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex; /* Mantengo display flex en body para que sidebar y main-content-wrapper se distribuyan bien */
            font-family: Arial, sans-serif; /* Fuente por defecto */
        }

        .sidebar {
            width: 250px; /* Ancho fijo para el sidebar */
            background-color: #343a40;
            color: white;
            flex-shrink: 0; /* Evita que el sidebar se encoja */
            position: fixed; /* Mantiene el sidebar fijo en la pantalla */
            top: 0;
            left: 0;
            height: 100vh; /* Ocupa toda la altura de la ventana */
            overflow-y: auto; /* Permite scroll si hay muchos elementos en el sidebar */
            z-index: 1030; /* Asegura que el sidebar esté por encima del contenido */
            transition: width 0.3s ease; /* Para futuras expansiones/contracciones */
        }

        .sidebar h4 {
            margin: 0;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .sidebar a {
            color: white;
            display: flex; /* Usar flex para alinear icono y texto */
            align-items: center; /* Centrar verticalmente */
            padding: 12px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar a i {
            margin-right: 10px; /* Espacio entre icono y texto */
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar a.active {
            background-color: #198754; /* Verde de Bootstrap */
            font-weight: bold;
        }

        /* Contenedor principal del contenido */
        .main-content-wrapper {
            margin-left: 250px; /* Empuja el contenido principal para que no se superponga con el sidebar */
            flex-grow: 1; /* Permite que el contenido ocupe el resto del ancho disponible */
            min-height: 100vh; /* Para que el fondo ocupe toda la altura */
            background-color: #f8f9fa; /* Fondo del área de contenido */
            display: flex; /* Para que el contenido interno pueda usar flexbox si se necesita */
            flex-direction: column; /* Organiza los elementos internos en columna */
        }

        /* Estilos para el contenido real de la página */
        .content-area {
            flex-grow: 1; /* Permite que el contenido ocupe el espacio disponible */
            padding: 20px; /* Padding interno para el contenido */
        }

        /* Media queries para responsividad */
        @media (max-width: 768px) { /* Para pantallas más pequeñas (tablets y móviles) */
            .sidebar {
                width: 0; /* Oculta el sidebar por defecto */
                overflow-x: hidden; /* Oculta el scroll horizontal */
                /* Considera añadir un botón para abrir/cerrar el sidebar en móvil si se desea */
            }

            .main-content-wrapper {
                margin-left: 0; /* El contenido ocupa todo el ancho */
                width: 100%;
            }
        }
    </style>

    {{-- Puedes añadir una sección para estilos específicos de cada vista si lo necesitas --}}
    @yield('styles')
</head>
<body>

    <div class="sidebar">
        <h4 class="text-center">D'Margarita's</h4>
        <br><br><br><br>
        <a href="{{ route('clientes.index') }}" class="{{ Request::is('clientes*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Clientes
        </a>
        <a href="{{ route('productos.index') }}" class="{{ Request::is('productos*') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> Productos
        </a>
        {{-- <a href="{{ route('creditos.index') }}" class="{{ Request::is('creditos*') ? 'active' : '' }}">
            <i class="fas fa-handshake"></i> Créditos
        </a> --}}
        <a href="{{ route('detalle_credito.index') }}" class="{{ Request::is('detalle_credito*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i> Detalles Créditos
        </a>
        <a href="{{ route('pagos.index') }}" class="{{ Request::is('pagos*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> Pagos
        </a>
        <a href="{{ route('cuenta_cobrar.index') }}" class="{{ Request::is('cuenta_cobrar*') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign"></i> Cuentas por Cobrar
        </a>
        <a href="{{ route('reportes.index') }}" class="{{ Request::is('reportes*') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Reportes
        </a>
        
        {{-- Información del usuario y logout --}}
        <div style="position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 0;">
            @auth
                <div class="text-center" style="padding: 10px; color: #adb5bd; font-size: 0.9rem;">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                </div>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endauth
        </div>
    </div>

    <div class="main-content-wrapper">
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    {{-- Bootstrap JS Bundle (popper.js incluido) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts específicos de cada vista --}}
    @yield('scripts')
</body>
</html>