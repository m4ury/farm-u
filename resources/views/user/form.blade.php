<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="rut" class="form-label">Rut</label>
            {{ html()->text('rut')->placeholder('Ej.: 16808000-K')->class('form-control' . ($errors->has('rut') ? ' is-invalid' : ''))->value(old('rut', isset($user) ? $user->rut : null))->required() }}
            @if ($errors->has('rut'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('rut') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-6">
            <label for="name" class="form-label">Nombre Completo</label>
            {{ html()->text('name')->class('form-control' . ($errors->has('name') ? ' is-invalid' : ''))->value(old('name', isset($user) ? $user->name : null))->required() }}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="apellido_p" class="form-label">Apellido Paterno</label>
            {{ html()->text('apellido_p')->class('form-control' . ($errors->has('apellido_p') ? ' is-invalid' : ''))->value(old('apellido_p', isset($user) ? $user->apellido_p : null))->required() }}
            @if ($errors->has('apellido_p'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('apellido_p') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-6">
            <label for="apellido_m" class="form-label">Apellido Materno</label>
            {{ html()->text('apellido_m')->class('form-control' . ($errors->has('apellido_m') ? ' is-invalid' : ''))->value(old('apellido_m', isset($user) ? $user->apellido_m : null)) }}
            @if ($errors->has('apellido_m'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('apellido_m') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            {{ html()->email('email')->class('form-control' . ($errors->has('email') ? ' is-invalid' : ''))->value(old('email', isset($user) ? $user->email : null))->required() }}
            @if ($errors->has('email'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-3">
            <label for="password" class="form-label">Contraseña</label>
            {{ html()->password('password')->class('form-control' . ($errors->has('password') ? ' is-invalid' : ''))->when(!isset($user), function ($input) {
                    return $input->required();
                }) }}
            @if ($errors->has('password'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-md-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            {{ html()->password('password_confirmation')->class('form-control' . ($errors->has('password_confirmation') ? ' is-invalid' : ''))->when(!isset($user), function ($input) {
                    return $input->required();
                }) }}
            @if ($errors->has('password_confirmation'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="type" class="form-label">Tipo de Usuario</label>
            {{ html()->select('type')->options(['admin' => 'Administrador', 'urgencias' => 'Urgencias', 'admin' => 'Administrador', 'farmacia' => 'Farmacia'])->class('form-control')->value(old('type', isset($user) ? $user->type : null))->placeholder('Seleccione un tipo de usuario')->required() }}
        </div>
    </div>
</div>
