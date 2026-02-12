@extends('adminlte::page')

@section('title', 'Detalle Receta / DAU')

@section('content')
    <div class="container-fluid my-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <a class="btn bg-gradient-info btn-sm mr-3" title="Volver" href="{{ route('recetas.index') }}">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                        Volver
                    </a>
                    <i class="fas fa-file-prescription px-2" style="color:rgb(38, 0, 255)"></i>
                    RECETA / DAU: <strong>{{ $receta->numero_dau }}</strong>
                </h3>
            </div>
            <div class="card-body">
                {{-- Info de la Receta --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-hashtag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Número DAU / Receta</span>
                                <span class="info-box-number">{{ $receta->numero_dau }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fecha</span>
                                <span class="info-box-number">{{ $receta->fecha_receta->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="fas fa-hospital"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Área</span>
                                <span class="info-box-number">{{ $receta->area->nombre_area ?? 'Farmacia Central' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-danger">
                            <span class="info-box-icon"><i class="fas fa-pills"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Fármacos</span>
                                <span class="info-box-number">{{ $receta->salidas->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($receta->observaciones)
                    <div class="callout callout-info py-2">
                        <strong>Observaciones:</strong> {{ $receta->observaciones }}
                    </div>
                @endif

                <div class="row mb-2">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Creado por: <strong>{{ $receta->user->fullUserName() }}</strong>
                            | <i class="fas fa-clock"></i> {{ $receta->created_at->format('d-m-Y H:i') }}
                        </small>
                    </div>
                </div>

                <hr>

                {{-- Detalle de Fármacos --}}
                <h5 class="text-bold mb-3">
                    <i class="fas fa-list text-primary"></i>
                    Fármacos Dispensados
                </h5>

                @foreach ($receta->salidas as $salida)
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title">
                                <i class="fas fa-pills text-info"></i>
                                <strong>{{ $salida->farmaco->descripcion ?? 'N/A' }}</strong>
                                <small class="text-muted ml-2">
                                    {{ $salida->farmaco->dosis ?? '' }} - {{ $salida->farmaco->forma_farmaceutica ?? '' }}
                                </small>
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-danger badge-lg">
                                    Cantidad: {{ $salida->cantidad_salida }}
                                </span>
                                <span class="badge badge-secondary">
                                    Stock al momento: {{ $salida->stock_actual }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Lote</th>
                                        <th>Cantidad usada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salida->lotes as $lote)
                                        <tr>
                                            <td>{{ $lote->num_serie }}</td>
                                            <td class="text-bold text-danger">{{ $lote->pivot->cantidad }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="callout callout-success py-2">
                            <strong>Total unidades dispensadas:</strong>
                            <span class="text-bold text-danger">{{ $receta->salidas->sum('cantidad_salida') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
