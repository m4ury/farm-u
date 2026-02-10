@extends('adminlte::page')

@section('title', 'Rechazar Pedido')

@section('content_header')
    <h1>Rechazar Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title">Rechazar Pedido</h3>
                </div>
                <div class="card-body">

                    <!-- Información del Pedido -->
                    <div class="alert alert-info">
                        <h5>Información del Pedido</h5>
                        <p><strong>ID:</strong> {{ $pedido->id }}</p>
                        <p><strong>Fecha:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                        <p><strong>Área:</strong> {{ $pedido->area->nombre ?? 'N/A' }}</p>
                        <p><strong>Solicitante:</strong> {{ $pedido->solicitante ?? 'N/A' }}</p>
                    </div>

                    <form action="{{ route('pedidos.rechazar', $pedido) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="motivo_rechazo"><strong>Motivo del Rechazo *</strong></label>
                            <textarea id="motivo_rechazo" 
                                      name="motivo_rechazo" 
                                      class="form-control @error('motivo_rechazo') is-invalid @enderror"
                                      rows="6" 
                                      placeholder="Indique claramente el motivo por el que se rechaza este pedido..."
                                      required>{{ old('motivo_rechazo') }}</textarea>
                            @error('motivo_rechazo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Mínimo 10 caracteres</small>
                        </div>

                        <div class="alert alert-warning">
                            <strong>⚠️ Advertencia:</strong> Al rechazar este pedido, se notificará al solicitante con el motivo indicado.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Rechazar Pedido
                            </button>
                            <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
