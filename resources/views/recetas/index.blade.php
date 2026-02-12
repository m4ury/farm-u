@extends('adminlte::page')

@section('title', 'Recetas / DAU')

@section('content')
    <div class="container-fluid my-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <a class="btn bg-gradient-info btn-sm mr-3" title="Volver" href="{{ route('home') }}">
                        <i class="fas fa-arrow-alt-circle-left"></i>
                        Volver
                    </a>
                    <i class="fas fa-file-prescription px-2" style="color:rgb(38, 0, 255)"></i>
                    RECETAS / DAU
                </h3>
                <div class="card-tools">
                    <a href="{{ route('recetas.create') }}" class="btn bg-gradient-success btn-sm">
                        <i class="fas fa-plus-circle"></i> Nueva Receta / DAU
                    </a>
                </div>
            </div>
            <div class="col-md-12 table-responsive py-3">
                <table id="recetasTable" class="table table-hover table-md-responsive table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Fecha</th>
                            <th>Num. DAU / Receta</th>
                            <th>Área</th>
                            <th>Fármacos</th>
                            <th>Total Unidades</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recetas as $receta)
                            <tr>
                                <td class="text-center">{{ $receta->fecha_receta->format('d-m-Y') }}</td>
                                <td class="text-bold">{{ $receta->numero_dau }}</td>
                                <td>{{ $receta->area->nombre_area ?? 'Farmacia Central' }}</td>
                                <td>
                                    @foreach ($receta->salidas as $salida)
                                        <span class="badge badge-info">
                                            {{ $salida->farmaco->descripcion ?? 'N/A' }}
                                            ({{ $salida->cantidad_salida }})
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center text-bold text-danger">
                                    {{ $receta->salidas->sum('cantidad_salida') }}
                                </td>
                                <td class="text-bold text-uppercase text-muted text-center">
                                    {{ $receta->user->fullUserName() }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('recetas.show', $receta) }}"
                                        class="btn btn-outline-primary btn-sm"
                                        title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
    <script>
        $("#recetasTable").DataTable({
            dom: 'Bfrtip',
            paging: true,
            pagingType: 'first_last_numbers',
            pageLength: 10,
            buttons: ['csv', 'excel', 'pdf'],
            order: [[0, 'desc']],
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
@endsection
