@extends('layouts.app')

@section('content')
    @include('pagos.form', [
        'route' => route('pagos.store'),
        'method' => 'POST',
        'pago' => new \App\Models\Pago(),
        'creditos' => $creditos,
    ])
@endsection
