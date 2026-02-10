@extends('adminlte::page')
@section('title', 'actualiza-area')

@section('content')
    <div class="container pt-3">
        <div class="row justify-content-left">
            <div class="col-sx-12 col-sm-12 col-lg-8">
                <div class="card card-default">
                    <div class="card-header">Actualizando Area</div>
                    <div class="card-body">
                        {{ html()->form('PATCH', url('areas/' . $area->id))->class('form-horizontal')->open() }}
                        {{-- Form::open(['action' => 'SolicitudController@update', 'method' => 'POST', 'url' => 'solicitudes/'.$solicitud->id, 'class' => 'form-horizontal']) --}}
                        @csrf
                        <div class="form-horizontal">
                            <div class="form-group row">
                                {{ html()->label('nombre area: ', 'nombre_area')->class('col-sm-2 col-form-label') }}
                                <div class="col-sm">
                                    {{ html()
                                        ->text('nombre_area')
                                        ->value(old('nombre_area', $area->nombre_area))
                                        ->class('form-control form-control-sm' . ($errors->has('nombre_area') ? ' is-invalid' : '')) }}
                                    @if ($errors->has('nombre_area'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('nombre_area') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                {{ html()->label('Descripcion area: ', 'descripcion_area')->class('col-sm-2 col-form-label') }}
                                <div class="col-sm">
                                    {{ html()
                                        ->textarea('descripcion_area')
                                        ->value(old('descripcion_area', $area->descripcion_area))
                                        ->class('form-control form-control-sm' . ($errors->has('descripcion_area') ? ' is-invalid' : '')) }}
                                    @if ($errors->has('descripcion_area'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('descripcion_area') }}</strong>
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
                                <a href="{{ url('areas') }}" style="text-decoration:none">
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
        $('#forma').select2({
            theme: "classic",
            width: '100%',
        });
    </script>
@endsection
