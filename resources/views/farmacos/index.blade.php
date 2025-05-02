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
                            <th>Fecha vencimiento</th>
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
                                <td>{{ $farmaco->stock_maximo }}</td>
                                <td>
                                    {{ $farmaco->stock_fisico }}

                                    @php
                                        $diferencia = $farmaco->stock_maximo - $farmaco->stock_fisico; // Diferencia entre stock máximo y físico
                                        $umbral = $farmaco->stock_fisico * 0.5; // 50% del stock físico
                                    @endphp
                                    @if ($farmaco->stock_fisico > 0 && $diferencia > 0 && $diferencia > $umbral)
                                        <span class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Bajo
                                            Stock</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $farmaco->fecha_vencimiento }}
                                    @if (Carbon\Carbon::create(Carbon\Carbon::now())->diffInDays($farmaco->fecha_vencimiento) < 20 &&
                                            $farmaco->fecha_vencimiento)
                                        <span class="btn rounded-pill bg-gradient-danger btn-xs text-bold ml-3">pronto a
                                            vencer</span>
                                    @elseif (Carbon\Carbon::create(Carbon\Carbon::now())->diffInDays($farmaco->fecha_vencimiento) < 0)
                                        <span
                                            class="btn rounded-pill bg-gradient-danger btn-xs text-bold ml-3">Vencido</span>
                                    @endif
                                </td>
                                <td class="text-bold text-uppercase text-muted text-center">
                                    {{ $farmaco->areas->pluck('nombre_area')->first() }}
                                </td>
                                <td>
                                    {!! Form::open([
                                        'route' => ['farmacos.destroy', $farmaco->id],
                                        'method' => 'DELETE',
                                        'class' => 'confirm',
                                    ]) !!}
                                    {!! Form::button('<i class="fas fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-outline-danger btn-sm',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'title' => 'Eliminar',
                                    ]) !!}
                                    {{-- <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                        data-target="#edit-farmaco"><i class="fas fa-pen"></i>
                                    </button> --}}
                                    <a class="btn btn-outline-primary btn-sm" data-toggle="tooltip" data-placement="top"
                                        title="Editar" href="{{ route('farmacos.edit', $farmaco) }}">
                                        <i class="fas fa-pen">
                                        </i>
                                    </a>
                                    {!! Form::close() !!}
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
