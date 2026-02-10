@extends('adminlte::page')

@section('title', 'Areas index')

@section('content')
    <div class="container-fluid my-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <a class="btn bg-gradient-info btn-sm mr-3" title="Volver" href="{{ route('home') }}">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                        Volver
                    </a>
                    <i class="fas fa-pills px-2" style="color:rgb(38, 0, 255)"></i>
                    AREAS
                </h3>
            </div>
            <div class="col-md-12 table-responsive py-3">
                <table id="areas" class="table table-hover table-md-responsive table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($areas as $area)
                            <tr>
                                <td class="text-uppercase">{{ $area->nombre_area }}</td>
                                <td>{{ $area->descripcion_area }}</td>
                                <td>
                                    {{ html()->form('DELETE', route('areas.destroy', $area->id))->class('confirm')->open() }}
                                    {{ html()
                                        ->button('<i class="fas fa-trash"></i>')
                                        ->class('btn btn-outline-danger btn-sm')
                                        ->type('submit')
                                        ->attribute('data-toggle', 'tooltip')
                                        ->attribute('data-placement', 'top')
                                        ->attribute('title', 'Eliminar') }}
                                    {{-- <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                        data-target="#edit-farmaco"><i class="fas fa-pen"></i>
                                    </button> --}}
                                    <a class="btn btn-outline-primary btn-sm" data-toggle="tooltip" data-placement="top"
                                        title="Editar" href="{{ route('areas.edit', $area) }}">
                                        <i class="fas fa-pen">
                                        </i>
                                    </a>
                                    {{ html()->form()->close() }}
                                    {{-- <a class="btn btn-outline-primary btn-sm" data-toggle="tooltip" data-placement="bottom"
                                        title="farmaco" href="{{ route('farmacos.show', $farmaco) }}" target="_blank"><i
                                            class="fas fa-envelope"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group d-inline-flex align-self-stretch">
                    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#new-area"><i
                            class="fas fa-calendar-check"></i>
                        Nueva area
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('areas.modal')
@section('plugins.Datatables', true)
@section('js')
    {{-- <script src="//cdn.datatables.net/plug-ins/1.12.1/sorting/datetime-moment.js"></script> --}}
    <script>
        $('#forma').select2({
            theme: "classic",
            width: "100%"
        })
    </script>
    <script>
        // $.fn.dataTable.moment('DD-MM-YYYY');
        $("#areas").DataTable({
            paging: true,
            pagingType: 'first_last_numbers',
            pageLength: 8,
            dom: 'Bfrtip',
            buttons: [
                'colvis',
                'excel',
                'pdf',
                'print',
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
    </script>

    <script>
        $(".confirm").on('submit', function(e) {
            e.preventDefault();
            //console.log('alerta');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success mx-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Estas seguro?',
                text: "No podras revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, borrar!',
                cancelButtonText: 'No, cancelar!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.submit();
                    swalWithBootstrapButtons.fire(
                        'Eliminado!',
                        'registro Eliminado.',
                        'success'
                    )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'Tranki.. no ha pasaso nada',
                        'error'
                    )
                }
            })
        })
    </script>
@endsection
