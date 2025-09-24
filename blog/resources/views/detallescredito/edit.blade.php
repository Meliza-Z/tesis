@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Detalle de Crédito #{{ $detalle_credito->id }}</h1>

    <form action="{{ route('detalle_credito.update', $detalle_credito) }}" method="POST">
        @method('PUT')
        @include('detallescredito.form')
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('detalle_credito.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
