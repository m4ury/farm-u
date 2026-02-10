@extends('adminlte::page')

@section('title', 'Crear Pedido')

@section('content_header')
    <h1>Crear Nuevo Pedido</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Creación</h3>
        </div>

        {!! Html::form('POST', route('pedidos.store'))
            ->class('needs-validation')
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
                                ->value(old('fecha_pedido', date('Y-m-d')))
                                ->required() !!}
                            @error('fecha_pedido')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Html::label('area_id', 'Área')
                                ->attribute('class', 'font-weight-bold')
                                ->addChild(Html::element('span')->text('*')->class('text-danger')) !!}
                            <select name="area_id" class="form-control {{ $errors->has('area_id') ? 'is-invalid' : '' }}" required>
                                <option value="">Seleccionar área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre_area }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Html::label('solicitante', 'Solicitante')
                                ->attribute('class', 'font-weight-bold') !!}
                            {!! Html::input('text', 'solicitante')
                                ->class('form-control ' . ($errors->has('solicitante') ? 'is-invalid' : ''))
                                ->value(old('solicitante'))
                                ->placeholder('Nombre del solicitante')
                                ->maxlength(100) !!}
                            @error('solicitante')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Html::label('observaciones', 'Observaciones')
                        ->attribute('class', 'font-weight-bold') !!}
                    {!! Html::textarea('observaciones')
                        ->class('form-control ' . ($errors->has('observaciones') ? 'is-invalid' : ''))
                        ->value(old('observaciones'))
                        ->rows(3)
                        ->placeholder('Notas adicionales') !!}
                    @error('observaciones')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>

                <hr>

                <h5>
                    Seleccionar Fármacos
                    <span class="text-danger">*</span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Fármaco</th>
                                <th>Dosis</th>
                                <th>Stock Máximo</th>
                                <th>Stock Físico</th>
                                <th>A Reponer</th>
                                <th>Área</th>
                                <th>Cantidad a Pedir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($farmacos as $index => $farmaco)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="farmacos[{{ $index }}][farmaco_id]"
                                            value="{{ $farmaco->id }}" class="farmaco-select">
                                    </td>
                                    <td>{{ $farmaco->descripcion }}</td>
                                    <td>{{ $farmaco->dosis }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $farmaco->stock_maximo }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $farmaco->stock_fisico < $farmaco->stock_maximo ? 'badge-danger' : 'badge-success' }}">
                                            {{ $farmaco->stock_fisico }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $farmaco->cantidad_a_pedir }}</strong>
                                    </td>
                                    <td>
                                        @if($farmaco->area_predeterminada)
                                            <span class="badge badge-info">{{ $farmaco->area_predeterminada->nombre_area }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" name="farmacos[{{ $index }}][cantidad]"
                                            class="form-control form-control-sm farmaco-cantidad"
                                            value="{{ $farmaco->cantidad_a_pedir }}"
                                            data-max="{{ $farmaco->cantidad_a_pedir }}"
                                            data-farmaco-nombre="{{ $farmaco->descripcion }}"
                                            min="1"
                                            max="{{ $farmaco->cantidad_a_pedir }}"
                                            style="width: 100px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('farmacos')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="card-footer">
                {!! Html::submit('Crear Pedido')
                    ->class('btn btn-success')
                    ->attribute('id', 'submitBtn') !!}
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>

        {!! Html::form()->close() !!}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cantidadInputs = document.querySelectorAll('.farmaco-cantidad');

            cantidadInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const maxValue = parseInt(this.dataset.max);
                    const currentValue = parseInt(this.value) || 0;
                    const farmacoNombre = this.dataset.farmacoNombre;

                    if (currentValue > maxValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cantidad excede el máximo',
                            text: `La cantidad no puede exceder ${maxValue} para "${farmacoNombre}"`,
                            confirmButtonText: 'Ajustar'
                        }).then(() => {
                            this.value = maxValue;
                        });
                    }
                });
            });

            // Validar antes de enviar
            document.getElementById('submitBtn').addEventListener('click', function(e) {
                let hasChecked = false;
                document.querySelectorAll('.farmaco-select:checked').forEach(checkbox => {
                    hasChecked = true;
                });

                if (!hasChecked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin fármacos seleccionados',
                        text: 'Por favor selecciona al menos un fármaco',
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        });
    </script>
@endsection
