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
                <th>Fecha vencimiento</th>
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
                    <td nowrap>
                        {{ $item->stock_fisico }}
                        @if ($item->stock_fisico < 5 && $item->stock_fisico > 0)
                            <span class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3">Bajo Stock</span>
                        @endif
                    </td>
                    <td nowrap>
                        {{ $item->fecha_vencimiento }}
                        @if ($item->fecha_vencimiento && Carbon\Carbon::parse($item->fecha_vencimiento)->isPast())
                            <span class="btn rounded-pill bg-gradient-danger btn-xs text-bold ml-3 text-uppercase">Vencido</span>
                        @elseif ($item->fecha_vencimiento && Carbon\Carbon::now()->diffInDays(Carbon\Carbon::parse($item->fecha_vencimiento)) < 20)
                            <span class="btn rounded-pill bg-gradient-warning btn-xs text-bold ml-3 text-uppercase">Pronto a Vencer</span>
                        @endif
                    </td>
                    @if ($showActions)
                        <td>
                            <a class="btn btn-outline-warning btn-sm {{ $item->stock_fisico < 1 ? 'disabled' : '' }} {{ $item->fecha_vencimiento && Carbon\Carbon::parse($item->fecha_vencimiento)->isPast() ? 'disabled' : '' }} {{ auth()->user()->type == 'farmacia' ? 'disabled' : '' }}"
                                href="#" data-toggle="modal" data-target="#productModal{{ $item->id }}"
                                title="Generar Salida"><i class="fas fa-share-square"></i>
                            </a>
                        </td>
                    @endif
                </tr>
                @include('salidas.modal', ['area' => $item])
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No hay medicamentos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
