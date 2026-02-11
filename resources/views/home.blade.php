@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Sección de Áreas -->
        <div class="row mb-4">
            <div class="col-sm-12">
                <h4 class="mb-3">
                    <i class="fas fa-hospital-alt text-primary"></i> Resumen por Áreas
                </h4>
            </div>
            @foreach($areas as $area)
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
                            <strong>Stock:</strong> 0/0
                        </div>
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


        <!-- Sección de Información Adicional -->
        <div class="row mb-4">
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
                                                <td class="text-center"><span class="badge badge-info">{{ $medicamento->getStockFisicoCalculado() }}</span></td>
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
