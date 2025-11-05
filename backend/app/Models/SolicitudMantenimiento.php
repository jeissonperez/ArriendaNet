<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudMantenimiento extends Model
{
    protected $table = 'solicitudes_mantenimiento';
    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'id_inmueble',
        'id_usuario',
        'fecha_solicitud',
        'tipo_mantenimiento',
        'descripcion',
        'prioridad',
        'estado_solicitud',
        'fecha_atencion',
        'observaciones_arrendador',
        'evidencia_foto'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_atencion' => 'datetime'
    ];

    // Relaciones
    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class, 'id_inmueble');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado_solicitud', 'Pendiente');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado_solicitud', 'En proceso');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado_solicitud', 'Finalizado');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado_solicitud', 'Rechazado');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'Urgente');
    }

    // Atributos calculados
    public function getTiempoTranscurridoAttribute()
    {
        return $this->fecha_solicitud->diffForHumans();
    }

    public function getDuracionAtencionAttribute()
    {
        if ($this->fecha_atencion) {
            return $this->fecha_solicitud->diffInHours($this->fecha_atencion);
        }
        return null;
    }
}