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
                <form method="POST" action={{ route('salidas.store') }}>
                    @csrf
                    {!! Form::hidden('id', $area->id) !!}
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {!! Form::label('dau_label', 'Numero DAU o Receta: ', ['class' => 'col-sm-4 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::text('numero_dau', old('numero_dau'), [
                                    'class' => 'form-control form-control-sm' . ($errors->has('numero_dau') ? 'is-invalid' : ''),
                                    'placeholder' => 'Num DAU urgencias / receta',
                                ]) !!}
                                @if ($errors->has('numero_dau'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('numero_dau') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {!! Form::label('cantidad_salida_label', 'Cantidad a rebajar: ', ['class' => 'col-sm-4 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::number('cantidad_salida', null, [
                                    'class' => 'form-control form-control-sm' . ($errors->has('cantidad_salida') ? 'is-invalid' : ''),
                                    'placeholder' => '' . $area->stock_fisico,
                                    /* 'placeholder' => 'Nº ultima ficha creada ' . Str::replace(['{', '}', '"ficha"'], '', $paciente->ultFicha()->last()), */
                                ]) !!}
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
                            {{ Form::submit('Guardar', ['class' => 'btn bg-gradient-success btn-sm btn-block']) }}
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-sm btn-block"
                                data-dismiss="modal">Cancelar</button>
                        </div>
                        <!-- Otros botones de acción -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
