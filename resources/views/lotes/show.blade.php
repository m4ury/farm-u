@extends('adminlte::page')

@section('title', 'Detalle Lote')

@section('content_header')
    <h1>Detalle del Lote</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Información del Lote</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><strong>ID Lote:</strong></label>
                                <p>{{ $lote->id }}</p>
                            </div>

                            <div class="form-group">
                                <label><strong>Farmaco:</strong></label>
                                <p>
                                    <a href="{{ route('farmacos.show', $lote->farmaco) }}">
                                        {{ $lote->farmaco->descripcion }}
                                    </a>
                                </p>
                            </div>

                            <div class="form-group">
                                <label><strong>Número de Serie:</strong></label>
                                <p>{{ $lote->num_serie }}</p>
                            </div>

                            <div class="form-group">
                                <label><strong>Cantidad Total:</strong></label>
                                <p><span class="badge badge-primary">{{ $lote->cantidad }}</span></p>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><strong>Cantidad Disponible:</strong></label>
                                <p>
                                    <span class="badge badge-{{ $lote->cantidad_disponible > 0 ? 'success' : 'danger' }}">
                                        {{ $lote->cantidad_disponible }}
                                    </span>
                                </p>
                            </div>

                            <div class="form-group">
                                <label><strong>Fecha de Vencimiento:</strong></label>
                                <p>{{ $lote->fecha_vencimiento->format('d/m/Y') }}</p>
                            </div>

                            <div class="form-group">
                                <label><strong>Estado:</strong></label>
                                <p>
                                    @if($lote->vencido)
                                        <span class="badge badge-danger">Vencido</span>
                                    @else
                                        <span class="badge badge-success">Vigente</span>
                                    @endif
                                </p>
                            </div>

                            <div class="form-group">
                                <label><strong>Creado:</strong></label>
                                <p>{{ $lote->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        @if(auth()->user()->isAdmin() || auth()->user()->isFarmacia())
                            <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            @if(!$lote->vencido)
                                <form action="{{ route('lotes.marcarVencido', $lote) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="button" class="btn btn-danger confirm-action" data-action="marcar-vencido">
                                        <i class="fas fa-times-circle"></i> Marcar Vencido
                                    </button>
                                </form>
                            @endif

                            @if($lote->despachos()->count() === 0)
                                <form action="{{ route('lotes.destroy', $lote) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger confirm-delete">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        @endif

                        <a href="{{ route('lotes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Despachos Realizados</h3>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($lote->despachos()->count() === 0)
                        <p class="text-center text-muted">Sin despachos registrados</p>
                    @else
                        <ul class="list-group">
                            @foreach($lote->despachos as $despacho)
                                <li class="list-group-item">
                                    <strong>{{ $despacho->cantidad }}</strong> unidades<br>
                                    <small class="text-muted">Área: {{ $despacho->area->nombre ?? 'N/A' }}</small><br>
                                    <small class="text-muted">{{ $despacho->fecha_aprobacion->format('d/m/Y H:i') }}</small><br>
                                    <small>Por: {{ $despacho->usuarioAprobador->name ?? 'N/A' }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Confirmación para eliminaciones
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

        // Confirmación para marcar vencido
        document.querySelectorAll('.confirm-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const action = this.dataset.action;
                
                if (action === 'marcar-vencido') {
                    Swal.fire({
                        title: '¿Marcar como vencido?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, marcar vencido',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'btn btn-danger mx-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.value) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection
