<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that should be used for authentication.
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo_usuario;
    }

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'doc_usuario',
        'tip_documento',
        'pri_nombre',
        'seg_nombre',
        'pri_apellido',
        'seg_apellido',
        'fec_nacimiento',
        'sex_usuario',
        'cel_usuario',
        'correo_usuario',
        'contraseña',
        'rol_usuario'
    ];

    protected $hidden = [
        'contraseña',
    ];

    protected $casts = [
        'fec_nacimiento' => 'date',
        'contraseña' => 'hashed',
    ];

    // Relaciones
    public function inmueblesArrendador()
    {
        return $this->hasMany(Inmueble::class, 'id_usuario');
    }

    public function contratosComoArrendador()
    {
        return $this->hasMany(Contrato::class, 'id_arrendador');
    }

    public function contratosComoArrendatario()
    {
        return $this->hasMany(Contrato::class, 'id_arrendatario');
    }

    public function solicitudesMantenimiento()
    {
        return $this->hasMany(SolicitudMantenimiento::class, 'id_usuario');
    }

    public function pagosRealizados()
    {
        return $this->hasMany(RegistroPago::class, 'id_arrendatario');
    }

    public function pagosVerificados()
    {
        return $this->hasMany(RegistroPago::class, 'verificado_por');
    }

    // Método para obtener nombre completo
    public function getNombreCompletoAttribute()
    {
        return "{$this->pri_nombre} " . ($this->seg_nombre ? "{$this->seg_nombre} " : "") . 
               "{$this->pri_apellido} {$this->seg_apellido}";
    }
}