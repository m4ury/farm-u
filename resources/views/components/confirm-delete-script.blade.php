@php
    /**
     * Script global para confirmaciones de eliminación con SweetAlert
     * Se incluye una sola vez al final del layout principal
     */
@endphp

<script>
    // Función general para confirmaciones de eliminación
    function confirmarEliminacion(event, mensaje = '¡No podrás revertir esto!') {
        event.preventDefault();

        const form = event.target.closest('form');
        if (!form) return;

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger mx-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: '¿Estás seguro?',
            text: mensaje,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                form.submit();
            }
        });
    }

    // Aplicar a todos los formularios con clase 'confirm' (compatibilidad con código antiguo)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('confirm') && form.method.toUpperCase() === 'POST') {
            const input = form.querySelector('input[name="_method"]');
            if (input && input.value === 'DELETE') {
                confirmarEliminacion(e);
            }
        }
    });

    // Aplicar a botones con clase 'confirm-delete'
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.confirm-delete');
        if (btn) {
            const mensaje = btn.dataset.mensaje || '¿Estás seguro? ¡No podrás revertir esto!';
            confirmarEliminacion(e, mensaje);
        }
    });

    // Aplicar a enlaces con clase 'delete-link'
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.delete-link');
        if (link) {
            e.preventDefault();
            const mensaje = link.dataset.mensaje || '¿Estás seguro? ¡No podrás revertir esto!';
            const url = link.href;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: '¿Estás seguro?',
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    // Crear un formulario temporal para DELETE
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
</script>
