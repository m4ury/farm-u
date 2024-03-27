@extends('adminlte::page')

@section('title', 'Salidas index')

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
                    SALIDAS
                </h3>
            </div>
            <div class="col-md-12 table-responsive py-3">
                <table id="farmacos" class="table table-hover table-md-responsive table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Fecha / Hora</th>
                            <th>Farmaco</th>
                            <th>Forma farmaceutica</th>
                            <th>Dosis</th>
                            <th>Stock maximo</th>
                            <th>Stock fisico</th>
                            <th>Cantidad salida</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salidas as $salida)
                            <tr>
                                <td>{{ $salida->created_at }}
                                </td>
                                <td>{{ $salida->farmacos->pluck('descripcion')->first() }}</td>
                                <td>{{ $salida->farmacos->pluck('forma_farmaceutica')->first() }}</td>
                                <td>{{ $salida->farmacos->pluck('dosis')->first() }}</td>
                                <td class="text-success text-bold">
                                    {{ $salida->farmacos->pluck('stock_maximo')->first() }}
                                </td>
                                <td class="text-primary text-bold">
                                    {{ $salida->farmacos->pluck('stock_fisico')->first() }}
                                </td>
                                <td class="text-danger text-bold">
                                    {{ $salida->cantidad_salida }}
                                </td>
                                <td class="text-bold text-uppercase text-muted text-center">
                                    {{ $salida->user->fullUserName() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('plugins.Datatables', true)
@section('js')
    {{-- <script src="//cdn.datatables.net/plug-ins/1.12.1/sorting/datetime-moment.js"></script> --}}
    <script>
        $('#forma, #area').select2({
            theme: "classic",
            width: "100%"
        })
    </script>
    <script>
        // $.fn.dataTable.moment('DD-MM-YYYY');
        $("#farmacos").DataTable({
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
            order: [
                [5, 'asc']
            ],
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
