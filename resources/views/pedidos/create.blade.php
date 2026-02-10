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

        {{ html()->form('POST', route('pedidos.store'))
            ->class('needs-validation')
            ->open() }}

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
                            <label class="font-weight-bold">
                                Fecha del Pedido <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_pedido" class="form-control {{ $errors->has('fecha_pedido') ? 'is-invalid' : '' }}" value="{{ old('fecha_pedido', date('Y-m-d')) }}" required>
                            @error('fecha_pedido')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Área <span class="text-danger">*</span>
                            </label>
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
                            {{ html()->label('solicitante', 'Solicitante')
                                ->attribute('class', 'font-weight-bold') }}
                            {{ html()->text('solicitante')
                                ->class('form-control ' . ($errors->has('solicitante') ? 'is-invalid' : ''))
                                ->value(old('solicitante'))
                                ->placeholder('Nombre del solicitante')
                                ->maxlength(100) }}
                            @error('solicitante')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {{ html()->label('observaciones', 'Observaciones')
                        ->attribute('class', 'font-weight-bold') }}
                    {{ html()->textarea('observaciones')
                        ->class('form-control ' . ($errors->has('observaciones') ? 'is-invalid' : ''))
                        ->value(old('observaciones'))
                        ->rows(3)
                        ->placeholder('Notas adicionales') }}
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
                                        <input type="checkbox" class="farmaco-select" data-index="{{ $index }}" data-farmaco-id="{{ $farmaco->id }}">
                                    </td>
                                    <td>{{ $farmaco->descripcion }}</td>
                                    <td>{{ $farmaco->dosis }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $farmaco->stock_maximo }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $farmaco->getStockFisicoCalculado() < $farmaco->stock_maximo ? 'badge-danger' : 'badge-success' }}">
                                            {{ $farmaco->getStockFisicoCalculado() }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $farmaco->stock_maximo - $farmaco->stock_fisico }}</strong>
                                    </td>
                                    <td>
                                        @if($farmaco->area_predeterminada)
                                            <span class="badge badge-info">{{ $farmaco->area_predeterminada->nombre_area }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm farmaco-cantidad"
                                            value="{{ $farmaco->stock_maximo - $farmaco->stock_fisico }}"
                                            data-index="{{ $index }}"
                                            data-max="{{ $farmaco->stock_maximo - $farmaco->stock_fisico }}"
                                            data-farmaco-id="{{ $farmaco->id }}"
                                            data-farmaco-nombre="{{ $farmaco->descripcion }}"
                                            min="1"
                                            max="{{ $farmaco->stock_maximo - $farmaco->stock_fisico }}"
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
                {{ html()->submit('Crear Pedido')
                    ->class('btn btn-success')
                    ->attribute('id', 'submitBtn') }}
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>

        {{ html()->form()->close() }}
    </div>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            console.log('✓ Script jQuery iniciado');
            console.log('Checkboxes encontrados:', $('.farmaco-select').length);

            // Validar cantidad máxima
            $(document).on('change', '.farmaco-cantidad', function() {
                const $this = $(this);
                const maxValue = parseInt($this.data('max'));
                const currentValue = parseInt($this.val()) || 0;
                const farmacoNombre = $this.data('farmaco-nombre');

                if (currentValue > maxValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad excede el máximo',
                        text: `La cantidad no puede exceder ${maxValue} para "${farmacoNombre}"`,
                        confirmButtonText: 'Ajustar'
                    }).then(() => {
                        $this.val(maxValue);
                    });
                }
            });

            // Manejar click del botón submit
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();
                
                console.log('📝 Botón clickeado');
                
                let selectedFarmacos = [];
                const $checkedBoxes = $('.farmaco-select:checked');
                
                console.log('✓ Checkboxes seleccionados:', $checkedBoxes.length);

                $checkedBoxes.each(function() {
                    const $checkbox = $(this);
                    const index = $checkbox.data('index');
                    const farmacoId = $checkbox.data('farmaco-id');
                    const $cantidadInput = $(`.farmaco-cantidad[data-index="${index}"]`);
                    const cantidad = parseInt($cantidadInput.val()) || 0;

                    console.log(`  Farmaco ID: ${farmacoId}, Cantidad: ${cantidad}`);

                    if (cantidad > 0) {
                        selectedFarmacos.push({
                            farmaco_id: farmacoId,
                            cantidad: cantidad
                        });
                    }
                });

                console.log('Final - Fármacos a enviar:', selectedFarmacos);

                if (selectedFarmacos.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin fármacos seleccionados',
                        text: 'Por favor selecciona al menos un fármaco marcando los checkboxes',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                // Limpiar inputs ocultos anteriores
                $('input[name^="farmacos["]').remove();

                // Crear inputs dinámicamente para los fármacos seleccionados
                const $form = $('form');
                selectedFarmacos.forEach((farmaco, index) => {
                    $form.append($('<input>').attr({
                        type: 'hidden',
                        name: `farmacos[${index}][farmaco_id]`,
                        value: farmaco.farmaco_id
                    }));

                    $form.append($('<input>').attr({
                        type: 'hidden',
                        name: `farmacos[${index}][cantidad]`,
                        value: farmaco.cantidad
                    }));
                });

                console.log('✓ Enviando formulario...');
                $form.submit();
            });
        });
    </script>
@endsection
