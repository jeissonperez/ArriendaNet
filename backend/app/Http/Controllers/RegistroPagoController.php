<?php

namespace App\Http\Controllers;

use App\Models\RegistroPago;
use App\Models\CuentaCobro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistroPagoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        
        if ($usuario->rol_usuario === 'Arrendador') {
            $pagos = RegistroPago::whereHas('cuentaCobro.contrato', function($q) {
                $q->where('id_arrendador', auth()->id());
            })->paginate(10);
        } else {
            $pagos = RegistroPago::where('id_arrendatario', auth()->id())
                                ->paginate(10);
        }

        return view('pagos.index', compact('pagos'));
    }

    public function create()
    {
        $cuentasCobro = CuentaCobro::whereHas('contrato', function($q) {
            $q->where('id_arrendatario', auth()->id())
              ->where('estado_contrato', 'Activo');
        })->where('estado_pago', 'Pendiente')
          ->get();

        return view('pagos.create', compact('cuentasCobro'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_cuenta' => 'required|exists:cuentas_cobro,id_cuenta',
            'monto_pagado' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Consignación,Otro',
            'referencia_pago' => 'nullable|string|max:100',
            'descripcion_soporte' => 'required|string',
            'archivo_soporte' => 'required|file|max:2048' // Máximo 2MB
        ], [
            'id_cuenta.required' => 'Debe seleccionar una cuenta de cobro',
            'id_cuenta.exists' => 'La cuenta de cobro seleccionada no existe',
            'monto_pagado.required' => 'El monto pagado es obligatorio',
            'monto_pagado.numeric' => 'El monto pagado debe ser numérico',
            'monto_pagado.min' => 'El monto pagado no puede ser negativo',
            'fecha_pago.required' => 'La fecha de pago es obligatoria',
            'fecha_pago.date' => 'La fecha de pago debe ser válida',
            'metodo_pago.required' => 'El método de pago es obligatorio',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido',
            'descripcion_soporte.required' => 'La descripción del soporte es obligatoria',
            'archivo_soporte.required' => 'El archivo de soporte es obligatorio',
            'archivo_soporte.file' => 'El soporte debe ser un archivo',
            'archivo_soporte.max' => 'El archivo no debe pesar más de 2MB'
        ]);

        $cuentaCobro = CuentaCobro::findOrFail($request->id_cuenta);
        
        // Verificar que el usuario sea el arrendatario del contrato
        if ($cuentaCobro->contrato->id_arrendatario !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para registrar pagos para esta cuenta de cobro');
        }

        if ($request->hasFile('archivo_soporte')) {
            $path = $request->file('archivo_soporte')->store('soportes_pago', 'public');
            $validatedData['archivo_soporte'] = $path;
        }

        $validatedData['id_arrendatario'] = auth()->id();
        $validatedData['estado_verificacion'] = 'Pendiente';

        $pago = RegistroPago::create($validatedData);

        return redirect()->route('pagos.show', $pago)
            ->with('success', 'Pago registrado exitosamente y pendiente de verificación');
    }

    public function show(RegistroPago $pago)
    {
        $this->authorize('view', $pago);
        return view('pagos.show', compact('pago'));
    }

    public function destroy(RegistroPago $pago)
    {
        $this->authorize('delete', $pago);

        if ($pago->estado_verificacion !== 'Pendiente') {
            return back()->with('error', 'No se puede eliminar un pago que ya ha sido verificado');
        }

        if ($pago->archivo_soporte) {
            Storage::disk('public')->delete($pago->archivo_soporte);
        }

        $pago->delete();

        return redirect()->route('pagos.index')
            ->with('success', 'Registro de pago eliminado exitosamente');
    }

    // Métodos adicionales
    public function verificar(Request $request, RegistroPago $pago)
    {
        $this->authorize('verify', $pago);

        $request->validate([
            'estado_verificacion' => 'required|in:Verificado,Rechazado'
        ]);

        $pago->update([
            'estado_verificacion' => $request->estado_verificacion,
            'verificado_por' => auth()->id()
        ]);

        // Si el pago es verificado, actualizar el estado de la cuenta de cobro
        if ($request->estado_verificacion === 'Verificado') {
            $cuentaCobro = $pago->cuentaCobro;
            $totalPagado = $cuentaCobro->registrosPago()
                                      ->where('estado_verificacion', 'Verificado')
                                      ->sum('monto_pagado');

            if ($totalPagado >= $cuentaCobro->total_pagar) {
                $cuentaCobro->update([
                    'estado_pago' => 'Pagado',
                    'fecha_pago' => now()
                ]);
            }
        }

        return back()->with('success', 'Estado de verificación actualizado exitosamente');
    }

    public function pagosPendientes()
    {
        $pagos = RegistroPago::whereHas('cuentaCobro.contrato', function($q) {
            $q->where('id_arrendador', auth()->id());
        })->where('estado_verificacion', 'Pendiente')
          ->paginate(10);

        return view('pagos.pendientes', compact('pagos'));
    }
}