<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductoModel;

class AlertasVencimiento extends Component
{
    public $mostrarVencidos = false;
    public $mostrarProximos = false;
    
    public function render()
    {
        $vencidos = [];
        $proximosVencer = [];
        $productosAgrupados = [];

        if ($this->mostrarVencidos || $this->mostrarProximos) {
            if ($this->mostrarVencidos) {
                $vencidos = ProductoModel::vencidos();
            }
            if ($this->mostrarProximos) {
                $proximosVencer = ProductoModel::proximosAVencer();
            }
        } else {
            // Por defecto, mostrar todos los vencidos y próximos a vencer
            $vencidos = ProductoModel::vencidos();
            $proximosVencer = ProductoModel::proximosAVencer();
        }
        
        // Agrupar productos por días de proveedor
        if (is_object($proximosVencer) && method_exists($proximosVencer, 'count')) {
            if ($proximosVencer->count() > 0) {
                $productosAgrupados = $proximosVencer->groupBy(function($producto) {
                    if ($producto->proveedor && $producto->proveedor->diasCambioAntesVencimiento) {
                        return $producto->proveedor->diasCambioAntesVencimiento;
                    }
                    return 30; // Valor por defecto
                });
            }
        } else if (is_array($proximosVencer) && count($proximosVencer) > 0) {
            // Convertir array a colección para usar groupBy
            $proximosVencerCollection = collect($proximosVencer);
            $productosAgrupados = $proximosVencerCollection->groupBy(function($producto) {
                if ($producto->proveedor && $producto->proveedor->diasCambioAntesVencimiento) {
                    return $producto->proveedor->diasCambioAntesVencimiento;
                }
                return 30; // Valor por defecto
            });
        }

        return view('livewire.alertas-vencimiento', [
            'vencidos' => $vencidos,
            'proximosVencer' => $proximosVencer,
            'productosAgrupados' => $productosAgrupados,
        ]);
    }

    public function toggleVencidos()
    {
        $this->mostrarVencidos = !$this->mostrarVencidos;
    }

    public function toggleProximos()
    {
        $this->mostrarProximos = !$this->mostrarProximos;
    }
}