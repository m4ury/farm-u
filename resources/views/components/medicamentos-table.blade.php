@props(['items' => [], 'showActions' => true])

<div class="col-md-12 table-responsive py-3">
    <table id="medicamentosTable" class="table table-hover table-md-responsive table-bordered">
        <thead class="thead-light">
            <tr class="text-center">
                <th>Farmaco</th>
                <th>Forma Farmaceutica</th>
                <th>Dosis</th>
                <th>Stock maximo</th>
                <th>Stock fisico</th>
                @if ($showActions)
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td class="text-uppercase" nowrap>{{ $item->descripcion ?? '' }}
                        @if ($item->controlado)
                            <p class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Controlado</p>
                        @endif
                    </td>
                    <td>{{ $item->forma_farmaceutica }}</td>
                    <td nowrap>{{ $item->dosis }}</td>
                    <td>{{ $item->stock_maximo }}</td>
                    <td>{{ $item->getStockFisicoCalculado() }}</td>
                    @if ($showActions)
                        <td>
                            <a class="btn btn-outline-warning btn-sm {{ $item->getStockFisicoCalculado() < 1 ? 'disabled' : '' }} {{ auth()->user()->type == 'farmacia' ? 'disabled' : '' }}"
                                href="#" data-toggle="modal" data-target="#productModal{{ $item->id }}"
                                title="Generar Salida"><i class="fas fa-share-square"></i>
                            </a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay medicamentos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modales fuera de la tabla para evitar HTML inválido --}}
@if ($showActions)
    @foreach ($items as $item)
        @include('salidas.modal', ['area' => $item])
    @endforeach
@endif
