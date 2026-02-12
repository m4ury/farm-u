@props(['items' => [], 'showActions' => true, 'areaModel' => null])

<div class="col-md-12 table-responsive py-3">
    <table id="medicamentosTable" class="table table-hover table-md-responsive table-bordered">
        <thead class="thead-light">
            <tr class="text-center">
                <th>Farmaco</th>
                <th>Forma Farmaceutica</th>
                <th>Dosis</th>
                <th>Stock minimo</th>
                <th>Stock en {{ $areaModel ? $areaModel->nombre_area : 'total' }}</th>
                @if ($showActions)
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                @php
                    $stockMostrar = $areaModel
                        ? $item->getStockEnArea($areaModel->id)
                        : $item->getStockFisicoCalculado();
                @endphp
                <tr>
                    <td class="text-uppercase" nowrap>
                        <a href="{{ route('farmacos.show', $item) }}" class="text-dark" title="Ver detalle del fármaco">
                            {{ $item->descripcion ?? '' }}
                        </a>
                        @if ($item->controlado)
                            <p class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Controlado</p>
                        @endif
                    </td>
                    <td>{{ $item->forma_farmaceutica }}</td>
                    <td nowrap>{{ $item->dosis }}</td>
                    <td>{{ $item->stock_minimo }}</td>
                    <td>{{ $stockMostrar }}</td>
                    @if ($showActions)
                        <td>
                            <a class="btn btn-outline-warning btn-sm {{ $stockMostrar < 1 ? 'disabled' : '' }} {{ auth()->user()->type == 'farmacia' ? 'disabled' : '' }}"
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
        @include('salidas.modal', ['area' => $item, 'areaModel' => $areaModel])
    @endforeach
@endif
