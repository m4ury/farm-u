@extends('adminlte::page')

@section('title', 'Crear Lote')

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-8 offset-md-2 mt-4">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Nuevo Lote</h3>
                </div>
                <div class="card-body">

                    <form action="{{ route('lotes.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="farmaco_id">Farmaco <span class="text-danger">*</span></label>
                            <select id="farmaco_id" name="farmaco_id" class="form-control select2-farmaco @error('farmaco_id') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($farmacos as $farmaco)
                                    <option value="{{ $farmaco->id }}" {{ old('farmaco_id') == $farmaco->id ? 'selected' : '' }}>
                                        {{ $farmaco->descripcion }} ({{ $farmaco->forma_farmaceutica }} - DOSIS: {{ $farmaco->dosis }})
                                    </option>
                                @endforeach
                            </select>
                            @error('farmaco_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="num_serie">Número de Serie <span class="text-danger">*</span></label>
                            <input type="text" id="num_serie" name="num_serie" class="form-control @error('num_serie') is-invalid @enderror" 
                                   value="{{ old('num_serie') }}" placeholder="Ej: LOTE-2024-001" required>
                            @error('num_serie')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fecha_vencimiento">Fecha de Vencimiento <span class="text-danger">*</span></label>
                                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       value="{{ old('fecha_vencimiento') }}" required>
                                @error('fecha_vencimiento')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                                <input type="number" id="cantidad" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" 
                                       value="{{ old('cantidad', 1) }}" min="1" required>
                                @error('cantidad')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Lote
                            </button>
                            <a href="{{ route('lotes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('plugins.Select2', true)

@section('js')
<script>
    $(document).ready(function() {
        $('.select2-farmaco').select2({
            theme: 'bootstrap4',
            placeholder: '-- Seleccionar --',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
