<div class="modal fade py-3" id="edit-user" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editUserBody">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Al hacer click en el botón de editar usuario
        document.querySelectorAll('.edit-user-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.dataset.userId;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Cargar los datos del usuario mediante AJAX
                fetch(`/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const { user } = data;
                    
                    // Construir el formulario dinámicamente
                    const formHtml = `
                        <form action="/users/${userId}" method="POST">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="container">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="rut" class="form-label">Rut</label>
                                        <input type="text" name="rut" id="rut" class="form-control" value="${user.rut}" placeholder="Ej.: 16808000-K" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nombre Completo</label>
                                        <input type="text" name="name" id="name" class="form-control" value="${user.name}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="apellido_p" class="form-label">Apellido Paterno</label>
                                        <input type="text" name="apellido_p" id="apellido_p" class="form-control" value="${user.apellido_p}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido_m" class="form-label">Apellido Materno</label>
                                        <input type="text" name="apellido_m" id="apellido_m" class="form-control" value="${user.apellido_m}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" value="${user.email}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Dejar vacío para no cambiar">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="password_confirmation" class="form-label">Confirmar</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmar contraseña">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Tipo de Usuario</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="">Seleccione un tipo de usuario</option>
                                            <option value="admin" ${user.type === 'admin' ? 'selected' : ''}>Administrador</option>
                                            <option value="urgencias" ${user.type === 'urgencias' ? 'selected' : ''}>Urgencias</option>
                                            <option value="farmacia" ${user.type === 'farmacia' ? 'selected' : ''}>Farmacia</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                                        <button type="submit" class="btn btn-primary w-100">Actualizar</button>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    `;
                    
                    document.getElementById('editUserBody').innerHTML = formHtml;
                    // Inicializar select2 si está disponible
                    const selectElement = document.getElementById('type');
                    if ($ && $.fn.select2 && !selectElement.hasAttribute('data-select2-id')) {
                        $(selectElement).select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            minimumResultsForSearch: Infinity,
                            dropdownParent: $('#edit-user')
                        });
                    }
                    $('#edit-user').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del usuario');
                });
            });
        });
    });
</script>
