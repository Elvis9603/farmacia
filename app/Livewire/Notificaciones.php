<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NotificacionModel;
use Illuminate\Support\Facades\Auth;

class Notificaciones extends Component
{
    public $notificaciones = [];
    public $mostrarTodas = false;
    
    protected $listeners = ['actualizarNotificaciones' => 'cargarNotificaciones'];
    
    public function mount()
    {
        $this->cargarNotificaciones();
    }
    
    public function cargarNotificaciones()
    {
        $query = NotificacionModel::orderBy('fecha_creacion', 'desc');
        
        if (!$this->mostrarTodas) {
            $query->where('leida', false);
        }
        
        // Si es para un usuario específico o para todos
        if (Auth::check()) {
            $query->where(function($q) {
                $q->whereNull('id_usuario')
                  ->orWhere('id_usuario', Auth::user()->id_usuario);
            });
        }
        
        $this->notificaciones = $query->limit(10)->get();
    }
    
    public function marcarLeida($id)
    {
        $notificacion = NotificacionModel::find($id);
        if ($notificacion) {
            $notificacion->marcarLeida();
            $this->cargarNotificaciones();
        }
    }
    
    public function marcarTodasLeidas()
    {
        $query = NotificacionModel::where('leida', false);
        
        if (Auth::check()) {
            $query->where(function($q) {
                $q->whereNull('id_usuario')
                  ->orWhere('id_usuario', Auth::user()->id_usuario);
            });
        }
        
        $query->update(['leida' => true]);
        $this->cargarNotificaciones();
    }
    
    public function toggleMostrarTodas()
    {
        $this->mostrarTodas = !$this->mostrarTodas;
        $this->cargarNotificaciones();
    }
    
    public function render()
    {
        return view('livewire.notificaciones');
    }
}