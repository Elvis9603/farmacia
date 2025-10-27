<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProveedorModel;
use App\Models\ProductoModel;
use Illuminate\Support\Facades\DB;

class Cambios extends Component
{
    public $showModal = false;
    public $showModalRecibir = false;

    public $fecha;
    public $motivo = '';
    public $proveedor = '';
    public $items = [];

    public $proveedores = [];
    public $productos = [];

    // Nuevas propiedades para marcar recibido
    public $devolucionSeleccionada = null;
    public $detallesRecibir = [];

    public function mount()
    {
        $this->fecha = now()->toDateString();
        $this->proveedores = ProveedorModel::orderBy('nombre')->get();
        $this->productos = collect();
    }

    public function render()
    {
        $devoluciones = DB::table('devolucion')
            ->orderBy('id_devolucion', 'desc')
            ->get();

        return view('livewire.cambios', [
            'proveedores' => $this->proveedores,
            'productos' => $this->productos,
            'devoluciones' => $devoluciones
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->fecha = now()->toDateString();
        $this->motivo = '';
        $this->proveedor = '';
        $this->items = [];
        $this->productos = collect();
    }

    // === CUANDO CAMBIA EL PROVEEDOR ===
    public function updatedProveedor($value)
    {
        $this->items = [];

        if (!$value) {
            $this->productos = collect();
            return;
        }

        $prov = ProveedorModel::find($value);

        if (!$prov) {
            $this->productos = collect();
            return;
        }

        if (strtolower($prov->nombre) === 'sin proveedor') {
            $this->productos = collect();
            session()->flash('error', 'El proveedor "sin proveedor" no permite devoluciones.');
            return;
        }

        $this->productos = ProductoModel::where('id_proveedor', $value)
            ->where('estado', 'activo')
            ->get();
    }

    // === AGREGAR PRODUCTO ===
    public function agregarProducto($id_producto)
    {
        $prod = ProductoModel::find($id_producto);
        if (!$prod) return;

        foreach ($this->items as &$it) {
            if ($it['id_producto'] == $prod->id_producto) {
                $it['cantidad']++;
                $it['subtotal'] = $it['cantidad'] * $it['precio_unitario'];
                return;
            }
        }

        $this->items[] = [
            'id_producto' => $prod->id_producto,
            'nombre' => $prod->nombre,
            'precio_unitario' => floatval($prod->precioCompra),
            'cantidad' => 1,
            'subtotal' => floatval($prod->precioCompra)
        ];
    }

    // === GUARDAR CAMBIO ===
    public function guardarCambio()
    {
        $this->validate([
            'proveedor' => 'required',
            'motivo' => 'required|string|max:255',
            'items' => 'required|array|min:1'
        ]);

        $prov = ProveedorModel::find($this->proveedor);
        if (!$prov) {
            session()->flash('error', 'Proveedor no válido.');
            return;
        }

        if (strtolower($prov->nombre) === 'sin proveedor') {
            session()->flash('error', 'Los productos del proveedor "sin proveedor" no pueden generar devoluciones.');
            return;
        }

        DB::transaction(function () {
            $devolId = DB::table('devolucion')->insertGetId([
                'fecha' => $this->fecha,
                'motivo' => $this->motivo,
                'id_proveedor' => $this->proveedor,
                'estado' => 'pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_devolucion');

            foreach ($this->items as $it) {
                DB::table('detalle_devolucion')->insert([
                    'id_devolucion' => $devolId,
                    'id_producto' => $it['id_producto'],
                    'precio_unitario' => $it['precio_unitario'],
                    'cantidad' => $it['cantidad'],
                    'subtotal' => $it['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        session()->flash('message', 'Devolución registrada correctamente.');
        $this->resetForm();
        $this->showModal = false;
    }

    // === ABRIR MODAL DE MARCAR RECIBIDO ===
    public function abrirModalRecibir($id_devolucion)
    {
        $this->devolucionSeleccionada = $id_devolucion;
        $this->detallesRecibir = [];

        $detalles = DB::table('detalle_devolucion')
            ->join('producto', 'detalle_devolucion.id_producto', '=', 'producto.id_producto')
            ->select('detalle_devolucion.*', 'producto.nombre', 'producto.fechaVencimiento')
            ->where('id_devolucion', $id_devolucion)
            ->get();

        foreach ($detalles as $det) {
            $this->detallesRecibir[] = [
                'id_producto' => $det->id_producto,
                'nombre' => $det->nombre,
                'cantidad' => $det->cantidad,
                'fechaVencimiento' => $det->fechaVencimiento,
            ];
        }

        $this->showModalRecibir = true;
    }

    // === CONFIRMAR RECEPCIÓN Y ACTUALIZAR FECHAS ===
    public function marcarRecibido()
    {
        if (!$this->devolucionSeleccionada) return;

        DB::transaction(function () {
            DB::table('devolucion')
                ->where('id_devolucion', $this->devolucionSeleccionada)
                ->update(['estado' => 'recibido', 'updated_at' => now()]);

            foreach ($this->detallesRecibir as $det) {
                // Incrementar stock
                DB::table('producto')
                    ->where('id_producto', $det['id_producto'])
                    ->increment('stock', $det['cantidad']);

                // Actualizar fecha de vencimiento
                if (!empty($det['fechaVencimiento'])) {
                    DB::table('producto')
                        ->where('id_producto', $det['id_producto'])
                        ->update(['fechaVencimiento' => $det['fechaVencimiento']]);
                }
            }
        });

        session()->flash('message', 'Devolución marcada como recibida y fechas actualizadas.');
        $this->showModalRecibir = false;
        $this->devolucionSeleccionada = null;
        $this->detallesRecibir = [];
    }
}
