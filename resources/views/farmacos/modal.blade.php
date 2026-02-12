<div class="modal fade py-3" id="new-farmaco" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Farmaco </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- {{ Form::open(['action' => route('farmacos.store'), 'method' => 'POST', 'class' => 'form-horizontal']) }} --}}
                {{ html()->form('POST', route('farmacos.store'))->open() }}
                    @csrf
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {{ html()->label('Descripción: ', 'descripcion')->class('col-sm-2 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->text('descripcion')
                                    ->value(old('descripcion'))
                                    ->class('form-control form-control-sm' . ($errors->has('descripcion') ? ' is-invalid' : ''))
                                    ->placeholder('ej.: paracetamol') }}
                                @if ($errors->has('descripcion'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {{ html()->label('Forma farmaceutica: ', 'forma_farmaceutica')->class('col-sm-2 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->select('forma_farmaceutica', [
                                        'comprimidos' => 'Comprimidos',
                                        'Solucion inyectable' => 'Solucion inyectable',
                                        'Supositorio' => 'Supositorio',
                                        'Capsula' => 'Capsula',
                                        'Polvo para suspension oral' => 'Polvo para suspension oral',
                                        'Polvo para suspension inyectable' => 'Polvo para suspension inyectable',
                                        'Aerosol para inhalacion' => 'Aerosol para inhalacion',
                                        'Solucion oftalmica' => 'Solucion oftalmica',
                                        'Unguentooftalmico' => 'Unguentooftalmico',
                                        'Capsula o comprimido' => 'Capsula o comprimido',
                                        'Supositorio infantil' => 'Supositorio infantil',
                                        'Solucion para gotas' => 'Solucion para gotas',
                                        'Solucion laxante rectal' => 'Solucion laxante rectal',
                                        'Solucion para nebulizacion' => 'Solucion para nebulizacion',
                                        'Frasco ampolla + solvente' => 'Frasco ampolla + solvente',
                                        'Unidad' => 'Unidad',
                                    ])
                                    ->value(old('forma_farmaceutica'))
                                    ->class('form-control form-control-sm' . ($errors->has('forma_farmaceutica') ? ' is-invalid' : ''))
                                    ->placeholder('seleccione')
                                    ->id('forma') }}
                                @if ($errors->has('forma_farmaceutica'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('forma_farmaceutica') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            {{ html()->label('Dosis: ', 'dosis')->class('col-sm-2 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->text('dosis')
                                    ->value(old('dosis'))
                                    ->class('form-control form-control-sm' . ($errors->has('dosis') ? ' is-invalid' : ''))
                                    ->placeholder('ej.: 100 mg') }}
                                @if ($errors->has('dosis'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('dosis') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {{ html()->label('Controlado: ', 'controlado')->class('col-sm-2 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->checkbox('controlado', old('controlado'))
                                    ->value(1)
                                    ->class('form-control my-2 controlado')
                                    ->id('controlado') }}
                                @if ($errors->has('controlado'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('controlado') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            {{ html()->label('Stock maximo: ', 'stock_minimo')->class('col-sm-2 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->number('stock_minimo')
                                    ->value(old('stock_minimo'))
                                    ->class('form-control form-control-sm' . ($errors->has('stock_minimo') ? ' is-invalid' : '')) }}
                                @if ($errors->has('stock_minimo'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('stock_minimo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row py-3 px-3">
                            <div class="col">
                                {{ html()->submit('Guardar')->class('btn bg-gradient-success btn-sm btn-block') }}
                            </div>
                            <div class="col">
                                {{-- <a href="{{ url()->previous() }}" style="text-decoration:none">
                                    {{ html()->button('Cancelar')->class('btn bg-gradient-secondary btn-sm btn-block') }}
                                </a> --}}
                                {{ html()
                                    ->button('Cancelar')
                                    ->class('btn bg-gradient-secondary btn-sm btn-block')
                                    ->type('button')
                                    ->attribute('data-dismiss', 'modal') }}
                            </div>
                        </div>
                    </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>
