<?php

namespace App\Http\Controllers;

use App\Models\SolicitudMantenimiento;
use App\Models\Inmueble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudMantenimientoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        
        if ($usuario->rol_usuario === 'Arrendador') {
            $solicitudes = SolicitudMantenimiento::whereHas('inmueble', function($q) {
                $q->where('id_usuario', auth()->id());
            })->paginate(10);
        } else {
            $solicitudes = SolicitudMantenimiento::where('id_usuario', auth()->id())
                                               ->paginate(10);
        }

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $usuario = auth()->user();
        
        if ($usuario->rol_usuario === 'Arrendatario') {
            $inmuebles = Inmueble::whereHas('contratos', function($q) {
                $q->where('id_arrendatario', auth()->id())
                  ->where('estado_contrato', 'Activo');
            })->get();
        } else {
            $inmuebles = Inmueble::where('id_usuario', auth()->id())->get();
        }

        return view('solicitudes.create', compact('inmuebles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_inmueble' => 'required|exists:inmuebles,id_inmueble',
            'tipo_mantenimiento' => 'required|in:Eléctrico,Plomería,Estructural,Electrodomésticos,Pintura,Otro',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
            'evidencia_foto' => 'nullable|image|max:2048' // Máximo 2MB
        ], [
            'id_inmueble.required' => 'Debe seleccionar un inmueble',
            'id_inmueble.exists' => 'El inmueble seleccionado no existe',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'tipo_mantenimiento.in' => 'El tipo de mantenimiento seleccionado no es válido',
            'descripcion.required' => 'La descripción es obligatoria',
            'prioridad.required' => 'La prioridad es obligatoria',
            'prioridad.in' => 'La prioridad seleccionada no es válida',
            'evidencia_foto.image' => 'El archivo debe ser una imagen',
            'evidencia_foto.max' => 'La imagen no debe pesar más de 2MB'
        ]);

        $validatedData['id_usuario'] = auth()->id();
        
        if ($request->hasFile('evidencia_foto')) {
            $path = $request->file('evidencia_foto')->store('evidencias', 'public');
            $validatedData['evidencia_foto'] = $path;
        }

        $solicitud = SolicitudMantenimiento::create($validatedData);

        return redirect()->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud de mantenimiento creada exitosamente');
    }

    public function show(SolicitudMantenimiento $solicitud)
    {
        $this->authorize('view', $solicitud);
        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(SolicitudMantenimiento $solicitud)
    {
        $this->authorize('update', $solicitud);

        if (in_array($solicitud->estado_solicitud, ['Finalizado', 'Rechazado'])) {
            return back()->with('error', 'No se puede editar una solicitud finalizada o rechazada');
        }

        $inmuebles = Inmueble::where('id_usuario', auth()->id())->get();
        return view('solicitudes.edit', compact('solicitud', 'inmuebles'));
    }

    public function update(Request $request, SolicitudMantenimiento $solicitud)
    {
        $this->authorize('update', $solicitud);

        if (in_array($solicitud->estado_solicitud, ['Finalizado', 'Rechazado'])) {
            return back()->with('error', 'No se puede editar una solicitud finalizada o rechazada');
        }

        $validatedData = $request->validate([
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:Baja,Media,Alta,Urgente',
            'evidencia_foto' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('evidencia_foto')) {
            // Eliminar la imagen anterior si existe
            if ($solicitud->evidencia_foto) {
                Storage::disk('public')->delete($solicitud->evidencia_foto);
            }
            
            $path = $request->file('evidencia_foto')->store('evidencias', 'public');
            $validatedData['evidencia_foto'] = $path;
        }

        $solicitud->update($validatedData);

        return redirect()->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud de mantenimiento actualizada exitosamente');
    }

    public function destroy(SolicitudMantenimiento $solicitud)
    {
        $this->authorize('delete', $solicitud);

        if ($solicitud->evidencia_foto) {
            Storage::disk('public')->delete($solicitud->evidencia_foto);
        }

        $solicitud->delete();

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud de mantenimiento eliminada exitosamente');
    }

    // Métodos adicionales
    public function cambiarEstado(Request $request, SolicitudMantenimiento $solicitud)
    {
        $this->authorize('update', $solicitud);

        $request->validate([
            'estado_solicitud' => 'required|in:Pendiente,En proceso,Finalizado,Rechazado',
            'observaciones_arrendador' => 'required|string'
        ]);

        $solicitud->update([
            'estado_solicitud' => $request->estado_solicitud,
            'observaciones_arrendador' => $request->observaciones_arrendador,
            'fecha_atencion' => in_array($request->estado_solicitud, ['Finalizado', 'Rechazado']) ? now() : null
        ]);

        return back()->with('success', 'Estado de la solicitud actualizado exitosamente');
    }

    public function solicitudesUrgentes()
    {
        $solicitudes = SolicitudMantenimiento::whereHas('inmueble', function($q) {
            $q->where('id_usuario', auth()->id());
        })->where('prioridad', 'Urgente')
          ->where('estado_solicitud', '!=', 'Finalizado')
          ->paginate(10);

        return view('solicitudes.urgentes', compact('solicitudes'));
    }
}