<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaModel extends Model
{
    protected $table = 'venta';
    protected $fillable = ['fecha','total','tipoVEnta','id_receta','id_usuario','id_cliente','id_pago','estado'];
    protected $primaryKey = 'id_venta';

    public function receta()
    {
        return $this->belongsTo(RecetaModel::class, 'id_receta', 'id_receta');
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'id_usuario', 'id_usuario');
    }
    public function cliente()
    {
        return $this->belongsTo(ClienteModel::class, 'id_cliente', 'id_cliente');
    }
    public function pago()
    {
        return $this->belongsTo(PagoModel::class, 'id_pago', 'id_pago');
    }
    public function detalleVentas()
    {
        return $this->hasMany(\App\Models\DetalleVentaModel::class, 'id_venta');
    }
}
