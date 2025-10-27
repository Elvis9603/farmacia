<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductoModel extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    protected $fillable = [
        'nombre', 'foto', 'id_tipo', 'precioCompra', 'precioVenta',
        'descuento', 'stock', 'stockMinimo', 'estado', 'fechaVencimiento',
        'controlado', 'id_proveedor', 'id_presentacion'
    ];

    protected $dates = ['fechaVencimiento'];

    public function tipo()
    {
        return $this->belongsTo(TipoModel::class, 'id_tipo');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorModel::class, 'id_proveedor');
    }

    public function presentacion()
    {
        return $this->belongsTo(PresentacionModel::class, 'id_presentacion');
    }

    /**
     * Verifica si el producto está próximo a vencer
     * 
     * @return array
     */
    public function getEstadoVencimientoAttribute()
    {
        if (!$this->fechaVencimiento) {
            return [
                'estado' => 'sin_vencimiento',
                'dias_restantes' => null,
                'alerta' => false
            ];
        }

        $hoy = Carbon::today();
        $diasRestantes = $hoy->diffInDays($this->fechaVencimiento, false);
        
        // Obtener los días configurados para el proveedor
        $diasProveedor = 30; // Valor por defecto
        if ($this->proveedor && $this->proveedor->diasCambioAntesVencimiento) {
            $diasProveedor = $this->proveedor->diasCambioAntesVencimiento;
        }

        if ($diasRestantes < 0) {
            return [
                'estado' => 'vencido',
                'dias_restantes' => abs($diasRestantes),
                'alerta' => true
            ];
        }

        if ($diasRestantes <= $diasProveedor) {
            return [
                'estado' => 'por_vencer_proveedor',
                'dias_restantes' => $diasRestantes,
                'alerta' => true,
                'dias_proveedor' => $diasProveedor
            ];
        }

        return [
            'estado' => 'vigente',
            'dias_restantes' => $diasRestantes,
            'alerta' => false
        ];
    }

    /**
     * Verifica si el producto debe mostrarse como próximo a vencer
     * 
     * @return bool
     */
    public function getProximoVencerAttribute()
    {
        $estado = $this->getEstadoVencimientoAttribute();
        return in_array($estado['estado'], ['por_vencer_30', 'por_vencer_90']);
    }

    /**
     * Verifica si el producto está vencido
     * 
     * @return bool
     */
    public function getEstaVencidoAttribute()
    {
        $estado = $this->getEstadoVencimientoAttribute();
        return $estado['estado'] === 'vencido';
    }

    /**
     * Obtiene los productos próximos a vencer según los días configurados por proveedor
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function proximosAVencer()
    {
        $hoy = Carbon::today();
        $productos = self::with('proveedor')
            ->whereNotNull('fechaVencimiento')
            ->where('fechaVencimiento', '>=', $hoy)
            ->where('stock', '>', 0)
            ->get();
            
        return $productos->filter(function($producto) use ($hoy) {
            // Obtener los días configurados para el proveedor
            $diasProveedor = 30; // Valor por defecto
            if ($producto->proveedor && $producto->proveedor->diasCambioAntesVencimiento) {
                $diasProveedor = $producto->proveedor->diasCambioAntesVencimiento;
            }
            
            $fechaLimite = $hoy->copy()->addDays($diasProveedor);
            return $producto->fechaVencimiento <= $fechaLimite;
        })->sortBy('fechaVencimiento');
    }

    /**
     * Obtiene los productos vencidos
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function vencidos()
    {
        return self::whereNotNull('fechaVencimiento')
            ->where('fechaVencimiento', '<', now())
            ->where('stock', '>', 0)
            ->orderBy('fechaVencimiento')
            ->get();
    }

    /**
     * Aplica el método FIFO para la salida de productos
     * 
     * @param int $cantidad Cantidad a descontar
     * @return bool True si se pudo realizar la operación
     */
    public function aplicarFIFO($cantidad)
    {
        if ($this->stock < $cantidad) {
            return false;
        }

        // Si el producto no tiene vencimiento, solo actualizamos el stock
        if (!$this->fechaVencimiento) {
            $this->decrement('stock', $cantidad);
            return true;
        }

        // Para productos con vencimiento, implementar lógica FIFO
        // Esto requeriría una tabla de lotes (que deberías crear)
        // Por ahora, solo actualizamos el stock
        $this->decrement('stock', $cantidad);
        return true;
    }
}
