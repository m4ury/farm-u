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
                <form method="POST" action={{ route('farmacos.store') }}>
                    @csrf
                    <div class="form-horizontal">
                        <div class="form-group row">
                            {!! Form::label('descripcion_label', 'Descripción: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::text('descripcion', old('descripcion'), [
                                    'class' => 'form-control form-control-sm' . ($errors->has('descripcion') ? 'is-invalid' : ''),
                                    'placeholder' => 'ej.: paracetamol',
                                ]) !!}
                                @if ($errors->has('descripcion'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {!! Form::label('forma_farmaceutica_label', 'Forma farmaceutica: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::select(
                                    'forma_farmaceutica',
                                    [
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
                                    ],
                                    old('forma_farmaceutica'),
                                    [
                                        'class' => 'form-control form-control-sm' . ($errors->has('forma_farmaceutica') ? 'is-invalid' : ''),
                                        'placeholder' => 'seleccione',
                                        'id' => 'forma',
                                    ],
                                ) !!}
                                @if ($errors->has('forma_farmaceutica'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('forma_farmaceutica') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            {!! Form::label('dosis_label', 'Dosis: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::text('dosis', null, [
                                    'class' => 'form-control form-control-sm' . ($errors->has('dosis') ? 'is-invalid' : ''),
                                    'placeholder' => 'ej.: 100 mg',
                                ]) !!}
                                @if ($errors->has('dosis'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('dosis') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {!! Form::label('controlado_label', 'Controlado: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::checkbox('controlado', 1, old('controlado'), [
                                    'class' => 'form-control my-2 controlado',
                                    'id' => 'controlado',
                                ]) !!}
                                @if ($errors->has('controlado'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('controlado') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            {!! Form::label('fecha_vencimiento_label', 'Vencimiento: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm-2">
                                {!! Form::date('fecha_vencimiento', old('fecha_vencimiento'), [
                                    'class' => 'form-control form-control-sm' . ($errors->has('fecha_vencimiento') ? 'is-invalid' : ''),
                                    'placeholder' => 'ej.: paracetamol',
                                ]) !!}
                                @if ($errors->has('fecha_vencimiento'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('fecha_vencimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {!! Form::label('stock_maximo_label', 'Stock maximo: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::number('stock_maximo', null, [
                                    'class' => 'form-control form-control-sm' . ($errors->has('stock_maximo') ? 'is-invalid' : ''),
                                ]) !!}
                                @if ($errors->has('stock_maximo'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('stock_maximo') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {!! Form::label('stock_fisico_label', 'Stock fisico: ', ['class' => 'col-sm-2 col-form-label']) !!}
                            <div class="col-sm">
                                {!! Form::number('stock_fisico', null, [
                                    'class' => 'form-control form-control-sm' . ($errors->has('stock_fisico') ? 'is-invalid' : ''),
                                ]) !!}
                                @if ($errors->has('stock_fisico'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('stock_fisico') }}</strong>
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
                                {{-- <a href="{{ url()->previous() }}" style="text-decoration:none">
                                    {{ Form::button('Cancelar', ['class' => 'btn bg-gradient-secondary btn-sm btn-block']) }}
                                </a> --}}
                                <button type="button" class="btn bg-gradient-secondary btn-sm btn-block"
                                    data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
