<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCobro extends Model
{
    protected $table = 'cuentas_cobro';
    protected $primaryKey = 'id_cuenta';

    protected $fillable = [
        'id_contrato',
        'fecha_emision',
        'periodo_inicio',
        'periodo_fin',
        'valor_arriendo',
        'otros_cargos',
        'total_pagar',
        'estado_pago',
        'metodo_pago',
        'fecha_pago',
        'numero_factura',
        'observaciones',
        'documento_pdf'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'fecha_pago' => 'date',
        'valor_arriendo' => 'decimal:2',
        'otros_cargos' => 'decimal:2',
        'total_pagar' => 'decimal:2'
    ];

    // Relaciones
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function registrosPago()
    {
        return $this->hasMany(RegistroPago::class, 'id_cuenta');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado_pago', 'Pendiente');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado_pago', 'Pagado');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado_pago', 'Vencido');
    }

    // Atributos calculados
    public function getTotalPagadoAttribute()
    {
        return $this->registrosPago()
                    ->where('estado_verificacion', 'Verificado')
                    ->sum('monto_pagado');
    }

    public function getSaldoPendienteAttribute()
    {
        return $this->total_pagar - $this->total_pagado;
    }
}