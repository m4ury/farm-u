@extends('adminlte::page')

@section('title', 'Nueva Receta / DAU')

@section('plugins.Select2', true)

@section('content')
    <div class="container-fluid my-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <a class="btn bg-gradient-info btn-sm mr-3" title="Volver" href="{{ route('recetas.index') }}">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                        Volver
                    </a>
                    <i class="fas fa-file-prescription px-2" style="color:rgb(38, 0, 255)"></i>
                    NUEVA RECETA / DAU
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="recetaForm" action="{{ route('recetas.store') }}" method="POST">
                    @csrf

                    {{-- Datos de la Receta --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="numero_dau">
                                    <i class="fas fa-hashtag text-primary"></i>
                                    Número DAU / Receta <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="numero_dau" id="numero_dau"
                                    class="form-control @error('numero_dau') is-invalid @enderror"
                                    value="{{ old('numero_dau') }}"
                                    placeholder="Ej: DAU-2026-001"
                                    required>
                                @error('numero_dau')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_receta">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    Fecha <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha_receta" id="fecha_receta"
                                    class="form-control @error('fecha_receta') is-invalid @enderror"
                                    value="{{ old('fecha_receta', date('Y-m-d')) }}"
                                    required>
                                @error('fecha_receta')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="area_id">
                                    <i class="fas fa-hospital text-success"></i>
                                    Área (dejar vacío = Farmacia Central)
                                </label>
                                <select name="area_id" id="area_id" class="form-control select2">
                                    <option value="">Farmacia Central</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->nombre_area }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones">
                                    <i class="fas fa-comment text-muted"></i>
                                    Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones" rows="2"
                                    class="form-control"
                                    placeholder="Observaciones opcionales...">{{ old('observaciones') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Selector de Fármacos --}}
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label>
                                <i class="fas fa-pills text-info"></i>
                                Agregar Fármaco
                            </label>
                            <select id="farmacoSelector" class="form-control select2-farmaco" style="width: 100%">
                                <option value="">Buscar fármaco...</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="btnAgregarFarmaco" class="btn bg-gradient-primary btn-block">
                                <i class="fas fa-plus-circle"></i> Agregar Fármaco
                            </button>
                        </div>
                    </div>

                    {{-- Lista de Fármacos Agregados --}}
                    <div id="farmacosContainer">
                        {{-- Se agregan dinámicamente --}}
                    </div>

                    <div id="emptyState" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <p>No se han agregado fármacos. Use el selector de arriba para agregar fármacos a esta receta.</p>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="callout callout-info py-2">
                                <p class="mb-0">
                                    <strong>Total fármacos:</strong> <span id="totalFarmacos">0</span> |
                                    <strong>Total unidades:</strong> <span id="totalUnidades">0</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" id="btnGuardar" class="btn bg-gradient-success btn-lg" disabled>
                                <i class="fas fa-save"></i> Guardar Receta / DAU
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let farmacoIndex = 0;
        const farmacosAgregados = {};

        // Inicializar Select2 para el selector de fármacos con búsqueda AJAX
        function initFarmacoSelector() {
            $('#farmacoSelector').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Buscar fármaco por nombre...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: '{{ route("recetas.buscarFarmacos") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term,
                            area_id: $('#area_id').val() || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(f) {
                                return {
                                    id: f.id,
                                    text: f.descripcion + ' - ' + f.dosis + ' (' + f.forma_farmaceutica + ') [Stock: ' + f.stock + ']',
                                    farmaco: f
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        // Inicializar Select2 para área
        $('#area_id').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Farmacia Central'
        });

        // Reiniciar selector de fármacos cuando cambia el área
        $('#area_id').on('change', function() {
            $('#farmacoSelector').val(null).trigger('change');
            // Limpiar fármacos agregados porque cambia el contexto de stock
            if (Object.keys(farmacosAgregados).length > 0) {
                if (confirm('Cambiar de área eliminará los fármacos ya agregados. ¿Continuar?')) {
                    $('#farmacosContainer').empty();
                    for (let key in farmacosAgregados) delete farmacosAgregados[key];
                    farmacoIndex = 0;
                    actualizarTotales();
                } else {
                    // Revertir
                    return false;
                }
            }
        });

        initFarmacoSelector();

        // Agregar fármaco a la lista
        $('#btnAgregarFarmaco').on('click', function() {
            const select = $('#farmacoSelector');
            const data = select.select2('data')[0];

            if (!data || !data.farmaco) {
                Swal.fire('Atención', 'Seleccione un fármaco primero', 'warning');
                return;
            }

            const farmaco = data.farmaco;

            // Verificar si ya está agregado
            if (farmacosAgregados[farmaco.id]) {
                Swal.fire('Atención', 'Este fármaco ya fue agregado a la receta', 'warning');
                return;
            }

            if (farmaco.stock <= 0) {
                Swal.fire('Atención', 'Este fármaco no tiene stock disponible', 'warning');
                return;
            }

            farmacosAgregados[farmaco.id] = true;
            const idx = farmacoIndex++;

            const card = crearCardFarmaco(idx, farmaco);
            $('#farmacosContainer').append(card);
            $('#emptyState').hide();

            // Cargar lotes para este fármaco
            cargarLotes(idx, farmaco.id);

            // Resetear selector
            select.val(null).trigger('change');
            actualizarTotales();
        });

        function crearCardFarmaco(idx, farmaco) {
            return `
                <div class="card card-outline card-info mb-3" id="farmacoCard_${idx}" data-farmaco-id="${farmaco.id}">
                    <div class="card-header py-2">
                        <h3 class="card-title">
                            <i class="fas fa-pills text-info"></i>
                            <strong>${farmaco.descripcion}</strong>
                            <small class="text-muted ml-2">${farmaco.dosis} - ${farmaco.forma_farmaceutica}</small>
                            <span class="badge badge-success ml-2">Stock: ${farmaco.stock}</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-outline-danger btn-xs btnRemoveFarmaco"
                                data-idx="${idx}" data-farmaco-id="${farmaco.id}">
                                <i class="fas fa-trash"></i> Quitar
                            </button>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <input type="hidden" name="items[${idx}][farmaco_id]" value="${farmaco.id}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="mb-1">Cantidad a dispensar <span class="text-danger">*</span></label>
                                    <input type="number" name="items[${idx}][cantidad]"
                                        class="form-control form-control-sm cantidadInput"
                                        data-idx="${idx}" data-farmaco-id="${farmaco.id}" data-stock="${farmaco.stock}"
                                        min="1" max="${farmaco.stock}" required
                                        placeholder="Max: ${farmaco.stock}">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="mb-0">Lotes disponibles (FIFO)</label>
                                    <button type="button" class="btn btn-outline-info btn-xs btnSugerirLotes"
                                        data-idx="${idx}" data-farmaco-id="${farmaco.id}">
                                        <i class="fas fa-magic"></i> Sugerir lotes
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Lote</th>
                                                <th>Vence</th>
                                                <th>Disponible</th>
                                                <th style="width:120px">Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lotesBody_${idx}">
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-spinner fa-spin"></i> Cargando lotes...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function cargarLotes(idx, farmacoId) {
            const areaId = $('#area_id').val() || '';
            const url = `/recetas/api/farmacos/${farmacoId}/lotes?area_id=${areaId}`;

            fetch(url)
                .then(r => r.json())
                .then(lotes => {
                    const tbody = $(`#lotesBody_${idx}`);
                    tbody.empty();

                    if (lotes.length === 0) {
                        tbody.html('<tr><td colspan="4" class="text-center text-muted">Sin lotes disponibles</td></tr>');
                        return;
                    }

                    lotes.forEach(function(lote) {
                        tbody.append(`
                            <tr>
                                <td>${lote.num_serie}</td>
                                <td>${lote.fecha_vencimiento}</td>
                                <td>${lote.cantidad_disponible}</td>
                                <td>
                                    <input type="number"
                                        class="form-control form-control-sm loteInput"
                                        name="items[${idx}][lotes][${lote.id}]"
                                        min="0" max="${lote.cantidad_disponible}"
                                        data-lote-disponible="${lote.cantidad_disponible}"
                                        data-idx="${idx}"
                                        data-farmaco-id="${farmacoId}"
                                        value="0">
                                </td>
                            </tr>
                        `);
                    });
                })
                .catch(err => {
                    $(`#lotesBody_${idx}`).html('<tr><td colspan="4" class="text-danger text-center">Error al cargar lotes</td></tr>');
                });
        }

        // Remover fármaco
        $(document).on('click', '.btnRemoveFarmaco', function() {
            const idx = $(this).data('idx');
            const farmacoId = $(this).data('farmaco-id');
            $(`#farmacoCard_${idx}`).remove();
            delete farmacosAgregados[farmacoId];
            actualizarTotales();

            if ($('#farmacosContainer').children().length === 0) {
                $('#emptyState').show();
            }
        });

        // Sugerir lotes FIFO
        $(document).on('click', '.btnSugerirLotes', function() {
            const idx = $(this).data('idx');
            const cantidadInput = $(`.cantidadInput[data-idx="${idx}"]`);
            const cantidadSolicitada = parseInt(cantidadInput.val(), 10) || 0;

            if (cantidadSolicitada <= 0) {
                Swal.fire('Atención', 'Ingrese primero la cantidad a dispensar', 'warning');
                return;
            }

            let restante = cantidadSolicitada;
            $(`#lotesBody_${idx} .loteInput`).each(function() {
                const disponible = parseInt($(this).data('lote-disponible'), 10) || 0;
                const asignar = Math.min(disponible, restante);
                $(this).val(asignar > 0 ? asignar : 0);
                restante -= asignar;
            });

            actualizarTotales();
        });

        // Actualizar totales
        function actualizarTotales() {
            const numFarmacos = $('#farmacosContainer').children().length;
            let totalUnidades = 0;

            $('.cantidadInput').each(function() {
                totalUnidades += parseInt($(this).val(), 10) || 0;
            });

            $('#totalFarmacos').text(numFarmacos);
            $('#totalUnidades').text(totalUnidades);
            $('#btnGuardar').prop('disabled', numFarmacos === 0);
        }

        $(document).on('change keyup', '.cantidadInput, .loteInput', function() {
            actualizarTotales();
        });

        // Validación antes de enviar
        $('#recetaForm').on('submit', function(e) {
            const numFarmacos = $('#farmacosContainer').children().length;
            if (numFarmacos === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Debe agregar al menos un fármaco', 'error');
                return false;
            }

            // Validar que cada fármaco tenga cantidad y lotes asignados
            let valid = true;
            let errorMsg = '';

            $('.cantidadInput').each(function() {
                const idx = $(this).data('idx');
                const cantidad = parseInt($(this).val(), 10) || 0;
                const stock = parseInt($(this).data('stock'), 10) || 0;
                const card = $(`#farmacoCard_${idx}`);
                const nombre = card.find('.card-title strong').text();

                if (cantidad <= 0) {
                    valid = false;
                    errorMsg = `Ingrese la cantidad para ${nombre}`;
                    return false;
                }

                if (cantidad > stock) {
                    valid = false;
                    errorMsg = `La cantidad excede el stock para ${nombre}`;
                    return false;
                }

                // Validar suma de lotes
                let totalLotes = 0;
                $(`#lotesBody_${idx} .loteInput`).each(function() {
                    totalLotes += parseInt($(this).val(), 10) || 0;
                });

                if (totalLotes !== cantidad) {
                    valid = false;
                    errorMsg = `La suma de lotes (${totalLotes}) no coincide con la cantidad (${cantidad}) para ${nombre}`;
                    return false;
                }
            });

            if (!valid) {
                e.preventDefault();
                Swal.fire('Error de Validación', errorMsg, 'error');
                return false;
            }
        });
    </script>
@endsection
