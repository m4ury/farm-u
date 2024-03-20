@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos
                            {{ $botiquin->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">botiquín urgencias</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.botiquin') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos {{ $carro->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">carro de paro urgencias</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.carro') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-sm py-3">
                <div class="small-box bg-gradient-warning">
                    <div class="inner">
                        <h6 class="text-muted">
                            cant. Farmacos {{ $maletin->count() }}
                        </h6>
                        <p class="text-bold text-uppercase">maletin urgencias</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('areas.maletin') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
