<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rut',
        'apellido_p',
        'apellido_m'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function adminlte_desc()
    {
        return strtoupper($this->type);
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function fullUserName()
    {
        return ucfirst($this->name) . " " . ucfirst($this->apellido_p) . " " . ucfirst($this->apellido_m);
    }

    function isAdmin(){
        return $this->type === 'admin';

    }

    function isFarmacia(){
        return $this->type === 'farmacia';

    }

    function isUrgencias(){
        return $this->type === 'urgencias';

    }
}
