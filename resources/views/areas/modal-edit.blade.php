<div class="modal fade py-3" id="edit-area" tabindex="-1" role="dialog" aria-labelledby="editAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAreaModalLabel">Editar Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editAreaBody">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Al hacer click en el botón de editar área
        document.querySelectorAll('.edit-area-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const areaId = this.dataset.areaId;
                const areaName = this.dataset.areaName;
                const areaDescription = this.dataset.areaDescription;
                
                // Construir el formulario dinámicamente
                const formHtml = `
                    <form action="/areas/${areaId}" method="POST">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                        <input type="hidden" name="_method" value="PATCH">
                        
                        <div class="form-horizontal">
                            <div class="form-group row">
                                <label for="nombre_area" class="col-sm-4 col-form-label">Nombre area:</label>
                                <div class="col-sm">
                                    <input type="text" name="nombre_area" id="nombre_area" class="form-control form-control-sm" value="${areaName}" placeholder="ej.: Urgencias">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="descripcion_area" class="col-sm-4 col-form-label">Descripcion area:</label>
                                <div class="col-sm">
                                    <textarea name="descripcion_area" id="descripcion_area" class="form-control form-control-sm" placeholder="opcional, breve descripcion">${areaDescription}</textarea>
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
                
                document.getElementById('editAreaBody').innerHTML = formHtml;
                $('#edit-area').modal('show');
            });
        });
    });
</script>
