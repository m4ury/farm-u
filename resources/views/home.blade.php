@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Sección de Áreas -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">
                    <i class="fas fa-hospital-alt text-primary"></i> Resumen por Áreas
                </h4>
            </div>
            @foreach($areas as $area)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3>{{ $area->farmacos->count() }}</h3>
                            <p class="text-bold text-uppercase">{{ $area->nombre_area }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <!-- <div class="small-box-footer" style="padding: 8px; font-size: 12px;">
                            <strong>Stock:</strong> {{ $area->farmacos->pluck('stock_fisico')->sum() }}/{{ $area->farmacos->pluck('stock_maximo')->sum() }}
                        </div> -->
                        @php
                            $areaSlug = $areaSlugMapping[$area->nombre_area] ?? null;
                        @endphp
                        @if($areaSlug)
                            <a href="{{ route('areas.show', $areaSlug) }}" class="small-box-footer" style="padding: 10px;">
                                Ver detalle <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Sección de Alertas Críticas -->
        <div class="row mb-4">
            <!-- Dashboard Bajo Stock -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-danger card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <!-- <i class="fas fa-exclamation-triangle"></i> Bajo Stock () -->
                        </h5>
                    </div>
                    <!-- <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        @if(bajo->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th class="text-center" style="width: 80px;">Actual</th>
                                            <th class="text-center" style="width: 80px;">Máximo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bajo as $medicamento)
                                            <tr>
                                                <td class="align-middle">{{ $medicamento->descripcion }}</td>
                                                <td class="text-center"><span class="badge badge-danger">{{ $medicamento->stock_fisico }}</span></td>
                                                <td class="text-center">{{ $medicamento->stock_maximo }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> Stock adecuado en todos
                            </div>
                        @endif
                    </div> -->
                </div>
            </div>

            <!-- Dashboard Próximos a Vencer -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-warning card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-calendar-times"></i> Próximos a Vencer ({{ $proximosVencer->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                        @if($proximosVencer->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th class="text-center" style="width: 100px;">Vencimiento</th>
                                            <th class="text-center" style="width: 60px;">Días</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($proximosVencer as $medicamento)
                                            <tr>
                                                <td class="align-middle">{{ $medicamento->descripcion }}</td>
                                                <td class="text-center small">{{ \Carbon\Carbon::parse($medicamento->fecha_vencimiento)->format('d/m/Y') }}</td>
                                                <td class="text-center"><span class="badge badge-warning">{{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($medicamento->fecha_vencimiento)) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> Todos con vigencia correcta
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Información Adicional -->
        <div class="row mb-4">
            <!-- Dashboard Medicamentos Vencidos -->
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card card-danger card-outline h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-times-circle"></i> Vencidos ({{ $vencidos->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        @if($vencidos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th class="text-center" style="width: 100px;">Vencimiento</th>
                                            <th class="text-center" style="width: 80px;">Área</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vencidos as $medicamento)
                                            <tr class="table-danger">
                                                <td class="align-middle">{{ $medicamento->descripcion }}</td>
                                                <td class="text-center small">{{ \Carbon\Carbon::parse($medicamento->fecha_vencimiento)->format('d/m/Y') }}</td>
                                                <td class="text-center small">{{ $medicamento->areas->pluck('nombre_area')->first() ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success m-3 mb-0">
                                <i class="fas fa-check-circle"></i> Sin medicamentos vencidos
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dashboard Medicamentos Controlados -->
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
                                            <th class="text-center" style="width: 60px;">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($controlados as $medicamento)
                                            <tr>
                                                <td class="align-middle small">{{ $medicamento->descripcion }}</td>
                                                <td class="text-center small">{{ $medicamento->dosis }}</td>
                                                <td class="text-center"><span class="badge badge-info">{{ $medicamento->stock_fisico }}</span></td>
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

        <!-- Top Stock -->
        <!-- <div class="row">
            <div class="col-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-chart-line"></i> Top 5 Medicamentos con Mayor Stock
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($mayorStock->count() > 0)
                            <div class="row">
                                @foreach($mayorStock as $medicamento)
                                    <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-pills"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text small font-weight-bold" title="{{ $medicamento->descripcion }}">
                                                    {{ Str::limit($medicamento->descripcion, 20) }}
                                                </span>
                                                <span class="info-box-number" style="font-size: 18px;">
                                                    {{ $medicamento->stock_fisico }}/{{ $medicamento->stock_maximo }}
                                                </span>
                                                <div class="progress progress-sm" style="margin-top: 5px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ ($medicamento->stock_fisico / $medicamento->stock_maximo) * 100 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay medicamentos registrados
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Mayor Salida -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">
                            <i class="fas fa-arrow-up"></i> Top 5 Medicamentos con Mayor Salida
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($mayorSalida->count() > 0)
                            <div class="row">
                                @foreach($mayorSalida as $medicamento)
                                    <div class="col-lg-2-4 col-md-4 col-sm-6 mb-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary">
                                                <i class="fas fa-cubes"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text small font-weight-bold" title="{{ $medicamento->descripcion }}">
                                                    {{ Str::limit($medicamento->descripcion, 20) }}
                                                </span>
                                                <span class="info-box-number" style="font-size: 18px;">
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
    </div>
@endsection

@section('plugins.Datatables', true)

@section('js')
    <script>
        $(document).ready(function() {
            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
