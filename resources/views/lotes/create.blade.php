@extends('adminlte::page')

@section('title', 'Crear Lote')

@section('content_header')
    <h1>Nuevo Lote de Farmaco</h1>
@endsection

@section('content')
    @include('components.sweetalert')
    
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Registrar Nuevo Lote</h3>
                </div>
                <div class="card-body">

                    <form action="{{ route('lotes.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="farmaco_id">Farmaco *</label>
                            <select id="farmaco_id" name="farmaco_id" class="form-control @error('farmaco_id') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($farmacos as $farmaco)
                                    <option value="{{ $farmaco->id }}" {{ old('farmaco_id') == $farmaco->id ? 'selected' : '' }}>
                                        {{ $farmaco->descripcion }} ({{ $farmaco->forma_farmaceutica }})
                                    </option>
                                @endforeach
                            </select>
                            @error('farmaco_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="num_serie">Número de Serie *</label>
                            <input type="text" id="num_serie" name="num_serie" class="form-control @error('num_serie') is-invalid @enderror" 
                                   value="{{ old('num_serie') }}" placeholder="Ej: LOTE-2024-001" required>
                            @error('num_serie')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fecha_vencimiento">Fecha de Vencimiento *</label>
                                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       value="{{ old('fecha_vencimiento') }}" required>
                                @error('fecha_vencimiento')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="cantidad">Cantidad *</label>
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
