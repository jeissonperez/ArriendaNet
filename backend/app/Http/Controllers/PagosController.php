<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\RegistroPago;
use Illuminate\Http\Request;

class PagosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pagos = RegistroPago::with(['cuentaCobro.contrato'])->get();
        return view('pagos.index', compact('pagos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cuentasCobro = CuentaCobro::where('estado', 'Pendiente')->get();
        return view('pagos.create', compact('cuentasCobro'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cuenta_cobro_id' => 'required|exists:cuentas_cobro,id_cuenta_cobro',
            'fecha_pago' => 'required|date',
            'valor_pagado' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'comprobante' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $pago = RegistroPago::create($validatedData);

        // Actualizar el estado de la cuenta de cobro si es necesario
        $cuentaCobro = CuentaCobro::find($request->cuenta_cobro_id);
        if ($cuentaCobro->valor_total <= $cuentaCobro->registrosPago->sum('valor_pagado')) {
            $cuentaCobro->estado = 'Pagada';
            $cuentaCobro->save();
        }

        return redirect()->route('pagos.show', $pago->id_registro_pago)
            ->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RegistroPago $pago)
    {
        return view('pagos.show', compact('pago'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistroPago $pago)
    {
        $cuentasCobro = CuentaCobro::where('estado', 'Pendiente')
            ->orWhere('id_cuenta_cobro', $pago->cuenta_cobro_id)
            ->get();
        return view('pagos.edit', compact('pago', 'cuentasCobro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistroPago $pago)
    {
        $validatedData = $request->validate([
            'cuenta_cobro_id' => 'required|exists:cuentas_cobro,id_cuenta_cobro',
            'fecha_pago' => 'required|date',
            'valor_pagado' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Tarjeta',
            'comprobante' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $pago->update($validatedData);

        // Actualizar estados de las cuentas de cobro
        $cuentaCobro = CuentaCobro::find($request->cuenta_cobro_id);
        if ($cuentaCobro->valor_total <= $cuentaCobro->registrosPago->sum('valor_pagado')) {
            $cuentaCobro->estado = 'Pagada';
            $cuentaCobro->save();
        }

        return redirect()->route('pagos.show', $pago->id_registro_pago)
            ->with('success', 'Pago actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistroPago $pago)
    {
        $pago->delete();
        return redirect()->route('pagos.index')
            ->with('success', 'Registro de pago eliminado exitosamente.');
    }
}