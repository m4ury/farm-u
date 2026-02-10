@extends('adminlte::page')

@section('title', 'Historial por Área - ' . $area->nombre_area)

@section('content_header')
    <h1>Historial de Movimientos - {{ $area->nombre_area }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')

    <!-- Estadísticas de la Área -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Despachos Recibidos</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo', 'recepcion')->count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-cube"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Unidades Recibidas</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo', 'recepcion')->sum('cantidad') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Salidas</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo', 'salida')->sum('cantidad') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Movimientos</span>
                    <span class="info-box-number">{{ $movimientos->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimientos de Inventario</h3>
            <div class="card-tools">
                <a href="{{ route('historial.movimientos', ['area_id' => $area->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> Ver en Historial General
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($movimientos->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> No hay movimientos registrados para esta área.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Fármaco</th>
                                <th>Lote</th>
                                <th>Cantidad</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>
                                        <small>{{ $movimiento->fecha->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @switch($movimiento->tipo)
                                            @case('recepcion')
                                                <span class="badge badge-success">Recepción</span>
                                                @break
                                            @case('salida')
                                                <span class="badge badge-warning">Salida</span>
                                                @break
                                            @case('devolucion')
                                                <span class="badge badge-info">Devolución</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($movimiento->tipo) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('historial.farmaco', $movimiento->farmaco) }}">
                                            {{ $movimiento->farmaco->descripcion }}
                                        </a>
                                    </td>
                                    <td>{{ $movimiento->lote->num_serie ?? '-' }}</td>
                                    <td class="text-center">
                                        <strong>{{ $movimiento->cantidad }}</strong>
                                    </td>
                                    <td>{{ $movimiento->usuario->name ?? '-' }}</td>
                                    <td>
                                        <small class="text-muted">{{ $movimiento->descripcion }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
