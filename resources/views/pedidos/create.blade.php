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
                            <select name="area_id" class="form-control select2-area {{ $errors->has('area_id') ? 'is-invalid' : '' }}" required>
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
                    <table id="farmacos-pedido-table" class="table table-striped table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Fármaco</th>
                                <th>Dosis</th>
                                <th>Stock Mínimo</th>
                                <th>En Farmacia</th>
                                <th>En Áreas</th>
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
                                        <span class="badge badge-primary">{{ $farmaco->stock_minimo }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info" title="Stock disponible en Farmacia Central">
                                            <i class="fas fa-clinic-medical"></i> {{ $farmaco->stock_en_farmacia }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $farmaco->stock_en_areas < $farmaco->stock_minimo ? 'badge-danger' : 'badge-success' }}" title="Stock en áreas asignadas">
                                            <i class="fas fa-hospital"></i> {{ $farmaco->stock_en_areas }}
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
                                        <input type="number" class="form-control form-control-sm farmaco-cantidad"
                                            value="{{ $farmaco->cantidad_a_pedir }}"
                                            data-index="{{ $index }}"
                                            data-max="{{ $farmaco->cantidad_a_pedir }}"
                                            data-farmaco-id="{{ $farmaco->id }}"
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

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('js')
    <script>
        $(document).ready(function() {
            // Select2 para área (pocas opciones, sin buscador)
            $('.select2-area').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccionar área',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // Almacenar selecciones entre páginas de DataTables
            var selectedData = {}; // { farmacoId: cantidad }

            // Inicializar DataTable
            var table = $('#farmacos-pedido-table').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0, 7] },
                    { searchable: false, targets: [0, 3, 4, 5, 7] }
                ],
                drawCallback: function() {
                    // Restaurar estado de checkboxes al cambiar de página
                    $('#farmacos-pedido-table tbody input.farmaco-select').each(function() {
                        var fId = $(this).data('farmaco-id').toString();
                        if (selectedData[fId] !== undefined) {
                            $(this).prop('checked', true);
                            $(this).closest('tr').find('.farmaco-cantidad').val(selectedData[fId]);
                        }
                    });
                }
            });

            // Guardar estado del checkbox al cambiar
            $(document).on('change', '.farmaco-select', function() {
                var fId = $(this).data('farmaco-id').toString();
                if ($(this).is(':checked')) {
                    var cantidad = $(this).closest('tr').find('.farmaco-cantidad').val();
                    selectedData[fId] = parseInt(cantidad) || 0;
                } else {
                    delete selectedData[fId];
                }
            });

            // Guardar cantidad al cambiarla
            $(document).on('change', '.farmaco-cantidad', function() {
                var $this = $(this);
                var maxValue = parseInt($this.data('max'));
                var currentValue = parseInt($this.val()) || 0;
                var farmacoNombre = $this.data('farmaco-nombre');
                var fId = $this.data('farmaco-id').toString();

                if (currentValue > maxValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad excede el máximo',
                        text: 'La cantidad no puede exceder ' + maxValue + ' para "' + farmacoNombre + '"',
                        confirmButtonText: 'Ajustar'
                    }).then(function() {
                        $this.val(maxValue);
                        if (selectedData[fId] !== undefined) {
                            selectedData[fId] = maxValue;
                        }
                    });
                } else {
                    // Si está seleccionado, actualizar la cantidad guardada
                    if (selectedData[fId] !== undefined) {
                        selectedData[fId] = currentValue;
                    }
                }
            });

            // Manejar submit - recoger de TODAS las páginas via selectedData
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();

                // También capturar los que están checkeados en la página visible actual
                $('#farmacos-pedido-table tbody input.farmaco-select:checked').each(function() {
                    var fId = $(this).data('farmaco-id').toString();
                    var cantidad = $(this).closest('tr').find('.farmaco-cantidad').val();
                    selectedData[fId] = parseInt(cantidad) || 0;
                });

                var farmacosToSend = [];
                $.each(selectedData, function(fId, cantidad) {
                    if (cantidad > 0) {
                        farmacosToSend.push({ farmaco_id: fId, cantidad: cantidad });
                    }
                });

                if (farmacosToSend.length === 0) {
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

                var $form = $('form');
                farmacosToSend.forEach(function(farmaco, index) {
                    $form.append($('<input>').attr({
                        type: 'hidden',
                        name: 'farmacos[' + index + '][farmaco_id]',
                        value: farmaco.farmaco_id
                    }));
                    $form.append($('<input>').attr({
                        type: 'hidden',
                        name: 'farmacos[' + index + '][cantidad]',
                        value: farmaco.cantidad
                    }));
                });

                $form.submit();
            });
        });
    </script>
@endsection
