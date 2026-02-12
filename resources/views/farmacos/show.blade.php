@extends('adminlte::page')

@section('title', $farmaco->descripcion)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">
                <i class="fas fa-capsules text-primary"></i>
                {{ strtoupper($farmaco->descripcion) }}
                @if($farmaco->controlado)
                    <span class="badge badge-warning ml-2" style="font-size: 14px;">
                        <i class="fas fa-lock"></i> Controlado
                    </span>
                @endif
            </h1>
            <p class="text-muted mb-0 mt-1">
                {{ $farmaco->forma_farmaceutica }} &mdash; {{ $farmaco->dosis }}
            </p>
        </div>
        <div>
            <a href="{{ route('farmacos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                <a href="{{ route('farmacos.edit', $farmaco) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
            <a href="{{ route('historial.farmaco', $farmaco) }}" class="btn btn-outline-info">
                <i class="fas fa-history"></i> Historial completo
            </a>
        </div>
    </div>
@endsection

@section('content')
    @include('components.sweetalert')

    {{-- ============================================ --}}
    {{-- FILA 1: Tarjetas resumen (KPIs) --}}
    {{-- ============================================ --}}
    <div class="row mb-4">
        {{-- Stock actual --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box {{ $stockFisico <= 0 ? 'bg-danger' : ($stockFisico < $farmaco->stock_minimo * 0.25 ? 'bg-warning' : 'bg-success') }}">
                <div class="inner">
                    <h3>{{ $stockFisico }}<sup style="font-size:18px"> / {{ $farmaco->stock_minimo }}</sup></h3>
                    <p>Stock Total</p>
                </div>
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <div class="small-box-footer" style="padding: 6px; font-size: 12px;">
                    <i class="fas fa-clinic-medical"></i> Farmacia: <strong>{{ $stockFarmacia }}</strong>
                    &nbsp;|&nbsp;
                    <i class="fas fa-hospital"></i> Áreas: <strong>{{ $stockAreas }}</strong>
                    @if($farmaco->stock_minimo > 0)
                        @php $pct = min(($stockFisico / $farmaco->stock_minimo) * 100, 100); @endphp
                        <div class="progress progress-sm mt-1" style="height: 5px; background: rgba(255,255,255,0.3);">
                            <div class="progress-bar bg-white" style="width: {{ $pct }}%"></div>
                        </div>
                        <span>{{ number_format($pct, 0) }}% capacidad</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lotes activos --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $lotesDisponibles->count() }}<sup style="font-size:18px"> / {{ $totalLotes }}</sup></h3>
                    <p>Lotes Disponibles</p>
                </div>
                <div class="icon"><i class="fas fa-cubes"></i></div>
                <a href="{{ route('lotes.index', ['farmaco_id' => $farmaco->id]) }}" class="small-box-footer">
                    Ver en gestión de lotes <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Lotes vencidos --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box {{ $lotesVencidos->count() > 0 ? 'bg-danger' : 'bg-gradient-secondary' }}">
                <div class="inner">
                    <h3>{{ $lotesVencidos->count() }}</h3>
                    <p>Lotes Vencidos</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-times"></i></div>
                <div class="small-box-footer" style="padding: 8px;">
                    @if($lotesVencidos->count() > 0)
                        <i class="fas fa-exclamation-triangle"></i> Requieren atención
                    @else
                        <i class="fas fa-check"></i> Sin vencidos
                    @endif
                </div>
            </div>
        </div>

        {{-- Movimientos --}}
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $stats['total_movimientos'] }}</h3>
                    <p>Movimientos Totales</p>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
                <a href="{{ route('historial.farmaco', $farmaco) }}" class="small-box-footer">
                    Ver historial <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILA 2: Info general + Áreas asignadas --}}
    {{-- ============================================ --}}
    <div class="row mb-4">
        {{-- Información general --}}
        <div class="col-lg-5 col-md-12 mb-3">
            <div class="card card-primary card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 40%;"><i class="fas fa-hashtag text-muted mr-1"></i> ID</th>
                                <td>{{ $farmaco->id }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-pills text-muted mr-1"></i> Descripción</th>
                                <td class="font-weight-bold text-uppercase">{{ $farmaco->descripcion }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-prescription-bottle text-muted mr-1"></i> Forma Farmacéutica</th>
                                <td>{{ $farmaco->forma_farmaceutica }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-weight text-muted mr-1"></i> Dosis</th>
                                <td>{{ $farmaco->dosis }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-layer-group text-muted mr-1"></i> Stock Máximo</th>
                                <td><span class="badge badge-primary" style="font-size: 14px;">{{ $farmaco->stock_minimo }}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-shield-alt text-muted mr-1"></i> Controlado</th>
                                <td>
                                    @if($farmaco->controlado)
                                        <span class="badge badge-warning"><i class="fas fa-lock"></i> Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-plus text-muted mr-1"></i> Registrado</th>
                                <td>{{ $farmaco->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-sync text-muted mr-1"></i> Última actualización</th>
                                <td>{{ $farmaco->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Indicadores de flujo + Áreas --}}
        <div class="col-lg-7 col-md-12 mb-3">
            <div class="row mb-3">
                <div class="col-4">
                    <div class="info-box mb-0">
                        <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Recibido</span>
                            <span class="info-box-number">{{ $stats['total_recibido'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-box mb-0">
                        <span class="info-box-icon bg-info"><i class="fas fa-truck"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Despachado</span>
                            <span class="info-box-number">{{ $stats['total_despachado'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-box mb-0">
                        <span class="info-box-icon bg-warning"><i class="fas fa-arrow-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Salidas</span>
                            <span class="info-box-number">{{ $stats['total_salidas'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Áreas asignadas --}}
            <div class="card card-outline card-info h-100" style="min-height: 0;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hospital-alt"></i> Áreas Asignadas &amp; Stock</h3>
                </div>
                <div class="card-body">
                    @if($farmaco->areas->isEmpty())
                        <p class="text-muted text-center my-3"><i class="fas fa-unlink"></i> Sin áreas asignadas</p>
                    @else
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Área</th>
                                    <th class="text-center">Stock en área</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($farmaco->areas as $area)
                                    @php $stockArea = $stockPorArea[$area->nombre_area] ?? 0; @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-clinic-medical text-primary mr-1"></i>
                                            {{ strtoupper($area->nombre_area) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $stockArea > 0 ? 'badge-success' : 'badge-secondary' }}" style="font-size: 13px;">
                                                {{ $stockArea }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td><i class="fas fa-clinic-medical text-info mr-1"></i> Farmacia Central</td>
                                    <td class="text-center">
                                        <span class="badge badge-info" style="font-size: 13px;">{{ $stockFarmacia }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILA 3: Tabla de Lotes --}}
    {{-- ============================================ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cubes"></i> Lotes del Fármaco
                        <span class="badge badge-info ml-2">{{ $totalLotes }} total</span>
                    </h3>
                    <div class="card-tools">
                        @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                            <a href="{{ route('lotes.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Lote
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($farmaco->lotes->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>No hay lotes registrados para este fármaco</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="tabla-lotes">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th>Serie</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Días Restantes</th>
                                        <th>Cantidad Total</th>
                                        <th>Disponible</th>
                                        <th>Uso</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($farmaco->lotes->sortBy('fecha_vencimiento') as $lote)
                                        @php
                                            $diasRestantes = now()->startOfDay()->diffInDays($lote->fecha_vencimiento->startOfDay(), false);
                                            $usoPct = $lote->cantidad > 0 ? (($lote->cantidad - $lote->cantidad_disponible) / $lote->cantidad) * 100 : 0;
                                        @endphp
                                        <tr class="{{ $lote->vencido ? 'table-danger' : ($diasRestantes <= 30 && $diasRestantes >= 0 ? 'table-warning' : '') }}">
                                            <td class="text-center font-weight-bold">{{ $lote->num_serie }}</td>
                                            <td class="text-center">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                @if($lote->vencido || $diasRestantes < 0)
                                                    <span class="badge badge-danger">Vencido hace {{ abs($diasRestantes) }}d</span>
                                                @elseif($diasRestantes <= 30)
                                                    <span class="badge badge-warning">{{ $diasRestantes }}d</span>
                                                @elseif($diasRestantes <= 90)
                                                    <span class="badge badge-info">{{ $diasRestantes }}d</span>
                                                @else
                                                    <span class="text-success">{{ $diasRestantes }}d</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $lote->cantidad }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $lote->cantidad_disponible > 0 ? 'success' : 'secondary' }}" style="font-size: 13px;">
                                                    {{ $lote->cantidad_disponible }}
                                                </span>
                                            </td>
                                            <td style="min-width: 120px;">
                                                <div class="progress progress-sm" style="height: 8px;" title="{{ number_format($usoPct, 0) }}% utilizado">
                                                    <div class="progress-bar {{ $usoPct > 80 ? 'bg-danger' : ($usoPct > 50 ? 'bg-warning' : 'bg-info') }}"
                                                         style="width: {{ $usoPct }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($usoPct, 0) }}% usado</small>
                                            </td>
                                            <td class="text-center">
                                                @if($lote->vencido)
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Vencido</span>
                                                @elseif($lote->cantidad_disponible <= 0)
                                                    <span class="badge badge-secondary"><i class="fas fa-box-open"></i> Agotado</span>
                                                @elseif($diasRestantes <= 30)
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Por vencer</span>
                                                @else
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Vigente</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('lotes.show', $lote) }}" class="btn btn-info btn-xs" title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                                                    <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-warning btn-xs" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILA 4: Salidas recientes + Timeline movimientos --}}
    {{-- ============================================ --}}
    <div class="row mb-4">
        {{-- Salidas recientes --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="card card-outline card-warning h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sign-out-alt"></i> Últimas Salidas</h3>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    @if($salidasRecientes->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Sin salidas registradas</p>
                        </div>
                    @else
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>DAU</th>
                                    <th>Lotes</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salidasRecientes as $salida)
                                    <tr>
                                        <td class="small">{{ $salida->fecha_salida }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-warning">{{ $salida->cantidad_salida }}</span>
                                        </td>
                                        <td class="small">{{ $salida->numero_dau ?? '—' }}</td>
                                        <td class="small">
                                            @foreach($salida->lotes as $loteSalida)
                                                <span class="badge badge-light" title="Cantidad: {{ $loteSalida->pivot->cantidad }}">
                                                    {{ $loteSalida->num_serie }} ({{ $loteSalida->pivot->cantidad }})
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="small">{{ $salida->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Timeline de últimos movimientos --}}
        <div class="col-lg-6 col-md-12 mb-3">
            <div class="card card-outline card-purple h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-stream"></i> Últimos Movimientos</h3>
                    <div class="card-tools">
                        <a href="{{ route('historial.farmaco', $farmaco) }}" class="btn btn-tool" title="Ver todos">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    @if($farmaco->movimientos->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>Sin movimientos registrados</p>
                        </div>
                    @else
                        <div class="timeline timeline-inverse p-3">
                            @foreach($farmaco->movimientos->sortByDesc('fecha')->take(10) as $mov)
                                @php
                                    switch($mov->tipo) {
                                        case 'despacho':   $icon = 'fas fa-truck';       $color = 'bg-info';    break;
                                        case 'recepcion':  $icon = 'fas fa-arrow-down';   $color = 'bg-success'; break;
                                        case 'salida':     $icon = 'fas fa-arrow-up';     $color = 'bg-warning'; break;
                                        default:           $icon = 'fas fa-exchange-alt';  $color = 'bg-secondary';
                                    }
                                @endphp
                                <div>
                                    <i class="{{ $icon }} {{ $color }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i>
                                            {{ $mov->fecha ? $mov->fecha->format('d/m/Y H:i') : $mov->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <h3 class="timeline-header">
                                            <span class="badge badge-{{ $mov->tipo === 'despacho' ? 'info' : ($mov->tipo === 'recepcion' ? 'success' : 'warning') }}">
                                                {{ ucfirst($mov->tipo) }}
                                            </span>
                                            &nbsp;{{ $mov->cantidad }} unidades
                                        </h3>
                                        @if($mov->descripcion)
                                            <div class="timeline-body small">
                                                {{ $mov->descripcion }}
                                                @if($mov->lote)
                                                    <br><span class="text-muted">Lote: {{ $mov->lote->num_serie }}</span>
                                                @endif
                                                @if($mov->area)
                                                    <br><span class="text-muted">Área: {{ $mov->area->nombre_area }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div><i class="fas fa-clock bg-gray"></i></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('plugins.Datatables', true)

@section('css')
    <style>
        .small-box .inner h3 sup {
            font-weight: 300;
        }
        .timeline {
            margin: 0;
            padding: 10px;
        }
        .timeline > div > .timeline-item {
            margin-left: 50px;
            margin-right: 10px;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .info-box {
            min-height: 80px;
        }
        .info-box .info-box-icon {
            width: 60px;
            height: 60px;
            line-height: 60px;
            font-size: 22px;
        }
    </style>
@endsection

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            @if($farmaco->lotes->count() > 5)
                $("#tabla-lotes").DataTable({
                    paging: true,
                    pageLength: 10,
                    order: [[1, 'asc']],
                    language: {
                        "processing": "Procesando...",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No se encontraron resultados",
                        "emptyTable": "Sin lotes",
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ lotes",
                        "infoEmpty": "Sin registros",
                        "infoFiltered": "(filtrado de _MAX_ total)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero", "last": "Último",
                            "next": "Siguiente", "previous": "Anterior"
                        }
                    }
                });
            @endif
        });
    </script>
@endsection
