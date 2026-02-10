@extends('adminlte::page')

@section('title', 'Historial de Recepción - Despachos')

@section('content_header')
    <h1>Historial de Despachos Recibidos</h1>
    <p class="text-muted">Área: <strong>{{ Auth::user()->area->nombre_area ?? 'Admin' }}</strong></p>
@endsection

@section('content')
    @include('components.sweetalert')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Despachos Confirmados</h3>
        </div>

        <div class="card-body">
            @if($recepciones->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> No hay despachos recibidos aún.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Despacho ID</th>
                                <th>Fármaco</th>
                                <th>Lote</th>
                                <th>Cantidad Despachada</th>
                                <th>Cantidad Recibida</th>
                                <th>Usuario Recibidor</th>
                                <th>Fecha Recepción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recepciones as $recepcion)
                                <tr>
                                    <td>
                                        <a href="{{ route('pedidos.show', $recepcion->despacho->pedido) }}">
                                            #{{ $recepcion->despacho->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $recepcion->despacho->lote->farmaco->descripcion }}
                                        </span>
                                    </td>
                                    <td>{{ $recepcion->despacho->lote->num_serie }}</td>
                                    <td class="text-center">
                                        {{ $recepcion->despacho->cantidad }}
                                    </td>
                                    <td class="text-center">
                                        @if($recepcion->cantidad_recibida == $recepcion->despacho->cantidad)
                                            <span class="badge badge-success">{{ $recepcion->cantidad_recibida }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $recepcion->cantidad_recibida }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $recepcion->usuario->name }}
                                    </td>
                                    <td>
                                        <small>{{ $recepcion->fecha_recepcion->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Recibido
                                        </span>
                                    </td>
                                    <td>
                                        @if($recepcion->observaciones)
                                            <button class="btn btn-sm btn-info" 
                                                    data-toggle="tooltip" 
                                                    title="{{ $recepcion->observaciones }}">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('pedidos.show', $recepcion->despacho->pedido) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver Pedido
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $recepciones->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $recepciones->total() }}</h3>
                    <p>Despachos Recibidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $recepciones->sum(function($r) { return $r->cantidad_recibida; }) }}</h3>
                    <p>Total Unidades Recibidas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>
                        {{
                            $recepciones->filter(function($r) { 
                                return $r->cantidad_recibida < $r->despacho->cantidad; 
                            })->count()
                        }}
                    </h3>
                    <p>Con Diferencias</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        {{
                            $recepciones->filter(function($r) { 
                                return !empty($r->observaciones); 
                            })->count()
                        }}
                    </h3>
                    <p>Con Observaciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
