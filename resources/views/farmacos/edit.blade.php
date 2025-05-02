@extends('adminlte::page')
@section('title', 'actualiza-farmaco')

@section('content')
    <div class="container pt-3">
        <div class="row justify-content-left">
            <div class="col-sx-12 col-sm-12 col-lg-8">
                <div class="card card-default">
                    <div class="card-header">Actualizando Farmaco</div>
                    <div class="card-body">
                        {{ Form::open(['action' => 'farmacoController@update', 'method' => 'POST', 'url' => 'farmacos/' . $farmaco->id, 'class' => 'form-horizontal']) }}
                        {{-- Form::open(['action' => 'SolicitudController@update', 'method' => 'POST', 'url' => 'solicitudes/'.$solicitud->id, 'class' => 'form-horizontal']) --}}
                        @csrf
                        @method('PATCH')
                        <div class="form-horizontal">
                            <div class="form-group row">
                                {!! Form::label('descripcion_label', 'Descripción: ', ['class' => 'col-sm-2 col-form-label']) !!}
                                <div class="col-sm">
                                    {!! Form::text('descripcion', old('descripcion', $farmaco->descripcion), [
                                        'class' => 'form-control form-control-sm' . ($errors->has('descripcion') ? 'is-invalid' : ''),
                                        'placeholder' => 'ej.: paracetamol',
                                        'disabled' => 'disabled',
                                    ]) !!}
                                    @if ($errors->has('descripcion'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('descripcion') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                {!! Form::label('forma_farmaceutica_label', 'Forma farmaceutica: ', ['class' => 'col-sm-2 col-form-label']) !!}
                                <div class="col-sm">
                                    {!! Form::select('forma_farmaceutica', [$farmaco->forma_farmaceutica], $farmaco->forma_farmaceutica, [
                                        'id' => 'forma',
                                        'disabled' => 'disabled',
                                    ]) !!}
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
                                    {!! Form::text('dosis', old('dosis', $farmaco->dosis), [
                                        'class' => 'form-control form-control-sm' . ($errors->has('dosis') ? 'is-invalid' : ''),
                                        'placeholder' => 'ej.: 100 mg',
                                        'disabled' => 'disabled',
                                    ]) !!}
                                    @if ($errors->has('dosis'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('dosis') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                {!! Form::label('controlado_label', 'Controlado: ', ['class' => 'col-sm-2 col-form-label']) !!}
                                <div class="col-sm">
                                    {!! Form::checkbox('controlado', 1, old('controlado', $farmaco->controlado ? true : null), [
                                        'class' => 'form-control my-2 controlado',
                                        'id' => 'controlado',
                                        'disabled' => 'disabled',
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
                                    {!! Form::date('fecha_vencimiento', old('fecha_vencimiento', $farmaco->fecha_vencimiento), [
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
                                    {!! Form::number('stock_maximo', old('stock_maximo', $farmaco->stock_maximo), [
                                        'class' => 'form-control form-control-sm' . ($errors->has('stock_maximo') ? 'is-invalid' : ''),
                                        'disabled' => 'disabled',
                                    ]) !!}
                                    @if ($errors->has('stock_maximo'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('stock_maximo') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                {!! Form::label('stock_fisico_label', 'Stock fisico: ', ['class' => 'col-sm-2 col-form-label']) !!}
                                <div class="col-sm">
                                    {!! Form::number('stock_fisico', old('stock_maximo', $farmaco->stock_fisico), [
                                        'class' => 'form-control form-control-sm' . ($errors->has('stock_fisico') ? 'is-invalid' : ''),
                                    ]) !!}
                                    @if ($errors->has('stock_fisico'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('stock_fisico') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('area_label', 'Area: ', ['class' => 'col-sm col-form-label']) !!}
                                <div class="col">
                                    {!! Form::select('area_id', $areas, old('area_id', $farmaco->areas), [
                                        'class' => 'form-control form-control-sm' . ($errors->has('area_id') ? 'is-invalid' : ''),
                                        'placeholder' => 'seleccione',
                                        'id' => 'area',
                                    ]) !!}
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
                                {{ Form::submit('Actualizar', ['class' => 'btn bg-gradient-success btn-sm btn-block']) }}
                            </div>
                            <div class="col">
                                <a href="{{ url()->previous() }}" style="text-decoration:none">
                                    {{ Form::button('Cancelar', ['class' => 'btn bg-gradient-secondary btn-sm btn-block']) }}
                                </a>
                            </div>
                        </div>

                        {{ Form::close() }}
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
