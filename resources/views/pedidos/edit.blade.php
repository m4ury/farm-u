@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('content_header')
    <h1>Editar Pedido #{{ $pedido->id }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Edición</h3>
        </div>

        {!! Html::form('PUT', route('pedidos.update', $pedido))
            ->open() !!}

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <h4 class="alert-heading">¡Error!</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('fecha_pedido', 'Fecha del Pedido')
                                ->attribute('class', 'font-weight-bold')
                                ->addChild(Html::element('span')->text('*')->class('text-danger')) !!}
                            {!! Html::input('date', 'fecha_pedido')
                                ->class('form-control ' . ($errors->has('fecha_pedido') ? 'is-invalid' : ''))
                                ->value($pedido->fecha_pedido->format('Y-m-d'))
                                ->required() !!}
                            @error('fecha_pedido')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('area_id', 'Área')
                                ->attribute('class', 'font-weight-bold')
                                ->addChild(Html::element('span')->text('*')->class('text-danger')) !!}
                            {!! Html::select('area_id', [''] + $areas->pluck('nombre_area', 'id')->toArray())
                                ->class('form-control ' . ($errors->has('area_id') ? 'is-invalid' : ''))
                                ->value($pedido->area_id)
                                ->required() !!}
                            @error('area_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('solicitante', 'Solicitante')
                                ->attribute('class', 'font-weight-bold') !!}
                            {!! Html::input('text', 'solicitante')
                                ->class('form-control ' . ($errors->has('solicitante') ? 'is-invalid' : ''))
                                ->value(old('solicitante', $pedido->solicitante))
                                ->maxlength(100) !!}
                            @error('solicitante')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('estado', 'Estado')
                                ->attribute('class', 'font-weight-bold')
                                ->addChild(Html::element('span')->text('*')->class('text-danger')) !!}
                            {!! Html::select('estado', [
                                'solicitado' => 'Solicitado',
                                'entregado' => 'Entregado',
                                'rechazado' => 'Rechazado'
                            ])
                                ->class('form-control ' . ($errors->has('estado') ? 'is-invalid' : ''))
                                ->value($pedido->estado)
                                ->required() !!}
                            @error('estado')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Html::label('observaciones', 'Observaciones')
                        ->attribute('class', 'font-weight-bold') !!}
                    {!! Html::textarea('observaciones')
                        ->class('form-control ' . ($errors->has('observaciones') ? 'is-invalid' : ''))
                        ->value(old('observaciones', $pedido->observaciones))
                        ->rows(3) !!}
                    @error('observaciones')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>

                <hr>

                <h5>Fármacos Solicitados</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Fármaco</th>
                                <th>Cantidad Pedida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->farmacos as $farmaco)
                                <tr>
                                    <td>{{ $farmaco->descripcion }} - {{ $farmaco->dosis }}</td>
                                    <td><strong>{{ $farmaco->pivot->cantidad_pedida }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {!! Html::submit('Guardar Cambios')
                    ->class('btn btn-success') !!}
                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>

        {!! Html::form()->close() !!}
    </div>
@endsection
