<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ProveedorModel;
use App\Models\ProductoModel;
use Illuminate\Support\Facades\DB;

class Pedidos extends Component
{
    // Modales
    public $showModal = false;
    public $showModalProveedor = false;

    // Pedido
    public $fecha;
    public $proveedor = '';
    public $items = [];
    public $total = 0;

    // Proveedor
    public $nombreProveedor;
    public $telefonoProveedor;
    public $tipoProveedor = 'distribuidora';
    public $diasCambio = 0;

    // Listas
    public $proveedores = [];
    public $productos = [];

    public function mount()
    {
        $this->fecha = now()->toDateString();
        $this->proveedores = ProveedorModel::orderBy('nombre')->get();
        $this->productos = collect();
    }

    public function render()
    {
        $pedidos = PedidoModel::with('proveedor')->orderBy('id_pedido', 'desc')->get();
        return view('livewire.pedidos', compact('pedidos'));
    }

    // === Modal Pedido ===
    public function openModal()
    {
        $this->resetPedido();
        $this->showModal = true;
    }

    public function resetPedido()
    {
        $this->fecha = now()->toDateString();
        $this->proveedor = '';
        $this->items = [];
        $this->total = 0;
        $this->productos = collect();
    }

    // === Cuando cambia el proveedor ===
    public function updatedProveedor($value)
    {
        $this->items = [];
        $this->total = 0;

        if (!empty($value)) {
            $this->productos = ProductoModel::where('id_proveedor', $value)
                ->where('estado', 'activo')
                ->get();
        } else {
            $this->productos = collect();
        }
    }

    // === Agregar producto al pedido ===
    public function agregarProducto($id_producto)
    {
        $producto = ProductoModel::find($id_producto);
        if (!$producto) return;

        foreach ($this->items as &$it) {
            if ($it['id_producto'] == $producto->id_producto) {
                $it['cantidad']++;
                $it['subtotal'] = $it['cantidad'] * $it['precio_unitario'];
                $this->recalcularTotal();
                return;
            }
        }

        $this->items[] = [
            'id_producto' => $producto->id_producto,
            'nombre' => $producto->nombre,
            'precio_unitario' => floatval($producto->precioCompra),
            'cantidad' => 1,
            'subtotal' => floatval($producto->precioCompra)
        ];

        $this->recalcularTotal();
    }

    public function actualizarCantidad($index, $valor)
    {
        if (!isset($this->items[$index])) return;
        $cantidad = max(1, intval($valor));
        $this->items[$index]['cantidad'] = $cantidad;
        $this->items[$index]['subtotal'] = $cantidad * $this->items[$index]['precio_unitario'];
        $this->recalcularTotal();
    }

    public function eliminarItem($index)
    {
        array_splice($this->items, $index, 1);
        $this->recalcularTotal();
    }

    private function recalcularTotal()
    {
        $this->total = collect($this->items)->sum('subtotal');
    }

    // === Guardar Pedido ===
    public function guardarPedido()
    {
        $this->validate([
            'proveedor' => 'required',
            'items' => 'required|array|min:1',
        ], [
            'proveedor.required' => 'Seleccione un proveedor.',
            'items.required' => 'Agregue al menos un producto al pedido.'
        ]);

        DB::transaction(function () {
            $pedido = PedidoModel::create([
                'fecha' => $this->fecha,
                'total' => $this->total,
                'id_proveedor' => intval($this->proveedor),
                'estado' => 'pendiente'
            ]);

            foreach ($this->items as $it) {
                DetallePedidoModel::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $it['id_producto'],
                    'precio_unitario' => $it['precio_unitario'],
                    'cantidad' => $it['cantidad'],
                    'subtotal' => $it['subtotal']
                ]);
            }
        });

        session()->flash('message', 'Pedido creado correctamente.');
        $this->resetPedido();
        $this->showModal = false;
    }

    // === Marcar pedido como recibido ===
    public function marcarRecibido($id_pedido)
    {
        $pedido = PedidoModel::with('detalles.producto')->find($id_pedido);
        if (!$pedido) return;

        $pedido->estado = 'recibido';
        $pedido->save();

        foreach ($pedido->detalles as $det) {
            if ($det->producto) {
                $det->producto->increment('stock', $det->cantidad);
            }
        }

        session()->flash('message', 'Pedido marcado como recibido.');
    }

    // === Modal Proveedor ===
    public function openModalProveedor()
    {
        $this->resetProveedor();
        $this->showModalProveedor = true;
    }

    public function resetProveedor()
    {
        $this->nombreProveedor = '';
        $this->telefonoProveedor = '';
        $this->tipoProveedor = 'distribuidora';
        $this->diasCambio = 0;
    }

    public function guardarProveedor()
    {
        $this->validate([
            'nombreProveedor' => 'required',
            'telefonoProveedor' => 'required',
        ], [
            'nombreProveedor.required' => 'El nombre es obligatorio.',
            'telefonoProveedor.required' => 'El teléfono es obligatorio.'
        ]);

        ProveedorModel::create([
            'nombre' => $this->nombreProveedor,
            'telefono' => $this->telefonoProveedor,
            'tipo' => $this->tipoProveedor,
            'diasCambioAntesVencimiento' => $this->diasCambio
        ]);

        $this->proveedores = ProveedorModel::orderBy('nombre')->get();

        session()->flash('message', 'Proveedor agregado correctamente.');
        $this->showModalProveedor = false;
    }
}
