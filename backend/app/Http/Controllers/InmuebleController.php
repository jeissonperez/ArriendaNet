<?php

namespace App\Http\Controllers;

use App\Models\Inmueble;
use Illuminate\Http\Request;

class InmuebleController extends Controller
{
    public function index(Request $request)
    {
        $query = Inmueble::query();

        // Filtros
        if ($request->has('tipo')) {
            $query->where('tipo_inmueble', $request->tipo);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('precio_min')) {
            $query->where('valor_arriendo', '>=', $request->precio_min);
        }

        if ($request->has('precio_max')) {
            $query->where('valor_arriendo', '<=', $request->precio_max);
        }

        $inmuebles = $query->paginate(10);
        return view('inmuebles.index', compact('inmuebles'));
    }

    public function create()
    {
        return view('inmuebles.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'direccion' => 'required|string|max:100',
            'tipo_inmueble' => 'required|in:Terreno,Casa,Apartamento,Local,Lote,Otro',
            'valor_arriendo' => 'required|numeric|min:0',
            'estado' => 'required|in:Disponible,Arrendado,Mantenimiento',
        ], [
            'direccion.required' => 'La dirección es obligatoria',
            'tipo_inmueble.required' => 'El tipo de inmueble es obligatorio',
            'tipo_inmueble.in' => 'El tipo de inmueble seleccionado no es válido',
            'valor_arriendo.required' => 'El valor del arriendo es obligatorio',
            'valor_arriendo.numeric' => 'El valor del arriendo debe ser numérico',
            'valor_arriendo.min' => 'El valor del arriendo no puede ser negativo',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',
        ]);

        $validatedData['id_usuario'] = auth()->id();

        Inmueble::create($validatedData);

        return redirect()->route('inmuebles.index')
            ->with('success', 'Inmueble registrado exitosamente');
    }

    public function show(Inmueble $inmueble)
    {
        return view('inmuebles.show', compact('inmueble'));
    }

    public function edit(Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);
        return view('inmuebles.edit', compact('inmueble'));
    }

    public function update(Request $request, Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        $validatedData = $request->validate([
            'direccion' => 'required|string|max:100',
            'tipo_inmueble' => 'required|in:Terreno,Casa,Apartamento,Local,Lote,Otro',
            'valor_arriendo' => 'required|numeric|min:0',
            'estado' => 'required|in:Disponible,Arrendado,Mantenimiento',
        ]);

        $inmueble->update($validatedData);

        return redirect()->route('inmuebles.index')
            ->with('success', 'Inmueble actualizado exitosamente');
    }

    public function destroy(Inmueble $inmueble)
    {
        $this->authorize('delete', $inmueble);
        
        $inmueble->delete();

        return redirect()->route('inmuebles.index')
            ->with('success', 'Inmueble eliminado exitosamente');
    }

    // Métodos adicionales para funcionalidades específicas
    public function misInmuebles()
    {
        $inmuebles = Inmueble::where('id_usuario', auth()->id())->paginate(10);
        return view('inmuebles.mis-inmuebles', compact('inmuebles'));
    }

    public function cambiarEstado(Request $request, Inmueble $inmueble)
    {
        $this->authorize('update', $inmueble);

        $request->validate([
            'estado' => 'required|in:Disponible,Arrendado,Mantenimiento'
        ]);

        $inmueble->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado del inmueble actualizado exitosamente');
    }
}