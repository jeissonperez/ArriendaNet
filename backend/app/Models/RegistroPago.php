<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroPago extends Model
{
    protected $table = 'registro_pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_cuenta',
        'id_arrendatario',
        'monto_pagado',
        'fecha_pago',
        'metodo_pago',
        'referencia_pago',
        'estado_verificacion',
        'descripcion_soporte',
        'archivo_soporte',
        'fecha_subida_soporte',
        'verificado_por'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'fecha_subida_soporte' => 'datetime',
        'monto_pagado' => 'decimal:2'
    ];

    // Relaciones
    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'id_cuenta');
    }

    public function arrendatario()
    {
        return $this->belongsTo(Usuario::class, 'id_arrendatario');
    }

    public function verificador()
    {
        return $this->belongsTo(Usuario::class, 'verificado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado_verificacion', 'Pendiente');
    }

    public function scopeVerificados($query)
    {
        return $query->where('estado_verificacion', 'Verificado');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado_verificacion', 'Rechazado');
    }

    // Mutadores
    public function setMontoPagadoAttribute($value)
    {
        $this->attributes['monto_pagado'] = round($value, 2);
    }

    // Atributos calculados
    public function getDiasDesdeUploadAttribute()
    {
        return $this->fecha_subida_soporte->diffInDays(now());
    }
}