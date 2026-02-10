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
    // Usar delegación de eventos para que funcione con DataTables/paginación
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-farmaco-btn');
        if (!btn) return;
        
        e.preventDefault();
        const farmacoId = btn.dataset.farmacoId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Cargar los datos del farmaco mediante AJAX
        fetch(`/farmacos/${farmacoId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const { farmaco, areas } = data;
            
            // Construir opciones de area
            let areaOptions = '<option value="">Seleccione</option>';
            const areaSeleccionada = farmaco.areas && farmaco.areas.length > 0 ? farmaco.areas[0].id : null;
            
            areas.forEach(area => {
                const selected = areaSeleccionada === area.id ? 'selected' : '';
                areaOptions += `<option value="${area.id}" ${selected}>${area.nombre_area}</option>`;
            });
            
            // Validar y formatear fecha (null or undefined = empty string)
            const fechaValue = farmaco.fecha_vencimiento ? farmaco.fecha_vencimiento.split(' ')[0] : '';
            
            // Construir el formulario dinámicamente
            const formHtml = `
                <form id="editFarmacoForm" action="/farmacos/${farmacoId}" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PATCH">
                    
                    <div class="form-horizontal">
                        <div class="form-group row">
                            <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                            <div class="col-sm">
                                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm" value="${farmaco.descripcion}" placeholder="ej.: paracetamol" disabled>
                            </div>
                            <label for="forma_farmaceutica" class="col-sm-2 col-form-label">Forma farmaceutica:</label>
                            <div class="col-sm">
                                <select name="forma_farmaceutica" id="forma_farmaceutica" class="form-control form-control-sm" disabled>
                                    <option value="${farmaco.forma_farmaceutica}">${farmaco.forma_farmaceutica}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dosis" class="col-sm-2 col-form-label">Dosis:</label>
                            <div class="col-sm">
                                <input type="text" name="dosis" id="dosis" class="form-control form-control-sm" value="${farmaco.dosis}" placeholder="ej.: 100 mg" disabled>
                            </div>
                            <label for="controlado" class="col-sm-2 col-form-label">Controlado:</label>
                            <div class="col-sm">
                                <input type="checkbox" name="controlado" id="controlado" class="form-control my-2 controlado" value="1" ${farmaco.controlado ? 'checked' : ''} disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fecha_vencimiento" class="col-sm-2 col-form-label">Vencimiento:</label>
                            <div class="col-sm-2">
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control form-control-sm" value="${fechaValue}">
                            </div>
                            <label for="stock_maximo" class="col-sm-2 col-form-label">Stock maximo:</label>
                            <div class="col-sm">
                                <input type="number" name="stock_maximo" id="stock_maximo" class="form-control form-control-sm" value="${farmaco.stock_maximo}" disabled>
                            </div>
                            <label for="stock_fisico" class="col-sm-2 col-form-label">Stock fisico:</label>
                            <div class="col-sm">
                                <input type="number" name="stock_fisico" id="stock_fisico" class="form-control form-control-sm" value="${farmaco.stock_fisico}">
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
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
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
            console.error('Error:', error);
            alert('Error al cargar los datos del fármaco');
        });
    });
</script>
