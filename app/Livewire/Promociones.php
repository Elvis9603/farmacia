<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PromocionModel;
use App\Models\ProductoModel;
use App\Models\ProveedorModel;
use Illuminate\Support\Facades\DB;

class Promociones extends Component
{
    public $showModal = false;
    public $producto = '';
    public $porcentaje_descuento = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    public function render()
    {
        // productos cuyo proveedor sea "sin proveedor"
        $proveedorSin = ProveedorModel::where('nombre', 'sin proveedor')->first();
        $productos = collect();
        if ($proveedorSin) {
            $productos = ProductoModel::where('id_proveedor', $proveedorSin->id_proveedor)
                        ->where('estado', 'activo')
                        ->get();
        }
        $promociones = PromocionModel::with('producto')->orderBy('id_promocion', 'desc')->get();

        return view('livewire.promociones', compact('productos', 'promociones'));
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->producto = '';
        $this->porcentaje_descuento = '';
        $this->fecha_inicio = now()->toDateString();
        $this->fecha_fin = now()->addDays(7)->toDateString();
    }

    public function guardar()
    {
        $this->validate([
            'producto' => 'required|exists:producto,id_producto',
            'porcentaje_descuento' => 'required|numeric|min:1|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        DB::transaction(function () {
            $producto = ProductoModel::find($this->producto);
            if (!$producto) {
                throw new \Exception('Producto no encontrado.');
            }

            // Solo permitir si proveedor es "sin proveedor"
            $proveedor = $producto->proveedor;
            if (!$proveedor || $proveedor->nombre !== 'sin proveedor') {
                throw new \Exception('Solo productos del proveedor "sin proveedor" pueden ir a promoción.');
            }

            $precioOriginal = $producto->precioVenta;
            $porc = floatval($this->porcentaje_descuento);
            $precioDescontado = round($precioOriginal * (1 - $porc / 100), 2);

            // Crear promocion (guarda precio original)
            $promo = PromocionModel::create([
                'id_producto' => $producto->id_producto,
                'porcentaje_descuento' => $porc,
                'precio_original' => $precioOriginal,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin
            ]);

            // Aplicar precio de venta descontado (modifica precioVenta actual)
            $producto->precioVenta = $precioDescontado;
            $producto->save();
        });

        session()->flash('message', 'Promoción creada y precio actualizado.');
        $this->resetForm();
        $this->showModal = false;
    }

    public function eliminarPromocion($id)
    {
        $promo = PromocionModel::find($id);
        if (!$promo) return;
        $producto = $promo->producto;
        // Restaurar precio original si existe
        if ($producto && $promo->precio_original) {
            $producto->precioVenta = $promo->precio_original;
            $producto->save();
        }
        $promo->delete();
        session()->flash('message', 'Promoción eliminada y precio restaurado.');
    }
}
