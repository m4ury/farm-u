@extends('adminlte::page')

@section('title', 'Farmacos Maletin Urgencias')

@section('content')
    <div class="container-fluid my-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <a class="btn bg-gradient-info btn-sm mr-3" title="Volver" href="{{ url()->previous() }}">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                        Volver
                    </a>
                    <i class="fas fa-pills px-2" style="color:rgb(38, 0, 255)"></i>
                    <span class="text-bold">Maletin Urgencias</span>
                </h3>
            </div>
            <div class="col-md-12 table-responsive py-3">
                <table id="areas" class="table table-hover table-md-responsive table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Farmaco</th>
                            <th>Forma Farmaceutica</th>
                            <th>Dosis</th>
                            <th>Stock maximo</th>
                            <th>Stock fisico</th>
                            <th>Fecha vencimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($areas as $area)
                            <tr>
                                <td class="text-uppercase" nowrap>{{ $area->descripcion ?? '' }}
                                    @if ($area->controlado)
                                        <p class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Controlado</P>
                                    @endif
                                </td>
                                <td>{{ $area->forma_farmaceutica }}</td>
                                <td nowrap>{{ $area->dosis }}</td>
                                <td>{{ $area->stock_maximo }}</td>
                                <td nowrap>
                                    {{ $area->stock_fisico }}
                                    @if ($area->stock_fisico < 5 && $area->stock_fisico > 0)
                                        <span class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Bajo
                                            Stock</span>
                                    @endif
                                </td>
                                <td nowrap>
                                    {{ $area->fecha_vencimiento }}
                                    @if (Carbon\Carbon::create(Carbon\Carbon::now())->diffInDays($area->fecha_vencimiento) < 20 && $area->fecha_vencimiento)
                                        <span class="btn rounded-pill bg-gradient-danger btn-xs text-bold ml-3">Pronto a
                                            Vencer</span>
                                    @elseif (Carbon\Carbon::create(Carbon\Carbon::now())->diffInDays($area->fecha_vencimiento) < 0)
                                        <span
                                            class="btn rounded-pill bg-gradient-danger btn-xs text-bold ml-3">Vencido</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-outline-warning btn-sm {{ $area->stock_fisico < 1 ? 'disabled' : '' }}"
                                        href="#" data-toggle="modal" data-target="#productModal{{ $area->id }}"
                                        title="Generar Salida"><i class="fas fa-share-square"></i>
                                    </a>
                                </td>
                            </tr>
                            @include('salidas.modal')
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
