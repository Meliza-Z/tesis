@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Cliente</h2>
    @include('clientes.form', ['cliente' => $cliente])
</div>
@endsection
