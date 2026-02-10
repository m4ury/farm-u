@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content')

    <div class="row mt-4">
        <div class="col-md-12 py-3">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-users"></i>
                        Usuarios
                        <a href="{{ url('users/create') }}" class="btn btn-success btn-sm float-right">Crear Usuario</a>
                    </h4>
                </div>
                <div class="card-body">
                    <table id="users" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Rut</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Tipo de Usuario</th>
                                <th>Accciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr @if ($user->trashed()) class="table-danger" @endif>
                                    <td>{{ $user->rut }}</td>
                                    <td class="text-uppercase">
                                        {{ $user->fullUserName() }}
                                        @if ($user->trashed())
                                            <span class="badge badge-danger ml-2">Deshabilitado</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->type }}</td>

                                    <td class="text-center text-uppercase">
                                        @if ($user->trashed())
                                            {{ html()->form('POST', route('users.restore', $user->id))->class('d-inline')->open() }}
                                                @csrf
                                                {{ html()->button('Restaurar')->class('btn btn-success btn-sm')->type('submit') }}
                                            {{ html()->form()->close() }}
                                        @else
                                            <a class="btn btn-primary btn-sm" title="Editar Usuario"
                                                href="{{ route('users.edit', $user->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{ html()->form('DELETE', route('users.destroy', $user->id))->class('d-inline')->open() }}
                                                @csrf
                                                {{ html()->button('<i class="fas fa-trash"></i>')->class('btn btn-danger btn-sm')->type('submit')->attribute('title', 'Deshabilitar Usuario')->attribute('onclick', "return confirm('¿Estás seguro de que deseas deshabilitar este usuario?')") }}
                                            {{ html()->form()->close() }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @stop
        @section('plugins.Datatables', true)
        @section('js')
            <script>
                $("#users").DataTable({
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
        @endsection
