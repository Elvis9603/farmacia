<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoModel extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    protected $fillable = ['fecha', 'total', 'id_proveedor', 'estado'];

    public function proveedor()
    {
        return $this->belongsTo(ProveedorModel::class, 'id_proveedor', 'id_proveedor');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedidoModel::class, 'id_pedido', 'id_pedido');
    }
}
