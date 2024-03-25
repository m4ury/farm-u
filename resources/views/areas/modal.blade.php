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
                <form method="POST" action={{ route('areas.store') }}>
                    @csrf
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {!! Form::label('nombre_area_label', 'Nombre area: ', ['class' => 'col-sm-4 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::text('nombre_area', old('nombre_area'), [
                                    'class' => 'form-control form-control-sm' . ($errors->has('nombre_area') ? 'is-invalid' : ''),
                                    'placeholder' => 'ej.: Urgencias',
                                ]) !!}
                                @if ($errors->has('nombre_area'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('nombre_area') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {!! Form::label('descripcion_area_label', 'Descripcion area: ', ['class' => 'col-sm-4 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::textarea('descripcion_area', old('descripcion_area'), [
                                    'class' => 'form-control form-control-sm' . ($errors->has('descripcion_area') ? 'is-invalid' : ''),
                                    'placeholder' => 'opcional, breve descripcion',
                                ]) !!}
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
                                {{ Form::submit('Guardar', ['class' => 'btn bg-gradient-success btn-sm btn-block']) }}
                            </div>
                            <div class="col">
                                <div class="col">
                                    <button type="button" class="btn bg-gradient-secondary btn-sm btn-block"
                                        data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
