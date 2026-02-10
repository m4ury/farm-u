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
                                    ->placeholder('' . $area->stock_fisico) }}
                                @if ($errors->has('cantidad_salida'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('cantidad_salida') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
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
