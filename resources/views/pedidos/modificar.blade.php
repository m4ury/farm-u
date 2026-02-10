@extends('adminlte::page')

@section('title', 'Modificar Pedido')

@section('content_header')
    <h1>Modificar Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Ajustar Cantidades del Pedido</h3>
                </div>
                <div class="card-body">

                    <!-- Información General -->
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Información del Pedido</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> {{ $pedido->id }}</p>
                                    <p><strong>Fecha Solicitud:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                                    <p><strong>Área:</strong> {{ $pedido->area->nombre ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Usuario Solicitante:</strong> {{ $pedido->user->name ?? 'N/A' }}</p>
                                    <p><strong>Estado Actual:</strong> 
                                        <span class="badge badge-{{ $pedido->estado == 'aprobado' ? 'success' : 'warning' }}">
                                            {{ strtoupper($pedido->estado) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fármacos del Pedido -->
                    <form action="{{ route('pedidos.modificar', $pedido) }}" method="POST">
                        @csrf

                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Fármacos - Ajustar Cantidades</h3>
                            </div>
                            <div class="card-body">
                                @if(empty($farmacos_info))
                                    <div class="alert alert-info">
                                        Este pedido no tiene farmácos asociados
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="35%">Farmaco</th>
                                                    <th width="15%">Pedido Actual</th>
                                                    <th width="15%">Stock Disponible</th>
                                                    <th width="20%">Cantidad Nueva</th>
                                                    <th width="15%">Acción</th>
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
                                                            <input type="hidden" 
                                                                   name="modificaciones[{{ $loop->index }}][farmaco_id]" 
                                                                   value="{{ $farmaco_id }}">
                                                            <input type="number" 
                                                                   name="modificaciones[{{ $loop->index }}][cantidad_nueva]" 
                                                                   class="form-control" 
                                                                   value="{{ $info['cantidad_aprobada'] ?? $info['cantidad_pedida'] }}"
                                                                   min="0"
                                                                   max="{{ $info['cantidad_pedida'] + $info['stock_disponible'] }}">
                                                        </td>
                                                        <td>
                                                            @if($info['stock_disponible'] < $info['cantidad_pedida'])
                                                                <small class="text-warning">
                                                                    Stock limitado
                                                                </small>
                                                            @else
                                                                <small class="text-success">✓ OK</small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            Sin farmácos en este pedido
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
