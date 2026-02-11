<div class="modal fade" id="productModal{{ $area->id }}" tabindex="-1" role="dialog"
    aria-labelledby="productModalLabel{{ $area->id }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="productModalLabel{{ $area->id }}">Detalles del farmaco</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Aquí puedes mostrar los detalles específicos del producto -->
                {{ html()->form('POST', route('salidas.store'))->open() }}
                    @csrf
                    {{ html()->hidden('id', $area->id) }}
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {{ html()->label('Numero DAU o Receta: ', 'numero_dau')->class('col-sm-4 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->text('numero_dau')
                                    ->value(old('numero_dau'))
                                    ->class('form-control form-control-sm' . ($errors->has('numero_dau') ? ' is-invalid' : ''))
                                    ->placeholder('Num DAU urgencias / receta')
                                    ->required() }}
                                @if ($errors->has('numero_dau'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('numero_dau') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ html()->label('Cantidad a rebajar: ', 'cantidad_salida')->class('col-sm-4 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->number('cantidad_salida')
                                    ->value(old('cantidad_salida'))
                                    ->class('form-control form-control-sm' . ($errors->has('cantidad_salida') ? ' is-invalid' : ''))
                                    ->placeholder('' . $area->getStockFisicoCalculado()) }}
                                @if ($errors->has('cantidad_salida'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('cantidad_salida') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $lotesDisponibles = $area->lotesDisponibles()->get();
                            $lotesVencidos = $area->lotesVencidos()->get();
                        @endphp

                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="col-form-label mb-0">Lotes disponibles (FIFO)</label>
                                <button type="button" class="btn btn-outline-info btn-xs" data-action="sugerir-lotes" data-farmaco-id="{{ $area->id }}">
                                    Sugerir lotes
                                </button>
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Lote</th>
                                            <th>Vence</th>
                                            <th>Disponible</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lotesDisponibles as $lote)
                                            <tr>
                                                <td>{{ $lote->num_serie }}</td>
                                                <td>{{ $lote->fecha_vencimiento?->format('d-m-Y') }}</td>
                                                <td>{{ $lote->cantidad_disponible }}</td>
                                                <td style="width: 120px;">
                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        name="lotes[{{ $lote->id }}]"
                                                        min="0"
                                                        max="{{ $lote->cantidad_disponible }}"
                                                        data-lote-disponible="{{ $lote->cantidad_disponible }}"
                                                        data-lote-orden="{{ $loop->index }}"
                                                        data-farmaco-id="{{ $area->id }}"
                                                        value="0">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-muted text-center">Sin lotes disponibles</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if ($lotesVencidos->isNotEmpty())
                            <div class="alert alert-warning py-2">
                                <strong>Atencion:</strong> Hay lotes vencidos. No se pueden usar en una salida.
                            </div>
                        @endif
                    </div>
                    <!-- Otros detalles del producto -->
                    <!-- Puedes agregar un pie de página con botones de acción si es necesario -->
                    <div class="modal-footer">
                        <div class="col">
                            {{ html()->submit('Guardar')->class('btn bg-gradient-success btn-sm btn-block') }}
                        </div>
                        <div class="col">
                            {{ html()
                                ->button('Cancelar')
                                ->class('btn btn-secondary btn-sm btn-block')
                                ->type('button')
                                ->attribute('data-dismiss', 'modal') }}
                        </div>
                        <!-- Otros botones de acción -->
                    </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>
