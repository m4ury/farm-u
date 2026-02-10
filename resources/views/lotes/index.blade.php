@extends('adminlte::page')

@section('title', 'Lotes')

@section('content_header')
    <h1>Gestión de Lotes</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Lotes</h3>
            <div class="card-tools">
                @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                    <a href="{{ route('lotes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Lote
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">

            <!-- Filtros -->
            <div class="mb-3">
                <form method="GET" action="{{ route('lotes.index') }}" class="form-inline">
                    <input type="text" name="farmaco_id" placeholder="ID Farmaco" class="form-control mr-2">
                    
                    <select name="vencidos" class="form-control mr-2">
                        <option value="">Todos</option>
                        <option value="no">No Vencidos</option>
                        <option value="si">Vencidos</option>
                    </select>
                    
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </form>
            </div>

            @if($lotes->isEmpty())
                <div class="alert alert-info">
                    No hay lotes registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
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
                                    <td>{{ $lote->id }}</td>
                                    <td>
                                        <a href="{{ route('farmacos.show', $lote->farmaco) }}">
                                            {{ $lote->farmaco->descripcion }}
                                        </a>
                                    </td>
                                    <td>{{ $lote->num_serie }}</td>
                                    <td>{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td>{{ $lote->cantidad }}</td>
                                    <td>
                                        <span class="badge badge-{{ $lote->cantidad_disponible > 0 ? 'success' : 'warning' }}">
                                            {{ $lote->cantidad_disponible }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($lote->vencido)
                                            <span class="badge badge-danger">Vencido</span>
                                        @elseif($lote->fecha_vencimiento->isPast())
                                            <span class="badge badge-warning">Por Vencer</span>
                                        @else
                                            <span class="badge badge-success">Vigente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('lotes.show', $lote) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                                            <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-warning btn-sm">
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

                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $lotes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

<script>
    // Confirmación para eliminaciones con SweetAlert
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });
            
            swalWithBootstrapButtons.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
