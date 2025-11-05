<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';
    protected $primaryKey = 'id_contrato';

    protected $fillable = [
        'id_inmueble',
        'id_arrendador',
        'id_arrendatario',
        'fecha_inicio',
        'fecha_fin',
        'valor_mensual',
        'deposito_garantia',
        'clausulas',
        'observaciones',
        'estado_contrato'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_mensual' => 'decimal:2',
        'deposito_garantia' => 'decimal:2'
    ];

    // Relaciones
    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class, 'id_inmueble');
    }

    public function arrendador()
    {
        return $this->belongsTo(Usuario::class, 'id_arrendador');
    }

    public function arrendatario()
    {
        return $this->belongsTo(Usuario::class, 'id_arrendatario');
    }

    public function cuentasCobro()
    {
        return $this->hasMany(CuentaCobro::class, 'id_contrato');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado_contrato', 'Activo');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado_contrato', 'Finalizado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado_contrato', 'Cancelado');
    }

    // Atributos calculados
    public function getProximoVencimientoAttribute()
    {
        return $this->cuentasCobro()
                    ->where('estado_pago', 'Pendiente')
                    ->orderBy('periodo_fin')
                    ->first();
    }

    public function getDuracionContratoDiasAttribute()
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }
}