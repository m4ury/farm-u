@extends('adminlte::page')

@section('title', 'Reporte - ' . ucfirst($tipo))

@section('content_header')
    <h1>Reporte de Movimientos - {{ ucfirst($tipo) }}</h1>
    <p class="text-muted">Período: {{ $fecha_desde }} a {{ $fecha_hasta }}</p>
@endsection

@section('content')
    @include('components.sweetalert')

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filtros del Reporte</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('historial.reportePorTipo') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Movimiento</label>
                        <select name="tipo" class="form-control select2-tipo">
                            <option value="entrada" {{ $tipo == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ $tipo == 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="despacho" {{ $tipo == 'despacho' ? 'selected' : '' }}>Despacho</option>
                            <option value="recepcion" {{ $tipo == 'recepcion' ? 'selected' : '' }}>Recepción</option>
                            <option value="devolucion" {{ $tipo == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                            <option value="ajuste" {{ $tipo == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ $fecha_desde }}" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ $fecha_hasta }}" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-3 btn-block">
                        <i class="fas fa-search"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas del Reporte -->
    @if(!$movimientos->isEmpty())
        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $movimientos->count() }}</h3>
                        <p>Total de Movimientos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $movimientos->sum('cantidad') }}</h3>
                        <p>Total de Unidades</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cube"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $movimientos->groupBy('farmaco_id')->count() }}</h3>
                        <p>Fármacos Involucrados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $movimientos->groupBy('area_id')->count() }}</h3>
                        <p>Áreas Involucradas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas por Fármaco -->
    @if(!$estadisticas->isEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top 10 Fármacos</h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Fármaco</th>
                                <th>Dosis</th>
                                <th>Total Unidades</th>
                                <th>Movimientos</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estadisticas->take(10) as $stat)
                                <tr>
                                    <td>
                                        <a href="{{ route('historial.farmaco', $stat['farmaco']) }}">
                                            {{ $stat['farmaco']->descripcion }}
                                        </a>
                                    </td>
                                    <td>{{ $stat['farmaco']->dosis }}</td>
                                    <td>
                                        <strong>{{ $stat['total_cantidad'] }}</strong>
                                    </td>
                                    <td>{{ $stat['total_movimientos'] }}</td>
                                    <td>
                                        {{ number_format($stat['total_cantidad'] / $stat['total_movimientos'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla de Movimientos Detallados -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimientos Detallados</h3>
            <div class="card-tools">
                <a href="{{ route('historial.exportar', ['tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]) }}" 
                   class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> Exportar CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($movimientos->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> No hay movimientos del tipo "{{ ucfirst($tipo) }}" en el período especificado.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Fármaco</th>
                                <th>Lote</th>
                                <th>Área</th>
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
                                        <a href="{{ route('historial.farmaco', $movimiento->farmaco) }}">
                                            {{ $movimiento->farmaco->descripcion }}
                                        </a>
                                    </td>
                                    <td>{{ $movimiento->lote->num_serie ?? '-' }}</td>
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
                                        <small class="text-muted">{{ $movimiento->descripcion }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('plugins.Select2', true)

@section('js')
<script>
    $(document).ready(function() {
        $('.select2-tipo').select2({
            theme: 'bootstrap4',
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endsection
