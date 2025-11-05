<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;

class ContratosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contratos = Contrato::with(['inmueble', 'arrendatario'])->get();
        return view('contratos.index', compact('contratos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contratos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id_inmueble',
            'arrendatario_id' => 'required|exists:usuarios,id_usuario',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'valor_mensual' => 'required|numeric|min:0',
            'estado' => 'required|in:Activo,Inactivo,Finalizado',
        ]);

        $contrato = Contrato::create($validatedData);
        return redirect()->route('contratos.show', $contrato->id_contrato)
            ->with('success', 'Contrato creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrato $contrato)
    {
        return view('contratos.show', compact('contrato'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contrato $contrato)
    {
        return view('contratos.edit', compact('contrato'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contrato $contrato)
    {
        $validatedData = $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id_inmueble',
            'arrendatario_id' => 'required|exists:usuarios,id_usuario',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'valor_mensual' => 'required|numeric|min:0',
            'estado' => 'required|in:Activo,Inactivo,Finalizado',
        ]);

        $contrato->update($validatedData);
        return redirect()->route('contratos.show', $contrato->id_contrato)
            ->with('success', 'Contrato actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')
            ->with('success', 'Contrato eliminado exitosamente.');
    }
}