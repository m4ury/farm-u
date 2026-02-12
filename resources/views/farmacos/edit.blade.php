@extends('adminlte::page')
@section('title', 'actualiza-farmaco')

@section('content')
    <div class="container pt-3">
        <div class="row justify-content-left">
            <div class="col-sx-12 col-sm-12 col-lg-8">
                <div class="card card-default">
                    <div class="card-header">Actualizando Farmaco</div>
                    <div class="card-body">
                        {{ html()->form('PATCH', url('farmacos/' . $farmaco->id))->class('form-horizontal')->open() }}
                        {{-- Form::open(['action' => 'SolicitudController@update', 'method' => 'POST', 'url' => 'solicitudes/'.$solicitud->id, 'class' => 'form-horizontal']) --}}
                        @csrf
                        <div class="form-horizontal">
                            <div class="form-group row">
                                {{ html()->label('Descripción: ', 'descripcion')->class('col-sm-2 col-form-label') }}
                                <div class="col-sm">
                                    {{ html()
                                        ->text('descripcion')
                                        ->value(old('descripcion', $farmaco->descripcion))
                                        ->class('form-control form-control-sm' . ($errors->has('descripcion') ? ' is-invalid' : ''))
                                        ->placeholder('ej.: paracetamol')
                                        ->disabled() }}
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
                                            $farmaco->forma_farmaceutica => $farmaco->forma_farmaceutica,
                                        ])
                                        ->value($farmaco->forma_farmaceutica)
                                        ->class('form-control form-control-sm' . ($errors->has('forma_farmaceutica') ? ' is-invalid' : ''))
                                        ->id('forma')
                                        ->disabled() }}
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
                                        ->value(old('dosis', $farmaco->dosis))
                                        ->class('form-control form-control-sm' . ($errors->has('dosis') ? ' is-invalid' : ''))
                                        ->placeholder('ej.: 100 mg')
                                        ->disabled() }}
                                    @if ($errors->has('dosis'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('dosis') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                {{ html()->label('Controlado: ', 'controlado')->class('col-sm-2 col-form-label') }}
                                <div class="col-sm">
                                    {{ html()
                                        ->checkbox('controlado', old('controlado', $farmaco->controlado ? true : null))
                                        ->value(1)
                                        ->class('form-control my-2 controlado')
                                        ->id('controlado')
                                        ->disabled() }}
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
                                        ->value(old('stock_minimo', $farmaco->stock_minimo))
                                        ->class('form-control form-control-sm' . ($errors->has('stock_minimo') ? ' is-invalid' : ''))
                                        ->disabled() }}
                                    @if ($errors->has('stock_minimo'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('stock_minimo') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                {{ html()->label('Area: ', 'area_id')->class('col-sm col-form-label') }}
                                <div class="col">
                                    {{ html()
                                        ->select('area_id', $areas)
                                        ->value(old('area_id', $farmaco->areas))
                                        ->class('form-control form-control-sm' . ($errors->has('area_id') ? ' is-invalid' : ''))
                                        ->placeholder('seleccione')
                                        ->id('area') }}
                                    @if ($errors->has('area_id'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('area_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="row py-3 px-3">
                            <div class="col">
                                {{ html()->submit('Actualizar')->class('btn bg-gradient-success btn-sm btn-block') }}
                            </div>
                            <div class="col">
                                <a href="{{ url()->previous() }}" style="text-decoration:none">
                                    {{ html()->button('Cancelar')->class('btn bg-gradient-secondary btn-sm btn-block')->type('button') }}
                                </a>
                            </div>
                        </div>

                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $('#forma, #area').select2({
            theme: "classic",
            width: '100%',
        });
    </script>
@endsection
