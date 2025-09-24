@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Cliente</h2>
    @include('clientes.form', ['cliente' => new \App\Models\Cliente])
</div>
@endsection
