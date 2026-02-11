@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">

        <!-- Tarjetas Resumen General -->
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalFarmacos }}</h3>
                        <p>Fármacos Registrados</p>
                    </div>
                    <div class="icon"><i class="fas fa-pills"></i></div>
                    <a href="{{ route('farmacos.index') }}" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stockTotal }}</h3>
                        <p>Stock Total Disponible</p>
                    </div>
                    <div class="icon"><i class="fas fa-boxes"></i></div>
                    <a href="{{ route('lotes.index') }}" class="small-box-footer">Ver lotes <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pedidosPendientes->count() }}</h3>
                        <p>Pedidos Pendientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                    <a href="{{ route('pedidos.index') }}" class="small-box-footer">Ver pedidos <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $proximosVencer->count() + $lotesVencidos->count() }}</h3>
                        <p>Alertas de Vencimiento</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="{{ route('lotes.index') }}" class="small-box-footer">Ver lotes <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Indicadores secundarios -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lotes Activos</span>
                        <span class="info-box-number">{{ $totalLotesActivos }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-olive"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pedidos Aprobados</span>
                        <span class="info-box-number">{{ $pedidosAprobados }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Despachos del Mes</span>
                        <span class="info-box-number">{{ $despachosMes }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pedidos Hoy</span>
                        <span class="info-box-number">{{ $pedidosHoy }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Áreas -->
        <div class="row mb-4">
            <div class="col-sm-12">
                <h4 class="mb-3">
                    <i class="fas fa-hospital-alt text-primary"></i> Resumen por Áreas
                </h4>
            </div>
            @foreach($areas as $area)
                @php
                    $stockArea = $area->farmacos->sum(function($farmaco) {
                        return $farmaco->getStockFisicoCalculado();
                    });
                    $stockMaxArea = $area->farmacos->sum('stock_maximo');
                    $areaSlug = $areaSlugMapping[$area->nombre_area] ?? null;
                @endphp
                <div class="col-lg col-md col-sm mb-3">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3>{{ $area->farmacos->count() }}</h3>
                            <p class="text-bold text-uppercase">{{ $area->nombre_area }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="small-box-footer" style="padding: 8px; font-size: 12px;">
                            <strong>Stock:</strong> {{ $stockArea }}/{{ $stockMaxArea }}
                            @if($stockMaxArea > 0)
                                <div class="progress progress-sm mt-1" style="height: 4px;">
                                    <div class="progress-bar {{ ($stockArea/$stockMaxArea)*100 < 25 ? 'bg-danger' : (($stockArea/$stockMaxArea)*100 < 50 ? 'bg-warning' : 'bg-success') }}"
                                         style="width: {{ min(($stockArea/$stockMaxArea)*100, 100) }}%"></div>
                                </div>
                            @endif
                        </div>
                        @if($areaSlug)
                            <a href="{{ route('areas.show', $areaSlug) }}" class="small-box-footer" style="padding: 10px;">
                                Ver detalle <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Alertas Críticas: Vencidos y Próximos a Vencer -->
        @if($lotesVencidos->count() > 0 || $proximosVencer->count() > 0)
        <div class="row mb-4">
            <!-- Lotes Vencidos -->
            @if($lotesVencidos->count() > 0)
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-danger card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-times-circle text-danger"></i> Lotes Vencidos con Stock ({{ $lotesVencidos->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Medicamento</th>
                                        <th class="text-center">Lote</th>
                                        <th class="text-center">Venció</th>
                                        <th class="text-center">Cant.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lotesVencidos as $lote)
                                        <tr>
                                            <td class="small">{{ $lote->farmaco->descripcion ?? 'N/A' }}</td>
                                            <td class="text-center small">{{ $lote->num_serie }}</td>
                                            <td class="text-center small text-danger font-weight-bold">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td class="text-center"><span class="badge badge-danger">{{ $lote->cantidad_disponible }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Próximos a Vencer -->
            @if($proximosVencer->count() > 0)
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-warning card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-exclamation-triangle text-warning"></i> Lotes Próximos a Vencer ({{ $proximosVencer->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Medicamento</th>
                                        <th class="text-center">Lote</th>
                                        <th class="text-center">Vence</th>
                                        <th class="text-center">Días</th>
                                        <th class="text-center">Cant.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximosVencer as $lote)
                                        @php $diasRestantes = now()->diffInDays($lote->fecha_vencimiento, false); @endphp
                                        <tr>
                                            <td class="small">{{ $lote->farmaco->descripcion ?? 'N/A' }}</td>
                                            <td class="text-center small">{{ $lote->num_serie }}</td>
                                            <td class="text-center small">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $diasRestantes <= 7 ? 'badge-danger' : ($diasRestantes <= 15 ? 'badge-warning' : 'badge-info') }}">
                                                    {{ $diasRestantes }}d
                                                </span>
                                            </td>
                                            <td class="text-center"><span class="badge badge-secondary">{{ $lote->cantidad_disponible }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Pedidos Pendientes y Despachos Pendientes de Recepción -->
        <div class="row mb-4">
            <!-- Pedidos Pendientes -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-warning card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-clipboard-list text-warning"></i> Pedidos Pendientes ({{ $pedidosPendientes->count() }})
                        </h5>
                        <div class="card-tools">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-tool"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        @if($pedidosPendientes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Área</th>
                                            <th>Solicitante</th>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pedidosPendientes as $pedido)
                                            <tr>
                                                <td class="small">{{ $pedido->id }}</td>
                                                <td class="small">{{ $pedido->area->nombre_area ?? 'N/A' }}</td>
                                                <td class="small">{{ $pedido->solicitante ?? ($pedido->user->name ?? 'N/A') }}</td>
                                                <td class="text-center small">{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-xs btn-outline-primary" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> No hay pedidos pendientes
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Despachos Pendientes de Recepción -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-info card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-truck text-info"></i> Despachos Pendientes de Recepción ({{ $despachosPendientesRecepcion->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        @if($despachosPendientesRecepcion->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th>Área Destino</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($despachosPendientesRecepcion as $despacho)
                                            <tr>
                                                <td class="small">{{ $despacho->lote->farmaco->descripcion ?? 'N/A' }}</td>
                                                <td class="small">{{ $despacho->pedido->area->nombre_area ?? 'N/A' }}</td>
                                                <td class="text-center"><span class="badge badge-info">{{ $despacho->cantidad }}</span></td>
                                                <td class="text-center small">{{ $despacho->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('despachos.confirmarForm', $despacho) }}" class="btn btn-xs btn-outline-success" title="Confirmar recepción">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> Todos los despachos han sido recibidos
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bajo Stock y Controlados -->
        <div class="row mb-4">
            <!-- Bajo Stock -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-danger card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-arrow-down text-danger"></i> Bajo Stock ({{ $bajoStock->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        @if($bajoStock->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th class="text-center">Stock Actual</th>
                                            <th class="text-center">Stock Máx.</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bajoStock as $farmaco)
                                            @php
                                                $stockActual = $farmaco->getStockFisicoCalculado();
                                                $porcentaje = $farmaco->stock_maximo > 0 ? round(($stockActual / $farmaco->stock_maximo) * 100) : 0;
                                            @endphp
                                            <tr>
                                                <td class="small">{{ $farmaco->descripcion }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $stockActual == 0 ? 'badge-dark' : 'badge-danger' }}">{{ $stockActual }}</span>
                                                </td>
                                                <td class="text-center small">{{ $farmaco->stock_maximo }}</td>
                                                <td class="text-center">
                                                    <div class="progress progress-sm" style="height: 8px; width: 60px; display: inline-block;">
                                                        <div class="progress-bar bg-danger" style="width: {{ $porcentaje }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $porcentaje }}%</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> Todos los fármacos tienen stock adecuado
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Controlados -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-info card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-lock"></i> Controlados ({{ $controlados->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        @if($controlados->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th class="text-center" style="width: 80px;">Dosis</th>
                                            <th class="text-center" style="width: 80px;">Stock</th>
                                            <th class="text-center" style="width: 60px;">Lotes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($controlados as $medicamento)
                                            <tr>
                                                <td class="align-middle small">{{ $medicamento->descripcion }}</td>
                                                <td class="text-center small">{{ $medicamento->dosis }}</td>
                                                <td class="text-center"><span class="badge badge-info">{{ $medicamento->getStockFisicoCalculado() }}</span></td>
                                                <td class="text-center small">{{ $medicamento->lotes->where('vencido', false)->count() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info m-3 mb-0">
                                <i class="fas fa-info-circle"></i> Sin medicamentos controlados
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Mayor Stock y Mayor Salida -->
        <div class="row mb-4">
            <!-- Top 5 Mayor Stock -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-success card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-sort-amount-up text-success"></i> Top 5 Mayor Stock Disponible
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($mayorStock->count() > 0)
                            @foreach($mayorStock as $farmaco)
                                @php
                                    $stockCalc = $farmaco->getStockFisicoCalculado();
                                    $porcentaje = $farmaco->stock_maximo > 0 ? round(($stockCalc / $farmaco->stock_maximo) * 100) : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small font-weight-bold" title="{{ $farmaco->descripcion }}">{{ Str::limit($farmaco->descripcion, 35) }}</span>
                                        <span class="badge badge-success">{{ $stockCalc }}</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" style="width: {{ min($porcentaje, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $porcentaje }}% del máximo ({{ $farmaco->stock_maximo }})</small>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Sin datos de stock
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top 5 Mayor Salida -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-primary card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-arrow-up text-primary"></i> Top 5 Mayor Salida
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($mayorSalida->count() > 0)
                            <div class="row">
                                @foreach($mayorSalida as $medicamento)
                                    <div class="col-12 mb-2">
                                        <div class="info-box bg-light mb-0">
                                            <span class="info-box-icon bg-primary">
                                                <i class="fas fa-cubes"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text small font-weight-bold" title="{{ $medicamento->descripcion }}">
                                                    {{ Str::limit($medicamento->descripcion, 30) }}
                                                </span>
                                                <span class="info-box-number" style="font-size: 16px;">
                                                    {{ $medicamento->salidas_sum_cantidad_salida ?? 0 }}
                                                </span>
                                                <span class="text-muted small">unidades retiradas</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Sin registros de salida
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Despachos Recientes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-history text-primary"></i> Últimos Despachos Realizados
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($despachosRecientes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Medicamento</th>
                                            <th>Lote</th>
                                            <th>Área Destino</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Aprobado por</th>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($despachosRecientes as $despacho)
                                            <tr>
                                                <td class="small">{{ $despacho->lote->farmaco->descripcion ?? 'N/A' }}</td>
                                                <td class="small">{{ $despacho->lote->num_serie ?? 'N/A' }}</td>
                                                <td class="small">{{ $despacho->pedido->area->nombre_area ?? 'N/A' }}</td>
                                                <td class="text-center"><span class="badge badge-primary">{{ $despacho->cantidad }}</span></td>
                                                <td class="small">{{ $despacho->usuarioAprobador->name ?? 'N/A' }}</td>
                                                <td class="text-center small">{{ $despacho->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="text-center">
                                                    @if($despacho->estaRecibido())
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Recibido</span>
                                                    @else
                                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info m-3">
                                <i class="fas fa-info-circle"></i> No hay despachos recientes
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('js')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
