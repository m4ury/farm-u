@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($areas as $area)
                <div class="col-lg-4 col-sm py-3">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h6 class="text-muted">
                                cant. Farmacos {{ $area->farmacos->count() }}
                            </h6>
                            <p class="text-bold text-uppercase"> {{ $area->nombre_area }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
