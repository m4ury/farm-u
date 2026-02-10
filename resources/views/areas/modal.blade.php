<div class="modal fade py-3" id="new-area" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Area </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fas-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                {{-- {{ Form::open(['action' => route('farmacos.store'), 'method' => 'POST', 'class' => 'form-horizontal']) }} --}}
                {{ html()->form('POST', route('areas.store'))->open() }}
                    @csrf
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {{ html()->label('Nombre area: ', 'nombre_area')->class('col-sm-4 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->text('nombre_area')
                                    ->value(old('nombre_area'))
                                    ->class('form-control form-control-sm' . ($errors->has('nombre_area') ? ' is-invalid' : ''))
                                    ->placeholder('ej.: Urgencias') }}
                                @if ($errors->has('nombre_area'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('nombre_area') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ html()->label('Descripcion area: ', 'descripcion_area')->class('col-sm-4 col-form-label') }}
                            <div class="col-sm">
                                {{ html()
                                    ->textarea('descripcion_area')
                                    ->value(old('descripcion_area'))
                                    ->class('form-control form-control-sm' . ($errors->has('descripcion_area') ? ' is-invalid' : ''))
                                    ->placeholder('opcional, breve descripcion') }}
                                @if ($errors->has('descripcion_area'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('descripcion_area') }}</strong>
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
                                <div class="col">
                                    {{ html()
                                        ->button('Cancelar')
                                        ->class('btn bg-gradient-secondary btn-sm btn-block')
                                        ->type('button')
                                        ->attribute('data-dismiss', 'modal') }}
                                </div>
                            </div>
                        </div>
                    </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>
