@extends('adminlte::page')

@section('title', 'Historial por Fármaco - ' . $farmaco->descripcion)

@section('content_header')
    <h1>Historial de Movimientos - {{ $farmaco->descripcion }}</h1>
    <p class="text-muted">Dosis: {{ $farmaco->dosis }}</p>
@endsection

@section('content')
    @include('components.sweetalert')

    <!-- Estadísticas del Fármaco -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Despachado</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo', 'despacho')->sum('cantidad') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-download"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Recibido</span>
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
                    <span class="info-box-text">Total Salidas</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo', 'salida')->sum('cantidad') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Movimientos</span>
                    <span class="info-box-number">{{ $movimientos->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Stock -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado Actual de Stock</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Stock Máximo:</strong>
                                <br>
                                <span class="badge badge-primary badge-lg">{{ $farmaco->stock_maximo }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Stock Actual:</strong>
                                <br>
                                <span class="badge badge-{{ $farmaco->stock_fisico > $farmaco->stock_maximo / 2 ? 'success' : 'danger' }} badge-lg">
                                    {{ $farmaco->stock_fisico }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribución por Área</h3>
                </div>
                <div class="card-body">
                    @if($farmaco->areas->isEmpty())
                        <p class="text-muted">No está asignado a ningún área actualmente.</p>
                    @else
                        <ul>
                            @foreach($farmaco->areas as $area)
                                <li>
                                    <a href="{{ route('historial.area', $area) }}">{{ $area->nombre_area }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Historial de Movimientos</h3>
        </div>

        <div class="card-body">
            @if($movimientos->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> No hay movimientos registrados para este fármaco.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Lote</th>
                                <th>Área</th>
                                <th>Cantidad</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr class="
                                    @switch($movimiento->tipo)
                                        @case('entrada') table-success @break
                                        @case('despacho') table-info @break
                                        @case('recepcion') table-success @break
                                        @case('salida') table-warning @break
                                        @case('ajuste') table-secondary @break
                                    @endswitch
                                ">
                                    <td>
                                        <small>{{ $movimiento->fecha->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @switch($movimiento->tipo)
                                            @case('entrada')
                                                <span class="badge badge-success">Entrada</span>
                                                @break
                                            @case('despacho')
                                                <span class="badge badge-info">Despacho</span>
                                                @break
                                            @case('recepcion')
                                                <span class="badge badge-success">Recepción</span>
                                                @break
                                            @case('salida')
                                                <span class="badge badge-warning">Salida</span>
                                                @break
                                            @case('ajuste')
                                                <span class="badge badge-secondary">Ajuste</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($movimiento->lote)
                                            <a href="{{ route('lotes.show', $movimiento->lote) }}">
                                                {{ $movimiento->lote->num_serie }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movimiento->area)
                                            <a href="{{ route('historial.area', $movimiento->area) }}">
                                                {{ $movimiento->area->nombre_area }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $movimiento->cantidad }}</strong>
                                    </td>
                                    <td>{{ $movimiento->usuario->name ?? '-' }}</td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($movimiento->descripcion, 50) }}</small>
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
