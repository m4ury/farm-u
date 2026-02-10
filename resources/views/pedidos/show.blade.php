@extends('adminlte::page')

@section('title', 'Ver Pedido')

@section('content_header')
    <h1>Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Pedido</h3>
                </div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p>
                                <strong>Fecha Solicitud:</strong>
                                <span class="badge badge-info">{{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
                            </p>
                            <p>
                                <strong>Área:</strong>
                                {{ $pedido->area->nombre_area ?? $pedido->area->nombre ?? 'N/A' }}
                            </p>
                            <p>
                                <strong>Usuario Responsable:</strong>
                                <span class="badge badge-secondary">{{ $pedido->user->fullUserName() ?? $pedido->user->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Estado:</strong>
                                @php
                                    $estado_color = [
                                        'pendiente' => 'warning',
                                        'aprobado' => 'success',
                                        'parcial' => 'info',
                                        'rechazado' => 'danger',
                                        'completado' => 'success'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $estado_color[$pedido->estado] ?? 'secondary' }}">
                                    {{ strtoupper($pedido->estado) }}
                                </span>
                            </p>
                            <p>
                                <strong>Solicitante:</strong>
                                {{ $pedido->solicitante ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($pedido->observaciones)
                        <div class="mb-3">
                            <p><strong>Observaciones Solicitante:</strong></p>
                            <div class="alert alert-info">
                                {{ $pedido->observaciones }}
                            </div>
                        </div>
                    @endif

                    @if($pedido->motivo_rechazo)
                        <div class="mb-3">
                            <p><strong>Motivo de Rechazo:</strong></p>
                            <div class="alert alert-danger">
                                {{ $pedido->motivo_rechazo }}
                            </div>
                        </div>
                    @endif

                    @if($pedido->fecha_aprobacion)
                        <div class="alert alert-success mb-3">
                            <p class="mb-1"><strong>Aprobado por:</strong> {{ $pedido->usuarioAprobador->name ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Fecha Aprobación:</strong> {{ $pedido->fecha_aprobacion->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    <hr>

                    <h5>Fármacos Solicitados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fármaco</th>
                                    <th>Forma Farmacéutica</th>
                                    <th>Pedido</th>
                                    <th>Aprobado</th>
                                    <th>Despachado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->farmacos as $farmaco)
                                    <tr>
                                        <td>{{ $farmaco->descripcion }}</td>
                                        <td>{{ $farmaco->forma_farmaceutica }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $farmaco->pivot->cantidad_pedida }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $farmaco->pivot->cantidad_aprobada ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $farmaco->pivot->cantidad_despachada ?? 0 }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <!-- Botones de Acción según Estado -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                        @if($pedido->estaPendiente())
                            <a href="{{ route('pedidos.aprobarForm', $pedido) }}" class="btn btn-success">
                                <i class="fas fa-check"></i> Aprobar
                            </a>
                            <a href="{{ route('pedidos.rechazarForm', $pedido) }}" class="btn btn-danger">
                                <i class="fas fa-times"></i> Rechazar
                            </a>
                        @endif

                        @if($pedido->estaAprobado() || $pedido->estado == 'parcial')
                            <a href="{{ route('pedidos.despacharForm', $pedido) }}" class="btn btn-warning">
                                <i class="fas fa-truck"></i> Despachar
                            </a>
                            <a href="{{ route('pedidos.modificarForm', $pedido) }}" class="btn btn-info">
                                <i class="fas fa-edit"></i> Modificar
                            </a>
                        @endif
                    @endif

                    @if($pedido->estado !== 'completado' && $pedido->estado !== 'rechazado')
                        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif

                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h3 class="card-title">Resumen del Pedido</h3>
                </div>
                <div class="card-body">
                    <p>
                        <strong>ID del Pedido:</strong><br>
                        <code>#{{ $pedido->id }}</code>
                    </p>
                    <p>
                        <strong>Creado:</strong><br>
                        {{ $pedido->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p>
                        <strong>Actualizado:</strong><br>
                        {{ $pedido->updated_at->format('d/m/Y H:i') }}
                    </p>
                    <hr>
                    <p>
                        <strong>Total de Fármacos:</strong><br>
                        <span class="badge badge-primary">{{ $pedido->farmacos->count() }}</span>
                    </p>
                    <p>
                        <strong>Cantidad Pedida:</strong><br>
                        <span class="badge badge-info">{{ $pedido->getTotalPedido() }}</span>
                    </p>
                    <p>
                        <strong>Cantidad Aprobada:</strong><br>
                        <span class="badge badge-warning">{{ $pedido->getTotalAprobado() ?? '-' }}</span>
                    </p>
                    <p>
                        <strong>Cantidad Despachada:</strong><br>
                        <span class="badge badge-success">{{ $pedido->getTotalDespachado() }}</span>
                    </p>
                </div>
            </div>

            @if($pedido->despachos->count() > 0)
                <div class="card bg-light mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Despachos Realizados</h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($pedido->despachos as $despacho)
                            <div class="mb-2 p-2 border rounded">
                                <p class="mb-1"><strong>Lote:</strong> {{ $despacho->lote->num_serie }}</p>
                                <p class="mb-1"><strong>Cantidad:</strong> {{ $despacho->cantidad }}</p>
                                <small class="text-muted">{{ $despacho->fecha_aprobacion->format('d/m/Y H:i') }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection