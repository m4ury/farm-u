@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1>Pedidos</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Pedidos</h3>
            <div class="card-tools">
                <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Pedido
                </a>
            </div>
        </div>
        <div class="card-body">

            @if($pedidos->isEmpty())
                <div class="alert alert-info">
                    No hay pedidos registrados. <a href="{{ route('pedidos.create') }}">Crear uno</a>
                </div>
            @else
                <table class="table table-striped table-hover" id="pedidosTable">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Área</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Cantidad de Fármacos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr>
                                <td>#{{ $pedido->id }}</td>
                                <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                <td>{{ $pedido->area->nombre_area }}</td>
                                <td>{{ $pedido->user->name }}</td>
                                <td>
                                    @php
                                        $estado_badge = [
                                            'pendiente' => 'warning',
                                            'aprobado' => 'success',
                                            'parcial' => 'info',
                                            'rechazado' => 'danger',
                                            'completado' => 'success'
                                        ];
                                        $estado_label = [
                                            'pendiente' => 'Pendiente',
                                            'aprobado' => 'Aprobado',
                                            'parcial' => 'Parcial',
                                            'rechazado' => 'Rechazado',
                                            'completado' => 'Completado'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $estado_badge[$pedido->estado] ?? 'secondary' }}">
                                        {{ $estado_label[$pedido->estado] ?? strtoupper($pedido->estado) }}
                                    </span>
                                </td>
                                <td>{{ $pedido->farmacos->count() }}</td>
                                <td>
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{ html()->form('DELETE', route('pedidos.destroy', $pedido))->class('d-inline confirm')->attribute('data-mensaje', '¿Está seguro de que desea eliminar este pedido? Esta acción no se puede deshacer.')->open() }}
                                        @csrf
                                        {{ html()->button('<i class="fas fa-trash"></i>')->class('btn btn-danger btn-sm')->type('submit')->attribute('title', 'Eliminar') }}
                                    {{ html()->form()->close() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('js')
    <script>
        $(document).ready(function() {
            $('#pedidosTable').DataTable({
                language: {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>
@endsection
