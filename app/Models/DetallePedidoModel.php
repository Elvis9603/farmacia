<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedidoModel extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id_detalle';
    public $timestamps = true;
    protected $fillable = ['id_pedido', 'id_producto', 'precio_unitario', 'cantidad', 'subtotal'];

    public function producto()
    {
        return $this->belongsTo(ProductoModel::class, 'id_producto', 'id_producto');
    }

    public function pedido()
    {
        return $this->belongsTo(PedidoModel::class, 'id_pedido', 'id_pedido');
    }
}
