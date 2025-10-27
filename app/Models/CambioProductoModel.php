<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CambioProductoModel extends Model
{
    protected $table = 'cambio_producto';
    protected $primaryKey = 'id_cambio';
    protected $fillable = [
        'fecha_solicitud', 'fecha_cambio', 'motivo', 'estado',
        'id_producto', 'cantidad', 'id_proveedor', 'id_usuario'
    ];

    protected $dates = ['fecha_solicitud', 'fecha_cambio'];

    // Estados posibles
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_RECHAZADO = 'rechazado';
    const ESTADO_COMPLETADO = 'completado';

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(ProductoModel::class, 'id_producto');
    }

    /**
     * Relación con el proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(ProveedorModel::class, 'id_proveedor');
    }

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'id_usuario');
    }

    /**
     * Solicitar cambio de producto por vencimiento
     */
    public static function solicitarCambio($idProducto, $cantidad, $idProveedor, $idUsuario)
    {
        return self::create([
            'fecha_solicitud' => Carbon::now(),
            'motivo' => 'Producto próximo a vencer',
            'estado' => self::ESTADO_PENDIENTE,
            'id_producto' => $idProducto,
            'cantidad' => $cantidad,
            'id_proveedor' => $idProveedor,
            'id_usuario' => $idUsuario
        ]);
    }

    /**
     * Aprobar solicitud de cambio
     */
    public function aprobar($idUsuario)
    {
        $this->estado = self::ESTADO_APROBADO;
        $this->save();
        
        // Crear notificación para el usuario que solicitó el cambio
        NotificacionModel::create([
            'titulo' => 'Solicitud de cambio aprobada',
            'mensaje' => "La solicitud de cambio para el producto {$this->producto->nombre} ha sido aprobada.",
            'tipo' => 'cambio_producto',
            'fecha_creacion' => Carbon::now(),
            'fecha_limite' => Carbon::now()->addDays(7),
            'leida' => false,
            'id_usuario' => $this->id_usuario,
            'enlace' => '/productos/cambios',
            'enviada_email' => false
        ]);
    }

    /**
     * Completar cambio de producto
     */
    public function completar()
    {
        $this->estado = self::ESTADO_COMPLETADO;
        $this->fecha_cambio = Carbon::now();
        $this->save();
        
        // Actualizar inventario
        $producto = $this->producto;
        
        // Registrar la salida del producto vencido
        // Aquí podrías implementar un registro de movimientos de inventario
        
        // Notificar al usuario
        NotificacionModel::create([
            'titulo' => 'Cambio de producto completado',
            'mensaje' => "El cambio del producto {$producto->nombre} ha sido completado exitosamente.",
            'tipo' => 'cambio_producto',
            'fecha_creacion' => Carbon::now(),
            'fecha_limite' => Carbon::now()->addDays(3),
            'leida' => false,
            'id_usuario' => $this->id_usuario,
            'enlace' => '/productos/cambios',
            'enviada_email' => false
        ]);
    }
}