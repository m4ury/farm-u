@extends('adminlte::page')

@section('title', 'Despachar Farmácos')

@section('content_header')
    <h1>Despachar Farmácos - Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Seleccionar Lotes para Despacho</h3>
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
                                    <p><strong>Área Destino:</strong> {{ $pedido->area->nombre ?? 'N/A' }}</p>
                                    <p><strong>Fechade Aprobación:</strong> {{ $pedido->fecha_aprobacion->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Aprobado por:</strong> {{ $pedido->usuarioAprobador->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fármacos para Despachar -->
                    @if($farmacos_despacho->isEmpty())
                        <div class="alert alert-info">
                            ✓ Todos los farmácos de este pedido ya han sido despachados.
                        </div>
                    @else
                        <form action="{{ route('pedidos.despachar', $pedido) }}" method="POST">
                            @csrf

                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Fármacos a Despachar</h3>
                                </div>
                                <div class="card-body">
                                    @php $despacho_index = 0; @endphp
                                    @forelse($farmacos_despacho as $farmaco_id => $info)
                                        <div class="card card-outline card-light mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    {{ $info['farmaco']->descripcion }} 
                                                    <span class="badge badge-info">{{ $info['farmaco']->forma_farmaceutica }}</span>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p><strong>Cantidad Aprobada:</strong> 
                                                            <span class="badge badge-primary">{{ $info['cantidad_aprobada'] }}</span>
                                                        </p>
                                                        <p><strong>Ya Despachado:</strong> 
                                                            <span class="badge badge-info">{{ $info['cantidad_despachada'] }}</span>
                                                        </p>
                                                        <p><strong>Por Despachar:</strong> 
                                                            <span class="badge badge-warning">{{ $info['cantidad_pendiente'] }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-9">
                                                        @if($info['lotes']->isEmpty())
                                                            <div class="alert alert-warning mb-0">
                                                                ⚠️ No hay lotes disponibles de este farmaco
                                                            </div>
                                                        @else
                                                            <div class="lotes-container">
                                                                @forelse($info['lotes'] as $lote)
                                                                    <?php $despacho_index++; ?>
                                                                    <div class="form-group border p-3 mb-2">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <strong>Lote:</strong> {{ $lote->num_serie }}<br>
                                                                                <small class="text-muted">
                                                                                    Vencimiento: {{ $lote->fecha_vencimiento->format('d/m/Y') }}
                                                                                </small><br>
                                                                                <small class="text-success">
                                                                                    Disponible: <strong>{{ $lote->cantidad_disponible }}</strong>
                                                                                </small>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label for="cant_{{ $farmaco_id }}_{{ $lote->id }}">
                                                                                    Cantidad a Despachar:
                                                                                </label>
                                                                                <input type="hidden" 
                                                                                       name="despachos[{{ $despacho_index }}][farmaco_id]" 
                                                                                       value="{{ $farmaco_id }}" 
                                                                                       class="despacho-farmaco-id">
                                                                                <input type="hidden" 
                                                                                       name="despachos[{{ $despacho_index }}][lote_id]" 
                                                                                       value="{{ $lote->id }}" 
                                                                                       class="despacho-lote-id">
                                                                                <input type="number" 
                                                                                       id="cant_{{ $farmaco_id }}_{{ $lote->id }}"
                                                                                       name="despachos[{{ $despacho_index }}][cantidad_despacho]" 
                                                                                       class="form-control despacho-cantidad" 
                                                                                       min="0"
                                                                                       max="{{ $lote->cantidad_disponible }}"
                                                                                       value="0">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div class="alert alert-warning mb-0">
                                                                        No hay lotes disponibles
                                                                    </div>
                                                                @endforelse
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info">
                                            No hay fármacos pendientes de despacho
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="form-group mt-3">
                                <label for="observaciones"><strong>Observaciones</strong></label>
                                <textarea id="observaciones" 
                                          name="observaciones" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="Observaciones adicionales sobre el despacho...">{{ old('observaciones') }}</textarea>
                            </div>

                            <!-- Botones -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-truck"></i> Realizar Despacho
                                </button>
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validación básica antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const cantidades = document.querySelectorAll('input[name*="cantidad_despacho"]');
            let totalDespacho = 0;
            
            cantidades.forEach(input => {
                totalDespacho += parseInt(input.value) || 0;
            });

            if (totalDespacho === 0) {
                e.preventDefault();
                alert('Debe indicar al menos una cantidad a despachar');
            }
        });
    </script>
@endsection
