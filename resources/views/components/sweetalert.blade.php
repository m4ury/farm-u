@php
    // Este partial maneja automáticamente los mensajes flash con SweetAlert
@endphp

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: `{{ session('success') }}`,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-success'
                },
                buttonsStyling: false
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: `{{ session('error') }}`,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: '¡Advertencia!',
                text: `{{ session('warning') }}`,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-warning'
                },
                buttonsStyling: false
            });
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: '¡Información!',
                text: `{{ session('info') }}`,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-info'
                },
                buttonsStyling: false
            });
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessages = {!! json_encode($errors->all()) !!};
            Swal.fire({
                icon: 'error',
                title: '¡Errores de Validación!',
                html: '<ul style="text-align: left;">' +
                    errorMessages.map(msg => '<li>' + msg + '</li>').join('') +
                    '</ul>',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        });
    </script>
@endif
