@extends('adminlte::page')

@section('title', 'Ver Pedido')

@section('content_header')
    <h1>Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p>
                                <strong>Fecha:</strong>
                                <span class="badge badge-info">{{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
                            </p>
                            <p>
                                <strong>Área:</strong>
                                {{ $pedido->area->nombre_area }}
                            </p>
                            <p>
                                <strong>Usuario Responsable:</strong>
                                <span class="badge badge-secondary">{{ $pedido->user->fullUserName() }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Estado:</strong>
                                @if($pedido->estado === 'solicitado')
                                    <span class="badge badge-warning">Solicitado</span>
                                @elseif($pedido->estado === 'entregado')
                                    <span class="badge badge-success">Entregado</span>
                                @else
                                    <span class="badge badge-danger">Rechazado</span>
                                @endif
                            </p>
                            <p>
                                <strong>Solicitante:</strong>
                                {{ $pedido->solicitante ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($pedido->observaciones)
                        <div class="mb-3">
                            <p><strong>Observaciones:</strong></p>
                            <div class="alert alert-info">
                                {{ $pedido->observaciones }}
                            </div>
                        </div>
                    @endif

                    <hr>

                    <h5>Fármacos Solicitados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fármaco</th>
                                    <th>Forma Farmacéutica</th>
                                    <th>Dosis</th>
                                    <th>Cantidad Pedida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->farmacos as $farmaco)
                                    <tr>
                                        <td>{{ $farmaco->descripcion }}</td>
                                        <td>{{ $farmaco->forma_farmaceutica }}</td>
                                        <td>{{ $farmaco->dosis }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $farmaco->pivot->cantidad_pedida }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    {{ html()->form('DELETE', route('pedidos.destroy', $pedido))->class('d-inline')->open() }}
                        @csrf
                        {{ html()->button('<i class="fas fa-trash"></i> Eliminar')->class('btn btn-danger')->type('submit')->attribute('onclick', "return confirm('¿Está seguro de que desea eliminar este pedido?')") }}
                    {{ html()->form()->close() }}
                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h3 class="card-title">Información del Pedido</h3>
                </div>
                <div class="card-body">
                    <p>
                        <strong>ID del Pedido:</strong><br>
                        <code>#{{ $pedido->id }}</code>
                    </p>
                    <p>
                        <strong>Creado:</strong><br>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p>
                        <strong>Actualizado:</strong><br>
                        {{ $pedido->updated_at->format('d/m/Y H:i') }}
                    </p>
                    <hr>
                    <p>
                        <strong>Total de Fármacos:</strong><br>
                        <span class="badge badge-primary">{{ $pedido->farmacos->count() }}</span>
                    </p>
                    <p>
                        <strong>Cantidad Total:</strong><br>
                        <span class="badge badge-info">
                            {{ $pedido->farmacos->sum(function($farmaco) { return $farmaco->pivot->cantidad_pedida; }) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
