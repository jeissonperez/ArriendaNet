<?php

namespace App\Http\Controllers;

use App\Models\SolicitudMantenimiento;
use App\Models\Inmueble;
use Illuminate\Http\Request;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = SolicitudMantenimiento::with(['inmueble', 'usuario'])->get();
        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inmuebles = Inmueble::where('estado', 'Arrendado')->get();
        return view('solicitudes.create', compact('inmuebles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id_inmueble',
            'usuario_id' => 'required|exists:usuarios,id_usuario',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|max:500',
            'nivel_urgencia' => 'required|in:Baja,Media,Alta,Crítica',
            'estado' => 'required|in:Pendiente,En Proceso,Completada,Cancelada',
            'fecha_solicitud' => 'required|date',
            'fecha_inicio' => 'nullable|date|after_or_equal:fecha_solicitud',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $solicitud = SolicitudMantenimiento::create($validatedData);

        return redirect()->route('solicitudes.show', $solicitud->id_solicitud)
            ->with('success', 'Solicitud de mantenimiento creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SolicitudMantenimiento $solicitude)
    {
        return view('solicitudes.show', ['solicitud' => $solicitude]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SolicitudMantenimiento $solicitude)
    {
        $inmuebles = Inmueble::where('estado', 'Arrendado')
            ->orWhere('id_inmueble', $solicitude->inmueble_id)
            ->get();
        return view('solicitudes.edit', [
            'solicitud' => $solicitude,
            'inmuebles' => $inmuebles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SolicitudMantenimiento $solicitude)
    {
        $validatedData = $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id_inmueble',
            'usuario_id' => 'required|exists:usuarios,id_usuario',
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|max:500',
            'nivel_urgencia' => 'required|in:Baja,Media,Alta,Crítica',
            'estado' => 'required|in:Pendiente,En Proceso,Completada,Cancelada',
            'fecha_solicitud' => 'required|date',
            'fecha_inicio' => 'nullable|date|after_or_equal:fecha_solicitud',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $solicitude->update($validatedData);

        return redirect()->route('solicitudes.show', $solicitude->id_solicitud)
            ->with('success', 'Solicitud de mantenimiento actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SolicitudMantenimiento $solicitude)
    {
        $solicitude->delete();
        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud de mantenimiento eliminada exitosamente.');
    }
}