@extends('adminlte::page')

@section('title', 'Historial de Movimientos')

@section('content_header')
    <h1>Historial de Movimientos de Inventario</h1>
@endsection

@section('content')
    @include('components.sweetalert')

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('historial.movimientos') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Movimiento</label>
                        <select name="tipo" class="form-control select2-tipo">
                            <option value="">Todos</option>
                            <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="despacho" {{ request('tipo') == 'despacho' ? 'selected' : '' }}>Despacho</option>
                            <option value="recepcion" {{ request('tipo') == 'recepcion' ? 'selected' : '' }}>Recepción</option>
                            <option value="devolucion" {{ request('tipo') == 'devolucion' ? 'selected' : '' }}>Devolución</option>
                            <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fármaco</label>
                        <select name="farmaco_id" class="form-control select2-farmaco">
                            <option value="">Todos</option>
                            @foreach($farmacos as $farmaco)
                                <option value="{{ $farmaco->id }}" {{ request('farmaco_id') == $farmaco->id ? 'selected' : '' }}>
                                    {{ $farmaco->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Área</label>
                        <select name="area_id" class="form-control select2-area">
                            <option value="">Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre_area }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-3 btn-block">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="row mt-3">
                <div class="col-md-3">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Fecha desde">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Fecha hasta">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('historial.exportar', request()->query()) }}" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Exportar CSV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('historial.movimientos') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimientos ({{ $movimientos->total() }} registros)</h3>
        </div>

        <div class="card-body">
            @if($movimientos->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No hay movimientos registrados con los filtros aplicados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
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
                                <tr class="
                                    @switch($movimiento->tipo)
                                        @case('entrada') table-success @break
                                        @case('salida') table-warning @break
                                        @case('despacho') table-info @break
                                        @case('recepcion') table-success @break
                                        @case('devolucion') table-warning @break
                                        @case('ajuste') table-secondary @break
                                    @endswitch
                                ">
                                    <td><small>{{ $movimiento->fecha->format('d/m/Y H:i') }}</small></td>
                                    <td>
                                        @switch($movimiento->tipo)
                                            @case('entrada')
                                                <span class="badge badge-success">Entrada</span>
                                                @break
                                            @case('salida')
                                                <span class="badge badge-warning">Salida</span>
                                                @break
                                            @case('despacho')
                                                <span class="badge badge-info">Despacho</span>
                                                @break
                                            @case('recepcion')
                                                <span class="badge badge-success">Recepción</span>
                                                @break
                                            @case('devolucion')
                                                <span class="badge badge-warning">Devolución</span>
                                                @break
                                            @case('ajuste')
                                                <span class="badge badge-secondary">Ajuste</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td><strong>{{ $movimiento->farmaco->descripcion }}</strong></td>
                                    <td>{{ $movimiento->lote->num_serie ?? '-' }}</td>
                                    <td>{{ $movimiento->area->nombre_area ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $movimiento->cantidad }}</span>
                                    </td>
                                    <td>{{ $movimiento->usuario->name ?? '-' }}</td>
                                    <td>
                                        <small>{{ Str::limit($movimiento->descripcion, 40) }}</small>
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

@section('plugins.Select2', true)

@section('js')
<script>
    $(document).ready(function() {
        $('.select2-tipo').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Todos',
            allowClear: true,
            minimumResultsForSearch: Infinity
        });
        $('.select2-farmaco').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Todos',
            allowClear: true
        });
        $('.select2-area').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Todas',
            allowClear: true,
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endsection
