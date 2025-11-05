<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Contrato;
use App\Models\CuentaCobro;
use App\Models\SolicitudMantenimiento;

class NotificacionService
{
    public function crearNotificacion($usuario, $tipo, $titulo, $mensaje, $url = null)
    {
        return Notificacion::create([
            'id_usuario' => $usuario->id_usuario,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'url' => $url
        ]);
    }

    public function notificarVencimientoContrato(Contrato $contrato)
    {
        // Notificar al arrendador
        $this->crearNotificacion(
            $contrato->arrendador,
            'contrato',
            'Contrato próximo a vencer',
            "El contrato del inmueble {$contrato->inmueble->direccion} vencerá el {$contrato->fecha_fin->format('d/m/Y')}",
            route('contratos.show', $contrato)
        );

        // Notificar al arrendatario
        $this->crearNotificacion(
            $contrato->arrendatario,
            'contrato',
            'Contrato próximo a vencer',
            "Tu contrato del inmueble {$contrato->inmueble->direccion} vencerá el {$contrato->fecha_fin->format('d/m/Y')}",
            route('contratos.show', $contrato)
        );
    }

    public function notificarPagoVencido(CuentaCobro $cuentaCobro)
    {
        // Notificar al arrendador
        $this->crearNotificacion(
            $cuentaCobro->contrato->arrendador,
            'pago',
            'Pago vencido',
            "El pago del inmueble {$cuentaCobro->contrato->inmueble->direccion} está vencido",
            route('cuentas-cobro.show', $cuentaCobro)
        );

        // Notificar al arrendatario
        $this->crearNotificacion(
            $cuentaCobro->contrato->arrendatario,
            'pago',
            'Pago vencido',
            "El pago del arriendo está vencido. Por favor, realiza el pago lo antes posible",
            route('pagos.create')
        );
    }

    public function notificarNuevaSolicitudMantenimiento(SolicitudMantenimiento $solicitud)
    {
        // Notificar al arrendador
        $this->crearNotificacion(
            $solicitud->inmueble->propietario,
            'mantenimiento',
            'Nueva solicitud de mantenimiento',
            "Se ha registrado una nueva solicitud de mantenimiento para el inmueble {$solicitud->inmueble->direccion}",
            route('solicitudes.show', $solicitud)
        );
    }

    public function notificarActualizacionSolicitudMantenimiento(SolicitudMantenimiento $solicitud)
    {
        // Notificar al arrendatario
        $this->crearNotificacion(
            $solicitud->solicitante,
            'mantenimiento',
            'Actualización de solicitud de mantenimiento',
            "El estado de tu solicitud de mantenimiento ha sido actualizado a: {$solicitud->estado_solicitud}",
            route('solicitudes.show', $solicitud)
        );
    }

    public function notificarPagoRegistrado(CuentaCobro $cuentaCobro)
    {
        // Notificar al arrendador
        $this->crearNotificacion(
            $cuentaCobro->contrato->arrendador,
            'pago',
            'Nuevo pago registrado',
            "Se ha registrado un nuevo pago para el inmueble {$cuentaCobro->contrato->inmueble->direccion}",
            route('cuentas-cobro.show', $cuentaCobro)
        );
    }

    public function marcarComoLeida(Notificacion $notificacion)
    {
        $notificacion->update(['leida' => true]);
    }

    public function marcarTodasComoLeidas(Usuario $usuario)
    {
        Notificacion::where('id_usuario', $usuario->id_usuario)
                    ->where('leida', false)
                    ->update(['leida' => true]);
    }
}