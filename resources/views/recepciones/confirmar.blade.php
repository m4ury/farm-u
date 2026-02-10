@extends('adminlte::page')

@section('title', 'Confirmar Recepción')

@section('content_header')
    <h1>Confirmar Recepción de Despacho</h1>
@endsection

@section('content')
    @include('components.sweetalert')

    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Despacho</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Fármaco:</strong>
                                <br>
                                <span class="badge badge-info">
                                    {{ $despacho->lote->farmaco->descripcion }}
                                </span>
                            </p>
                            <p>
                                <strong>Lote:</strong>
                                <br>
                                {{ $despacho->lote->num_serie }}
                            </p>
                            <p>
                                <strong>Área Origen:</strong>
                                <br>
                                {{ $despacho->area->nombre_area }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p>
                                <strong>Cantidad Despachada:</strong>
                                <br>
                                <span class="badge badge-success badge-lg">
                                    {{ $despacho->cantidad }} unidades
                                </span>
                            </p>
                            <p>
                                <strong>Fecha Despacho:</strong>
                                <br>
                                {{ $despacho->fecha_aprobacion->format('d/m/Y H:i') }}
                            </p>
                            <p>
                                <strong>Despachado por:</strong>
                                <br>
                                {{ $despacho->usuarioAprobador->fullUserName() }}
                            </p>
                        </div>
                    </div>

                    @if($despacho->observaciones)
                        <div class="alert alert-info">
                            <strong>Observaciones:</strong>
                            <br>
                            {{ $despacho->observaciones }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Confirmar Recepción</h3>
                </div>

                <form action="{{ route('despachos.confirmar', $despacho) }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Cantidad Recibida <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="cantidad_recibida"
                                   class="form-control {{ $errors->has('cantidad_recibida') ? 'is-invalid' : '' }}"
                                   value="{{ old('cantidad_recibida', $despacho->cantidad) }}"
                                   min="1"
                                   max="{{ $despacho->cantidad }}"
                                   required>
                            <small class="text-muted">
                                Máximo: {{ $despacho->cantidad }} unidades
                            </small>
                            @error('cantidad_recibida')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Observaciones</label>
                            <textarea name="observaciones"
                                      class="form-control {{ $errors->has('observaciones') ? 'is-invalid' : '' }}"
                                      rows="3"
                                      placeholder="Ej: Falta 1 unidad, llegó dañada...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Confirmar Recepción
                        </button>
                        <a href="{{ route('recepciones.historialArea') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <div class="alert alert-warning alert-sm">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Si la cantidad recibida es menor que la despachada, se ajustará automáticamente el stock.
            </div>
        </div>
    </div>
@endsection
