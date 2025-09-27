@extends('layouts.app')

@section('content')
    @include('productos.form', ['producto' => $producto])
@endsection
