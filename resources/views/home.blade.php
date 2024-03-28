@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos
                            {{ $botiquin->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">botiquín urgencias</p>
                    </div>
                    <h6 class="text-primary m-2 text-bold">
                        Stock maximo: {{ $botiquin->pluck('stock_maximo')->sum() }}
                    </h6>
                    <h6
                        class="{{ $botiquin->pluck('stock_fisico')->sum() < $botiquin->pluck('stock_maximo')->sum() ? 'text-danger' : 'text-success' }} mx-2 text-bold">
                        Stock fisico: {{ $botiquin->pluck('stock_fisico')->sum() }}
                    </h6>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.botiquin') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos {{ $carro->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">carro de paro urgencias</p>
                    </div>
                    <h6 class="text-primary m-2 text-bold">
                        Stock maximo: {{ $carro->pluck('stock_maximo')->sum() }}
                    </h6>
                    <h6
                        class="{{ $carro->pluck('stock_fisico')->sum() < $carro->pluck('stock_maximo')->sum() ? 'text-danger' : 'text-success' }} mx-2 text-bold">
                        Stock fisico: {{ $carro->pluck('stock_fisico')->sum() }}
                    </h6>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.carro') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos {{ $maletin->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">maletin urgencias</p>
                    </div>
                    <h6 class="text-primary m-2 text-bold">
                        Stock maximo: {{ $maletin->pluck('stock_maximo')->sum() }}
                    </h6>
                    <h6
                        class="{{ $maletin->pluck('stock_fisico')->sum() < $maletin->pluck('stock_maximo')->sum() ? 'text-danger' : 'text-success' }} mx-2 text-bold">
                        Stock fisico: {{ $maletin->pluck('stock_fisico')->sum() }}
                    </h6>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.maletin') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-12 table-responsive py-3">
            <table id="farmacos" class="table table-hover table-md-responsive table-bordered">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th>Farmaco</th>
                        <th>Forma farmaceutica</th>
                        <th>Dosis</th>
                        <th>Stock Maximo</th>
                        <th>Stock fisico</th>
                        <th>Area</th>
                        <th>Reponer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bajo as $b)
                        <tr>
                            <td>
                                {{ $b->descripcion }}
                            </td>
                            <td>
                                {{ $b->forma_farmaceutica }}
                            </td>
                            <td>
                                {{ $b->dosis }}
                            </td>
                            <td>
                                {{ $b->stock_maximo }}
                            </td>
                            <td>
                                {{ $b->stock_fisico }}
                            </td>
                            <td>
                                {{ $b->areas->pluck('nombre_area')->first() ?? '' }}
                            </td>
                            <td>
                                {{ $b->stock_maximo - $b->stock_fisico }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('plugins.Datatables', true)
@section('js')
    <script>
        $("#farmacos").DataTable({
            paging: true,
            pagingType: 'first_last_numbers',
            pageLength: 5,
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
        });
    </script>
@endsection
