<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotificacionModel extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    protected $fillable = [
        'titulo', 'mensaje', 'tipo', 'fecha_creacion', 'fecha_limite', 
        'leida', 'id_usuario', 'enlace', 'enviada_email'
    ];

    protected $dates = ['fecha_creacion', 'fecha_limite'];

    // Tipos de notificación
    const TIPO_VENCIMIENTO_90 = 'vencimiento_90';
    const TIPO_VENCIMIENTO_30 = 'vencimiento_30';
    const TIPO_INFORME_FELCC = 'informe_felcc';
    const TIPO_INFORME_SEDES = 'informe_sedes';
    const TIPO_FACTURACION = 'facturacion';

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'id_usuario');
    }

    /**
     * Crear notificación de vencimiento de productos
     */
    public static function crearNotificacionVencimiento($dias, $productos)
    {
        $tipo = $dias == 30 ? self::TIPO_VENCIMIENTO_30 : self::TIPO_VENCIMIENTO_90;
        $titulo = "Productos por vencer en $dias días";
        
        $mensaje = "Hay " . count($productos) . " productos que vencerán en $dias días. ";
        if (count($productos) > 0) {
            $mensaje .= "Algunos productos: " . implode(", ", $productos->take(3)->pluck('nombre')->toArray());
        }
        
        return self::create([
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fecha_creacion' => Carbon::now(),
            'fecha_limite' => Carbon::now()->addDays(7),
            'leida' => false,
            'enlace' => '/vencimientos',
            'enviada_email' => false
        ]);
    }

    /**
     * Crear notificación para informes FELCC y SEDES
     */
    public static function crearNotificacionInforme($tipo)
    {
        $tipoInforme = $tipo === 'felcc' ? self::TIPO_INFORME_FELCC : self::TIPO_INFORME_SEDES;
        $entidad = $tipo === 'felcc' ? 'FELCC' : 'SEDES';
        
        $titulo = "Informe mensual para $entidad";
        $mensaje = "Debe realizar el informe mensual de ventas controladas para $entidad antes del 10 del mes actual.";
        
        return self::create([
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipoInforme,
            'fecha_creacion' => Carbon::now(),
            'fecha_limite' => Carbon::now()->endOfMonth()->setDay(10),
            'leida' => false,
            'enlace' => '/ventas/informes',
            'enviada_email' => false
        ]);
    }

    /**
     * Crear notificación para facturación de empleados
     */
    public static function crearNotificacionFacturacion($usuario)
    {
        $titulo = "Preparación de facturación mensual";
        $mensaje = "Debe preparar su facturación hasta el 14 del mes actual.";
        
        return self::create([
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => self::TIPO_FACTURACION,
            'fecha_creacion' => Carbon::now(),
            'fecha_limite' => Carbon::now()->endOfMonth()->setDay(14),
            'leida' => false,
            'id_usuario' => $usuario->id_usuario,
            'enlace' => '/facturacion',
            'enviada_email' => false
        ]);
    }

    /**
     * Marcar como leída
     */
    public function marcarLeida()
    {
        $this->leida = true;
        $this->save();
    }

    /**
     * Marcar como enviada por email
     */
    public function marcarEnviadaEmail()
    {
        $this->enviada_email = true;
        $this->save();
    }
}