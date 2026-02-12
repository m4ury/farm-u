@extends('adminlte::page')

@section('title', 'Farmacos index')

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
                    FARMACOS
                </h3>
            </div>
            <div class="col-md-12 table-responsive py-3">
                <table id="farmacos" class="table table-hover table-md-responsive table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Farmaco</th>
                            <th>Forma Farmaceutica</th>
                            <th>Dosis</th>
                            <th>Stock maximo</th>
                            <th>Stock fisico</th>
                            <th>Area</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($farmacos as $farmaco)
                            <tr>
                                <td class="text-uppercase">{{ $farmaco->descripcion }}
                                    @if ($farmaco->controlado)
                                        <p class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Controlado</P>
                                    @endif
                                </td>
                                <td>{{ $farmaco->forma_farmaceutica }}</td>
                                <td>{{ $farmaco->dosis }}</td>
                                <td>{{ $farmaco->stock_minimo }}</td>
                                <td>
                                    {{ $stock_fisico = $farmaco->getStockFisicoCalculado() }}

                                    @php
                                        $diferencia = $farmaco->stock_minimo - $stock_fisico; // Diferencia entre stock máximo y físico calculado
                                        $umbral = $stock_fisico * 0.5; // 50% del stock físico calculado
                                    @endphp
                                    @if ($stock_fisico > 0 && $diferencia > 0 && $diferencia > $umbral)
                                        <span class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Bajo
                                            Stock</span>
                                    @endif
                                </td>
                                <td class="text-bold text-uppercase text-muted text-center">
                                    {{ $farmaco->areas->pluck('nombre_area')->first() }}
                                </td>
                                <td>
                                    {{ html()->form('DELETE', route('farmacos.destroy', $farmaco->id))->class('confirm')->open() }}
                                    {{ html()
                                        ->button('<i class="fas fa-trash"></i>')
                                        ->class('btn btn-outline-danger btn-sm')
                                        ->type('submit')
                                        ->attribute('data-toggle', 'tooltip')
                                        ->attribute('data-placement', 'top')
                                        ->attribute('title', 'Eliminar')
                                        ->attribute('data-mensaje', 'Este farmaco será eliminado permanentemente. ¿Estás seguro?') }}
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-farmaco-btn" data-toggle="tooltip" data-placement="top"
                                        title="Editar" data-farmaco-id="{{ $farmaco->id }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    {{ html()->form()->close() }}
                                    <a class="btn btn-outline-info btn-sm" data-toggle="tooltip" data-placement="bottom"
                                        title="Ver detalle" href="{{ route('farmacos.show', $farmaco) }}"><i
                                            class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group d-inline-flex align-self-stretch">
                    <button type="button" class="btn btn-primary my-3" data-toggle="modal" data-target="#new-farmaco"><i
                            class="fas fa-pills"></i>
                        Nuevo Farmaco
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('farmacos.modal')
@include('farmacos.modal-edit')
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
