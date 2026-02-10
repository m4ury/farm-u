@extends('adminlte::page')

@section('title', 'Aprobar Pedido')

@section('content_header')
    <h1>Aprobar Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Revisión y Aprobación de Pedido</h3>
                </div>
                <div class="card-body">

                    <!-- Información del Pedido -->
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Pedido</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Fecha Pedido:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                                    <p><strong>Solicitante:</strong> {{ $pedido->solicitante ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Área Solicitante:</strong> {{ $pedido->area->nombre ?? 'N/A' }}</p>
                                    <p><strong>Usuario:</strong> {{ $pedido->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if($pedido->observaciones)
                                <p><strong>Observaciones:</strong> {{ $pedido->observaciones }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Fármacos Solicitados -->
                    <form action="{{ route('pedidos.aprobar', $pedido) }}" method="POST">
                        @csrf

                        <div class="card card-outline card-warning mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Fármacos Solicitados</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="25%">Farmaco</th>
                                                <th width="15%">Pedido</th>
                                                <th width="15%">Stock Disponible</th>
                                                <th width="20%">Cantidad a Aprobar</th>
                                                <th width="25%">Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($farmacos_info as $farmaco_id => $info)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $info['farmaco']->descripcion }}</strong><br>
                                                        <small class="text-muted">{{ $info['farmaco']->forma_farmaceutica }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">{{ $info['cantidad_pedida'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $info['stock_disponible'] > 0 ? 'success' : 'danger' }}">
                                                            {{ $info['stock_disponible'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="aprobaciones[{{ $loop->index }}][farmaco_id]" value="{{ $farmaco_id }}">
                                                        <input type="number" 
                                                               name="aprobaciones[{{ $loop->index }}][cantidad_aprobada]" 
                                                               class="form-control" 
                                                               value="{{ $info['cantidad_aprobada'] ?? $info['cantidad_pedida'] }}"
                                                               min="0"
                                                               max="{{ $info['cantidad_pedida'] }}"
                                                               @if($info['stock_disponible'] == 0) disabled @endif>
                                                    </td>
                                                    <td>
                                                        @if($info['stock_disponible'] < $info['cantidad_pedida'])
                                                            <small class="text-warning">
                                                                ⚠️ Stock insuficiente. Disponible: {{ $info['stock_disponible'] }}
                                                            </small>
                                                        @else
                                                            <small class="text-success">✓ Stock suficiente</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay fármacos en el pedido</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Aprobar Pedido
                            </button>
                            <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
