<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductoModel;
use App\Models\NotificacionModel;
use App\Models\UsuarioModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class GenerarAlertas extends Command
{
    protected $signature = 'alertas:generar';
    protected $description = 'Genera todas las alertas del sistema (vencimientos, informes, facturación)';

    public function handle()
    {
        $this->generarAlertasVencimiento();
        $this->generarAlertasInformes();
        $this->generarAlertasFacturacion();
        
        $this->info('Todas las alertas han sido generadas correctamente.');
        return 0;
    }

    private function generarAlertasVencimiento()
    {
        // Alertas según días configurados por proveedor
        $productos = ProductoModel::proximosAVencer();
        
        if ($productos->count() > 0) {
            // Agrupar productos por días de proveedor
            $productosPorDias = $productos->groupBy(function($producto) {
                if ($producto->proveedor && $producto->proveedor->diasCambioAntesVencimiento) {
                    return $producto->proveedor->diasCambioAntesVencimiento;
                }
                return 30; // Valor por defecto
            });
            
            // Generar alertas para cada grupo
            foreach ($productosPorDias as $dias => $productosGrupo) {
                $notificacion = NotificacionModel::crearNotificacionVencimiento($dias, $productosGrupo);
                $this->info("Alerta de vencimiento a {$dias} días creada: {$productosGrupo->count()} productos");
                $this->enviarEmailAlerta($notificacion);
            }
        }
    }

    private function generarAlertasInformes()
    {
        $hoy = Carbon::now();
        
        // Solo generar alertas del 1 al 9 de cada mes
        if ($hoy->day >= 1 && $hoy->day <= 9) {
            $notificacionFELCC = NotificacionModel::crearNotificacionInforme('felcc');
            $notificacionSEDES = NotificacionModel::crearNotificacionInforme('sedes');
            
            $this->info("Alertas de informes FELCC y SEDES creadas");
            
            $this->enviarEmailAlerta($notificacionFELCC);
            $this->enviarEmailAlerta($notificacionSEDES);
        }
    }

    private function generarAlertasFacturacion()
    {
        $hoy = Carbon::now();
        
        // Solo generar alertas del 1 al 13 de cada mes
        if ($hoy->day >= 1 && $hoy->day <= 13) {
            $usuarios = UsuarioModel::where('estado', 'activo')->get();
            
            foreach ($usuarios as $usuario) {
                $notificacion = NotificacionModel::crearNotificacionFacturacion($usuario);
                $this->info("Alerta de facturación creada para: {$usuario->nombre}");
                $this->enviarEmailAlerta($notificacion);
            }
        }
    }

    private function enviarEmailAlerta($notificacion)
    {
        // Aquí implementarías el envío de email
        // Por ejemplo usando Laravel Mail
        /*
        Mail::to($email)->send(new AlertaNotificacion($notificacion));
        $notificacion->marcarEnviadaEmail();
        */
        
        // Por ahora solo marcamos como enviada
        $notificacion->marcarEnviadaEmail();
    }
}