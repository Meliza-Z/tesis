@extends('layouts.app')

@section('content')
    @php($producto = $producto ?? new \App\Models\Producto())
    @include('productos.form')
@endsection
