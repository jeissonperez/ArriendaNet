<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Inmueble;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with(['inmueble', 'arrendador', 'arrendatario'])->paginate(10);
        return view('contratos.index', compact('contratos'));
    }

    public function create()
    {
        $inmuebles = Inmueble::where('estado', 'Disponible')->get();
        $arrendatarios = Usuario::where('rol_usuario', 'Arrendatario')->get();
        return view('contratos.create', compact('inmuebles', 'arrendatarios'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_inmueble' => 'required|exists:inmuebles,id_inmueble',
            'id_arrendatario' => 'required|exists:usuarios,id_usuario',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'valor_mensual' => 'required|numeric|min:0',
            'deposito_garantia' => 'nullable|numeric|min:0',
            'clausulas' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ], [
            'id_inmueble.required' => 'Debe seleccionar un inmueble',
            'id_inmueble.exists' => 'El inmueble seleccionado no existe',
            'id_arrendatario.required' => 'Debe seleccionar un arrendatario',
            'id_arrendatario.exists' => 'El arrendatario seleccionado no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser válida',
            'fecha_fin.date' => 'La fecha de fin debe ser válida',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'valor_mensual.required' => 'El valor mensual es obligatorio',
            'valor_mensual.numeric' => 'El valor mensual debe ser numérico',
            'valor_mensual.min' => 'El valor mensual no puede ser negativo',
            'deposito_garantia.numeric' => 'El depósito de garantía debe ser numérico',
            'deposito_garantia.min' => 'El depósito de garantía no puede ser negativo',
        ]);

        $inmueble = Inmueble::findOrFail($request->id_inmueble);
        
        // Verificar que el usuario autenticado sea el propietario del inmueble
        if ($inmueble->id_usuario !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para crear contratos para este inmueble');
        }

        $validatedData['id_arrendador'] = auth()->id();
        $validatedData['estado_contrato'] = 'Activo';

        $contrato = Contrato::create($validatedData);

        // Actualizar el estado del inmueble
        $inmueble->update(['estado' => 'Arrendado']);

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato creado exitosamente');
    }

    public function show(Contrato $contrato)
    {
        $this->authorize('view', $contrato);
        return view('contratos.show', compact('contrato'));
    }

    public function edit(Contrato $contrato)
    {
        $this->authorize('update', $contrato);
        $inmuebles = Inmueble::all();
        $arrendatarios = Usuario::where('rol_usuario', 'Arrendatario')->get();
        return view('contratos.edit', compact('contrato', 'inmuebles', 'arrendatarios'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $this->authorize('update', $contrato);

        $validatedData = $request->validate([
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'valor_mensual' => 'required|numeric|min:0',
            'deposito_garantia' => 'nullable|numeric|min:0',
            'clausulas' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado_contrato' => 'required|in:Activo,Finalizado,Cancelado'
        ]);

        $contrato->update($validatedData);

        // Si el contrato se finaliza o cancela, actualizar el estado del inmueble
        if (in_array($request->estado_contrato, ['Finalizado', 'Cancelado'])) {
            $contrato->inmueble->update(['estado' => 'Disponible']);
        }

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato actualizado exitosamente');
    }

    public function destroy(Contrato $contrato)
    {
        $this->authorize('delete', $contrato);
        
        // Liberar el inmueble si el contrato estaba activo
        if ($contrato->estado_contrato === 'Activo') {
            $contrato->inmueble->update(['estado' => 'Disponible']);
        }

        $contrato->delete();

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato eliminado exitosamente');
    }

    // Métodos adicionales
    public function misContratos()
    {
        $usuario = auth()->user();
        
        if ($usuario->rol_usuario === 'Arrendador') {
            $contratos = Contrato::where('id_arrendador', $usuario->id_usuario)->paginate(10);
        } else {
            $contratos = Contrato::where('id_arrendatario', $usuario->id_usuario)->paginate(10);
        }

        return view('contratos.mis-contratos', compact('contratos'));
    }

    public function cambiarEstado(Request $request, Contrato $contrato)
    {
        $this->authorize('update', $contrato);

        $request->validate([
            'estado_contrato' => 'required|in:Activo,Finalizado,Cancelado'
        ]);

        $contrato->update(['estado_contrato' => $request->estado_contrato]);

        if (in_array($request->estado_contrato, ['Finalizado', 'Cancelado'])) {
            $contrato->inmueble->update(['estado' => 'Disponible']);
        }

        return back()->with('success', 'Estado del contrato actualizado exitosamente');
    }
}