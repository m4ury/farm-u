{{-- filepath: resources/views/errors/unauthorized.blade.php --}}
@extends('adminlte::page')

@section('content')
    <div class="container text-center">
        <h1 class="text-danger">Acceso Denegado</h1>
        <p>{{ $message ?? 'No tienes permiso para acceder a esta página.' }}</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary">Volver</a>
    </div>
@endsection
