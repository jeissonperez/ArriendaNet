<?php

namespace App\Http\Controllers;

use App\Models\CuentaCobro;
use App\Models\Contrato;
use Illuminate\Http\Request;

class CuentaCobroController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        
        if ($usuario->rol_usuario === 'Arrendador') {
            $cuentasCobro = CuentaCobro::whereHas('contrato', function($q) {
                $q->where('id_arrendador', auth()->id());
            })->paginate(10);
        } else {
            $cuentasCobro = CuentaCobro::whereHas('contrato', function($q) {
                $q->where('id_arrendatario', auth()->id());
            })->paginate(10);
        }

        return view('cuentas-cobro.index', compact('cuentasCobro'));
    }

    public function create()
    {
        $contratos = Contrato::where('id_arrendador', auth()->id())
                            ->where('estado_contrato', 'Activo')
                            ->get();
        return view('cuentas-cobro.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_contrato' => 'required|exists:contratos,id_contrato',
            'fecha_emision' => 'required|date',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after:periodo_inicio',
            'valor_arriendo' => 'required|numeric|min:0',
            'otros_cargos' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $contrato = Contrato::findOrFail($request->id_contrato);
        
        // Verificar que el usuario sea el arrendador
        if ($contrato->id_arrendador !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para crear cuentas de cobro para este contrato');
        }

        // Calcular el total a pagar
        $validatedData['total_pagar'] = $validatedData['valor_arriendo'] + ($validatedData['otros_cargos'] ?? 0);
        $validatedData['estado_pago'] = 'Pendiente';

        $cuentaCobro = CuentaCobro::create($validatedData);

        // Aquí podrías generar el PDF y guardarlo
        // $cuentaCobro->documento_pdf = $rutaPDF;
        // $cuentaCobro->save();

        return redirect()->route('cuentas-cobro.show', $cuentaCobro)
            ->with('success', 'Cuenta de cobro creada exitosamente');
    }

    public function show(CuentaCobro $cuentaCobro)
    {
        $this->authorize('view', $cuentaCobro);
        return view('cuentas-cobro.show', compact('cuentaCobro'));
    }

    public function edit(CuentaCobro $cuentaCobro)
    {
        $this->authorize('update', $cuentaCobro);
        
        if ($cuentaCobro->estado_pago !== 'Pendiente') {
            return back()->with('error', 'No se puede editar una cuenta de cobro que ya ha sido pagada o está vencida');
        }

        $contratos = Contrato::where('id_arrendador', auth()->id())
                            ->where('estado_contrato', 'Activo')
                            ->get();
        return view('cuentas-cobro.edit', compact('cuentaCobro', 'contratos'));
    }

    public function update(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorize('update', $cuentaCobro);

        if ($cuentaCobro->estado_pago !== 'Pendiente') {
            return back()->with('error', 'No se puede editar una cuenta de cobro que ya ha sido pagada o está vencida');
        }

        $validatedData = $request->validate([
            'valor_arriendo' => 'required|numeric|min:0',
            'otros_cargos' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $validatedData['total_pagar'] = $validatedData['valor_arriendo'] + ($validatedData['otros_cargos'] ?? 0);

        $cuentaCobro->update($validatedData);

        return redirect()->route('cuentas-cobro.show', $cuentaCobro)
            ->with('success', 'Cuenta de cobro actualizada exitosamente');
    }

    public function destroy(CuentaCobro $cuentaCobro)
    {
        $this->authorize('delete', $cuentaCobro);

        if ($cuentaCobro->estado_pago !== 'Pendiente') {
            return back()->with('error', 'No se puede eliminar una cuenta de cobro que ya ha sido pagada o está vencida');
        }

        $cuentaCobro->delete();

        return redirect()->route('cuentas-cobro.index')
            ->with('success', 'Cuenta de cobro eliminada exitosamente');
    }

    // Métodos adicionales
    public function generarAutomaticas()
    {
        $contratosActivos = Contrato::where('id_arrendador', auth()->id())
                                   ->where('estado_contrato', 'Activo')
                                   ->get();

        $fechaActual = now();
        $cuentasGeneradas = 0;

        foreach ($contratosActivos as $contrato) {
            // Verificar si ya existe una cuenta de cobro para el mes actual
            $existeCuenta = CuentaCobro::where('id_contrato', $contrato->id_contrato)
                                      ->whereYear('periodo_inicio', $fechaActual->year)
                                      ->whereMonth('periodo_inicio', $fechaActual->month)
                                      ->exists();

            if (!$existeCuenta) {
                CuentaCobro::create([
                    'id_contrato' => $contrato->id_contrato,
                    'fecha_emision' => $fechaActual,
                    'periodo_inicio' => $fechaActual->startOfMonth(),
                    'periodo_fin' => $fechaActual->endOfMonth(),
                    'valor_arriendo' => $contrato->valor_mensual,
                    'otros_cargos' => 0,
                    'total_pagar' => $contrato->valor_mensual,
                    'estado_pago' => 'Pendiente'
                ]);

                $cuentasGeneradas++;
            }
        }

        return back()->with('success', "Se generaron $cuentasGeneradas cuentas de cobro automáticamente");
    }

    public function marcarPagada(Request $request, CuentaCobro $cuentaCobro)
    {
        $this->authorize('update', $cuentaCobro);

        $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,Consignacion,Otro'
        ]);

        $cuentaCobro->update([
            'estado_pago' => 'Pagado',
            'fecha_pago' => $request->fecha_pago,
            'metodo_pago' => $request->metodo_pago
        ]);

        return back()->with('success', 'Cuenta de cobro marcada como pagada');
    }

    public function marcarVencida(CuentaCobro $cuentaCobro)
    {
        $this->authorize('update', $cuentaCobro);

        if ($cuentaCobro->estado_pago === 'Pendiente' && $cuentaCobro->periodo_fin < now()) {
            $cuentaCobro->update(['estado_pago' => 'Vencido']);
            return back()->with('success', 'Cuenta de cobro marcada como vencida');
        }

        return back()->with('error', 'No se puede marcar como vencida esta cuenta de cobro');
    }
}