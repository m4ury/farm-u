@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            @foreach($areas as $area)
                <div class="col-lg-4 col-sm py-3">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h6 class="text-muted">
                                cant. Farmacos
                                {{ $area->farmacos->count() }}
                            </h6>
                            <p class="text-bold text-uppercase">{{ $area->nombre_area }}</p>
                        </div>
                        <h6 class="text-primary m-2 text-bold">
                            Stock maximo: {{ $area->farmacos->pluck('stock_maximo')->sum() }}
                        </h6>
                        <h6
                            class="{{ $area->farmacos->pluck('stock_fisico')->sum() < $area->farmacos->pluck('stock_maximo')->sum() ? 'text-danger' : 'text-success' }} mx-2 text-bold">
                            Stock fisico: {{ $area->farmacos->pluck('stock_fisico')->sum() }}
                        </h6>
                        <div class="icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        @php
                            $areaSlug = $areaSlugMapping[$area->nombre_area] ?? null;
                        @endphp
                        @if($areaSlug)
                            <a href="{{ route('areas.show', $areaSlug) }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-12 table-responsive py-3">
            <div class="btn-group mb-3">
                <button type="button" class="btn btn-primary" id="selectAllBtn">
                    <i class="fas fa-check-square"></i> Seleccionar todos
                </button>
                <button type="button" class="btn btn-secondary" id="deselectAllBtn">
                    <i class="fas fa-square"></i> Deseleccionar todos
                </button>
                <button type="button" class="btn btn-success" id="generateOrderBtn" disabled>
                    <i class="fas fa-plus-circle"></i> Generar Pedido
                </button>
            </div>
            <table id="farmacos" class="table table-hover table-md-responsive table-bordered">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th style="width: 50px;">
                            <input type="checkbox" id="selectAllCheck" title="Seleccionar todos">
                        </th>
                        <th>Farmaco</th>
                        <th>Forma farmaceutica</th>
                        <th>Dosis</th>
                        <th>Stock Maximo</th>
                        <th>Stock fisico</th>
                        <th>Area</th>
                        <th>Reponer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bajo as $b)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="farmaco-checkbox" data-farmaco-id="{{ $b->id }}" 
                                    data-farmaco-nombre="{{ $b->descripcion }}"
                                    data-stock-maximo="{{ $b->stock_maximo }}"
                                    data-stock-fisico="{{ $b->stock_fisico }}"
                                    title="Seleccionar fármaco">
                            </td>
                            <td>{{ $b->descripcion }}</td>
                            <td>{{ $b->forma_farmaceutica }}</td>
                            <td>{{ $b->dosis }}</td>
                            <td>{{ $b->stock_maximo }}</td>
                            <td>{{ $b->stock_fisico }}</td>
                            <td>{{ $b->areas->pluck('nombre_area')->first() ?? '' }}</td>
                            <td>{{ $b->stock_maximo - $b->stock_fisico }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear el pedido -->
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Crear Nuevo Pedido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="orderForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha_pedido">Fecha del Pedido <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_pedido" name="fecha_pedido" required>
                        </div>

                        <div class="form-group">
                            <label for="solicitante">Solicitante</label>
                            <input type="text" class="form-control" id="solicitante" name="solicitante" placeholder="Nombre del solicitante" maxlength="100">
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales"></textarea>
                        </div>

                        <!-- Sección de fármacos seleccionados -->
                        <div class="form-group">
                            <label>Fármacos Seleccionados <span class="text-danger">*</span></label>
                            <div id="farmacosList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <!-- Los fármacos se agregarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            const table = $("#farmacos").DataTable({
                paging: true,
                pagingType: 'first_last_numbers',
                pageLength: 5,
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                    'pdf',
                    'print',
                ],
                columnDefs: [
                    { orderable: false, targets: 0 }
                ],
                language: {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "infoThousands": ",",
                    "loadingRecords": "Cargando...",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
            });

            // Establecer la fecha actual por defecto
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_pedido').val(today);

            // Seleccionar todos los checkboxes
            $('#selectAllCheck, #selectAllBtn').on('click', function() {
                $('.farmaco-checkbox').prop('checked', true);
                $('#selectAllCheck').prop('checked', true);
                updateUI();
            });

            // Deseleccionar todos los checkboxes
            $('#deselectAllBtn').on('click', function() {
                $('.farmaco-checkbox').prop('checked', false);
                $('#selectAllCheck').prop('checked', false);
                updateUI();
            });

            // Cambiar estado del checkbox "Seleccionar todos" cuando se selecciona uno individual
            $('.farmaco-checkbox').on('change', function() {
                const total = $('.farmaco-checkbox').length;
                const checked = $('.farmaco-checkbox:checked').length;
                $('#selectAllCheck').prop('checked', total === checked);
                updateUI();
            });

            // Obtener el nombre del usuario logueado
            const userName = "{{ auth()->user()->name ?? 'Usuario' }}";

            // Generar el pedido
            $('#generateOrderBtn').on('click', function() {
                const selectedFarmacos = [];
                $('.farmaco-checkbox:checked').each(function() {
                    selectedFarmacos.push({
                        id: $(this).data('farmaco-id'),
                        nombre: $(this).data('farmaco-nombre'),
                        stock_maximo: parseInt($(this).data('stock-maximo')),
                        stock_fisico: parseInt($(this).data('stock-fisico'))
                    });
                });

                if (selectedFarmacos.length === 0) {
                    Swal.fire('Advertencia', 'Por favor selecciona al menos un fármaco', 'warning');
                    return;
                }

                // Mostrar el modal y agregar los fármacos seleccionados
                $('#farmacosList').empty();
                selectedFarmacos.forEach(function(farmaco, index) {
                    const cantidadSugerida = farmaco.stock_maximo - farmaco.stock_fisico;
                    const html = `
                        <div class="form-group">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <label>${farmaco.nombre}</label>
                                    <input type="hidden" name="farmacos[${index}][id]" value="${farmaco.id}">
                                    <small class="form-text text-muted d-block">Stock: ${farmaco.stock_fisico}/${farmaco.stock_maximo}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="cantidad_${index}">Cantidad a pedir</label>
                                    <input type="number" class="form-control cantidad-input" id="cantidad_${index}" 
                                        name="farmacos[${index}][cantidad]"
                                        data-max="${farmaco.stock_maximo}"
                                        data-farmaco-nombre="${farmaco.nombre}"
                                        min="1" value="${cantidadSugerida}" required>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#farmacosList').append(html);
                });

                // Establecer el solicitante por defecto como el usuario logueado
                if (!$('#solicitante').val()) {
                    $('#solicitante').val(userName);
                }

                $('#orderModal').modal('show');
            });

            // Enviar el formulario del pedido
            $('#orderForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validar que las cantidades no excedan el stock máximo
                let isValid = true;
                $('.cantidad-input').each(function() {
                    const cantidad = parseInt($(this).val());
                    const maxStock = parseInt($(this).data('max'));
                    const nombreFarmaco = $(this).data('farmaco-nombre');
                    
                    if (cantidad > maxStock) {
                        isValid = false;
                        Swal.fire('Error', `La cantidad para ${nombreFarmaco} no puede ser mayor al stock máximo (${maxStock})`, 'error');
                        return false;
                    }
                });

                if (!isValid) return;
                
                const formData = {
                    _token: $('input[name="_token"]').val(),
                    fecha_pedido: $('#fecha_pedido').val(),
                    solicitante: $('#solicitante').val(),
                    observaciones: $('#observaciones').val(),
                    farmacos: []
                };

                selectedFarmacos = [];
                $('.farmaco-checkbox:checked').each(function() {
                    selectedFarmacos.push({
                        id: $(this).data('farmaco-id'),
                        nombre: $(this).data('farmaco-nombre')
                    });
                });

                selectedFarmacos.forEach(function(farmaco, index) {
                    const cantidad = $(`#cantidad_${index}`).val();
                    formData.farmacos.push({
                        id: farmaco.id,
                        cantidad: parseInt(cantidad)
                    });
                });

                $.ajax({
                    url: '{{ route("pedidos.storeFromSelection") }}',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(function() {
                            window.location.href = '{{ route("pedidos.index") }}';
                        });
                        $('#orderModal').modal('hide');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Ocurrió un error al crear el pedido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    }
                });
            });

            // Validación en tiempo real para las cantidades
            $(document).on('change', '.cantidad-input', function() {
                const cantidad = parseInt($(this).val());
                const maxStock = parseInt($(this).data('max'));
                const nombreFarmaco = $(this).data('farmaco-nombre');
                
                if (cantidad > maxStock) {
                    $(this).addClass('is-invalid');
                    $(this).after(`<div class="invalid-feedback d-block">No puede exceder ${maxStock}</div>`);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });

            // Actualizar el estado de los botones
            function updateUI() {
                const hasSelected = $('.farmaco-checkbox:checked').length > 0;
                $('#generateOrderBtn').prop('disabled', !hasSelected);
            }

            // Inicializar el estado de los botones
            updateUI();
        });
    </script>
@endsection
