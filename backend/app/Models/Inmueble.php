<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    protected $table = 'inmuebles';
    protected $primaryKey = 'id_inmueble';

    protected $fillable = [
        'id_usuario',
        'direccion',
        'tipo_inmueble',
        'valor_arriendo',
        'estado'
    ];

    protected $casts = [
        'valor_arriendo' => 'decimal:2'
    ];

    // Relaciones
    public function propietario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'id_inmueble');
    }

    public function solicitudesMantenimiento()
    {
        return $this->hasMany(SolicitudMantenimiento::class, 'id_inmueble');
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'Disponible');
    }

    public function scopeArrendados($query)
    {
        return $query->where('estado', 'Arrendado');
    }

    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado', 'Mantenimiento');
    }

    // Método para obtener el contrato activo
    public function getContratoActivoAttribute()
    {
        return $this->contratos()
                    ->where('estado_contrato', 'Activo')
                    ->first();
    }
}