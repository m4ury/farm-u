<div class="modal fade" id="edit-farmaco" tabindex="-1" role="dialog" aria-labelledby="editFarmacoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFarmacoModalLabel">Editar Farmaco</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editFarmacoBody">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>
    </div>
</div>

<script>
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);

    // Usar delegación de eventos para que funcione con DataTables/paginación
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-farmaco-btn');
        if (!btn) return;

        e.preventDefault();
        const farmacoId = btn.dataset.farmacoId;
        const editUrl = btn.dataset.editUrl;
        const updateUrl = btn.dataset.updateUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Cargar los datos del farmaco mediante AJAX
        fetch(editUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON. Status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const { farmaco, areas } = data;
            const stockMinimoPorArea = data.stockMinimoPorArea || [];
            const stockMinimoMap = {};

            stockMinimoPorArea.forEach(item => {
                stockMinimoMap[String(item.area_id)] = item.stock_minimo || 0;
            });

            // Construir opciones de area para farmacos sin area asociada.
            let areaOptions = '<option value="">Seleccione</option>';
            const areaSeleccionada = farmaco.areas && farmaco.areas.length > 0 ? farmaco.areas[0].id : null;

            areas.forEach(area => {
                const selected = areaSeleccionada === area.id ? 'selected' : '';
                const stockMinimo = stockMinimoMap[String(area.id)] || 0;
                areaOptions += `<option value="${escapeHtml(area.id)}" data-stock-minimo="${escapeHtml(stockMinimo)}" ${selected}>${escapeHtml(area.nombre_area)}</option>`;
            });

            const stockMinimoSeleccionado = areaSeleccionada ? (stockMinimoMap[String(areaSeleccionada)] || 0) : 0;
            const stockMinimoFields = stockMinimoPorArea.length
                ? stockMinimoPorArea.map(item => {
                    const areaId = escapeHtml(item.area_id);
                    const areaNombre = escapeHtml(item.area);
                    const stockMinimo = escapeHtml(item.stock_minimo || 0);

                    return `
                        <div class="form-group row">
                            <label for="stock_minimo_${areaId}" class="col-sm-2 col-form-label">${areaNombre}:</label>
                            <div class="col-sm">
                                <input type="number" name="stock_minimos[${areaId}]" id="stock_minimo_${areaId}" class="form-control form-control-sm" value="${stockMinimo}" placeholder="${stockMinimo}" @if(!auth()->user()->isAdmin()) disabled @endif>
                            </div>
                        </div>
                    `;
                }).join('')
                : `
                    <div class="form-group row">
                        <label for="stock_minimo" class="col-sm-2 col-form-label">Stock mínimo:</label>
                        <div class="col-sm">
                            <input type="number" name="stock_minimo" id="stock_minimo" class="form-control form-control-sm" value="${escapeHtml(stockMinimoSeleccionado)}" placeholder="${escapeHtml(stockMinimoSeleccionado)}" @if(!auth()->user()->isAdmin()) disabled @endif>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="area_id" class="col-sm col-form-label">Area:</label>
                        <div class="col">
                            <select name="area_id" id="area_id" class="form-control form-control-sm">
                                ${areaOptions}
                            </select>
                        </div>
                    </div>
                `;

            // Construir el formulario dinámicamente
            const formHtml = `
                <form id="editFarmacoForm" action="${updateUrl}" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PATCH">

                    <div class="form-horizontal">
                        <div class="form-group row">
                            <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                            <div class="col-sm">
                                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm" value="${escapeHtml(farmaco.descripcion)}" placeholder="ej.: paracetamol" @if(!auth()->user()->isAdmin()) disabled @endif>
                            </div>
                            <label for="forma_farmaceutica" class="col-sm-2 col-form-label">Forma farmaceutica:</label>
                            <div class="col-sm">
                                <select name="forma_farmaceutica" id="forma_farmaceutica" class="form-control form-control-sm" @if(!auth()->user()->isAdmin()) disabled @endif>
                                    <option value="${escapeHtml(farmaco.forma_farmaceutica)}">${escapeHtml(farmaco.forma_farmaceutica)}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dosis" class="col-sm-2 col-form-label">Dosis:</label>
                            <div class="col-sm">
                                <input type="text" name="dosis" id="dosis" class="form-control form-control-sm" value="${escapeHtml(farmaco.dosis)}" placeholder="ej.: 100 mg" @if(!auth()->user()->isAdmin()) disabled @endif>
                            </div>
                            <label for="controlado" class="col-sm-2 col-form-label">Controlado:</label>
                            <div class="col-sm">
                                <input type="checkbox" name="controlado" id="controlado" class="form-control my-2 controlado" value="1" ${farmaco.controlado ? 'checked' : ''} @if(!auth()->user()->isAdmin()) disabled @endif>
                            </div>
                        </div>
                        ${stockMinimoFields}
                        <hr>
                        <div class="row py-3 px-3">
                            <div class="col">
                                <button type="submit" class="btn bg-gradient-success btn-sm btn-block">Actualizar</button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn bg-gradient-secondary btn-sm btn-block" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </form>
            `;

            document.getElementById('editFarmacoBody').innerHTML = formHtml;
            $('#edit-farmaco').modal('show');

            // Agregar event listener para el envío del formulario
            document.getElementById('editFarmacoForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#edit-farmaco').modal('hide');
                            // Recargar la tabla si existe DataTables
                            if ($.fn.DataTable.isDataTable('#farmacos-table')) {
                                $('#farmacos-table').DataTable().ajax.reload();
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al actualizar el fármaco',
                        confirmButtonText: 'OK'
                    });
                });
            });
        })
        .catch(error => {
            console.error('Error al cargar farmaco:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error al cargar',
                text: error.message || 'Error al cargar los datos del fármaco. Verifica que el fármaco existe y tienes permisos de acceso.',
                confirmButtonText: 'OK'
            });
        });
    });

    $('#edit-farmaco').on('hide.bs.modal', function() {
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
</script>
