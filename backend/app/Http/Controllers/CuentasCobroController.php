<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\Contrato;
use Illuminate\Http\Request;

class CuentasCobroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cuentasCobro = CuentaCobro::with(['contrato.inmueble', 'contrato.arrendatario'])->get();
        return view('cuentas-cobro.index', compact('cuentasCobro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contratos = Contrato::where('estado', 'Activo')->get();
        return view('cuentas-cobro.create', compact('contratos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'contrato_id' => 'required|exists:contratos,id_contrato',
            'periodo_mes' => 'required|integer|between:1,12',
            'periodo_año' => 'required|integer|min:2023',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_emision',
            'valor_total' => 'required|numeric|min:0',
            'estado' => 'required|in:Pendiente,Pagada,Vencida',
            'conceptos' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $cuentaCobro = CuentaCobro::create($validatedData);

        return redirect()->route('cuentas-cobro.show', $cuentaCobro->id_cuenta_cobro)
            ->with('success', 'Cuenta de cobro creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CuentaCobro $cuentasCobro)
    {
        return view('cuentas-cobro.show', ['cuentaCobro' => $cuentasCobro]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuentaCobro $cuentasCobro)
    {
        $contratos = Contrato::where('estado', 'Activo')
            ->orWhere('id_contrato', $cuentasCobro->contrato_id)
            ->get();
        return view('cuentas-cobro.edit', [
            'cuentaCobro' => $cuentasCobro,
            'contratos' => $contratos
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuentaCobro $cuentasCobro)
    {
        $validatedData = $request->validate([
            'contrato_id' => 'required|exists:contratos,id_contrato',
            'periodo_mes' => 'required|integer|between:1,12',
            'periodo_año' => 'required|integer|min:2023',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_emision',
            'valor_total' => 'required|numeric|min:0',
            'estado' => 'required|in:Pendiente,Pagada,Vencida',
            'conceptos' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $cuentasCobro->update($validatedData);

        return redirect()->route('cuentas-cobro.show', $cuentasCobro->id_cuenta_cobro)
            ->with('success', 'Cuenta de cobro actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuentaCobro $cuentasCobro)
    {
        if ($cuentasCobro->estado === 'Pagada') {
            return redirect()->route('cuentas-cobro.index')
                ->with('error', 'No se puede eliminar una cuenta de cobro que ya ha sido pagada.');
        }

        $cuentasCobro->delete();
        return redirect()->route('cuentas-cobro.index')
            ->with('success', 'Cuenta de cobro eliminada exitosamente.');
    }
}