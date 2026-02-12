@extends('adminlte::page')

@section('title', 'Lotes')

@section('content')
    @include('components.sweetalert')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-cubes mr-2"></i>Lista de Lotes</h3>
            <div class="card-tools">
                @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                    <a href="{{ route('lotes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Lote
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="lotes-table" class="table table-striped table-hover table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Farmaco</th>
                            <th>Serie</th>
                            <th>Fecha Vencimiento</th>
                            <th>Cantidad Total</th>
                            <th>Disponible</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lotes as $lote)
                            <tr class="{{ $lote->vencido ? 'table-danger' : '' }}">
                                <td class="text-center">{{ $lote->id }}</td>
                                <td>
                                    <a href="{{ route('farmacos.show', $lote->farmaco) }}">
                                        {{ $lote->farmaco->descripcion }} - {{ $lote->farmaco->dosis }} {{ $lote->farmaco->forma_farmaceutica }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $lote->num_serie }}</td>
                                <td class="text-center">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $lote->cantidad }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $lote->cantidad_disponible > 0 ? 'success' : 'warning' }}">
                                        {{ $lote->cantidad_disponible }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($lote->vencido)
                                        <span class="badge badge-danger">Vencido</span>
                                    @elseif($lote->fecha_vencimiento->isPast())
                                        <span class="badge badge-warning">Por Vencer</span>
                                    @else
                                        <span class="badge badge-success">Vigente</span>
                                    @endif
                                </td>
                                <td class="text-center" nowrap>
                                    <a href="{{ route('lotes.show', $lote) }}" class="btn btn-info btn-sm" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                                        <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$lote->vencido && $lote->despachos()->count() === 0)
                                            <form action="{{ route('lotes.destroy', $lote) }}" method="POST" style="display:inline;" class="confirm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
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
        $(function () {
            $("#lotes-table").DataTable({
                paging: true,
                pagingType: 'first_last_numbers',
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: ['excel', 'pdf', 'print'],
                order: [[3, 'asc']],
                language: {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "No hay lotes registrados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ lotes",
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
                }
            });

            $(".confirm").on('submit', function(e) {
                e.preventDefault();
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success mx-2',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: '¿Estás seguro?',
                    text: "Este lote será eliminado permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar!',
                    cancelButtonText: 'No, cancelar!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
