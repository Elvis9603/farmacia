<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromocionModel extends Model
{
    protected $table = 'promocion';
    protected $primaryKey = 'id_promocion';
    protected $fillable = ['id_producto', 'porcentaje_descuento', 'precio_original', 'fecha_inicio', 'fecha_fin'];

    public function producto()
    {
        return $this->belongsTo(ProductoModel::class, 'id_producto', 'id_producto');
    }
}
