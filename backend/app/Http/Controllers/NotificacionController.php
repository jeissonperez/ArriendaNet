<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    protected $notificacionService;

    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    public function index()
    {
        $notificaciones = Notificacion::where('id_usuario', auth()->id())
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarComoLeida(Notificacion $notificacion)
    {
        $this->authorize('update', $notificacion);
        
        $this->notificacionService->marcarComoLeida($notificacion);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Notificación marcada como leída']);
        }

        return back()->with('success', 'Notificación marcada como leída');
    }

    public function marcarTodasComoLeidas()
    {
        $this->notificacionService->marcarTodasComoLeidas(auth()->user());

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
        }

        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    public function obtenerNoLeidas()
    {
        $notificaciones = Notificacion::where('id_usuario', auth()->id())
                                     ->where('leida', false)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return response()->json($notificaciones);
    }
}