@extends('layouts.app')

@section('content')
    @include('pagos.form', ['route' => route('pagos.update', $pago), 'method' => 'PUT', 'pago' => $pago])
@endsection