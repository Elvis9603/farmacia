<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RecetaModel;
use App\Models\ProductoModel;
use App\Models\PagoModel;
use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ClienteModel;
use App\Models\TipoModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Venta extends Component
{
    public $id_tipo=1;
    public $productos=[];
    public $searchProducto='';
    public $carrito= [];
    public $total=0;
    public $searchCliente='';
    public $clientes= [];

    public $ciCliente;
    public $clienteId;
    public $nombre;
    public $telefono;

    public $tipoPago = "Efectivo";
    public $id_pago;
    public $tiposPago;
    public $idVenta;

    public $showModal = false;
    public $showModalReceta = false;

    public $fechaEmision = '';
    public $nombreDoctor = '';
    public $diagnostico = '';
    public $idReceta = '';

    public function rulesReceta(){
        $rules = [
            'nombreDoctor' => 'required|string|max:255',
            'fechaEmision' => 'required|date',
            'diagnostico' => 'required|string|max:255',
        ];
        return $rules;
    }

    public function render()
    {
        $tipos = TipoModel::all();
        $this->tiposPago = PagoModel::all(); 
        $this->clientes = ClienteModel::select('id_cliente', 'nombre', 'telefono')->get();
        $this->id_pago = PagoModel::where('nombre', 'Efectivo')->value('id_pago');
        $this->productos = ProductoModel::where('id_tipo',$this->id_tipo)
        ->where('nombre','like','%'.$this->searchProducto.'%')
        ->get();
        return view('livewire.venta', compact('tipos'));
    }
    public function guardar(){
        $this->validate([
            'clienteId'=>'required',
            'carrito'=>'required',
            'id_pago'=>'required',
        ]);

        $venta = new VentaModel();
        $venta->fecha = now()->setTimezone('America/La_Paz');
        $venta->total = $this->total;
        $venta->id_receta = !empty($this->idReceta) ? $this->idReceta : null;
        $venta->tipoVEnta = $this->idReceta ? 'controlado' : 'libre';
        $venta->id_usuario = Auth::user()->id_usuario;
        $venta->id_cliente = $this->clienteId;
        $venta->id_pago = $this->id_pago;
        $venta->estado = 'pagado';
        $venta->save();

        $this->idVenta = $venta->id_venta;

        foreach ($this->carrito as $item) {
            $detalle = new DetalleVentaModel();
            $detalle->id_venta = $venta->id_venta;
            $detalle->id_producto = $item['id_producto'];
            $detalle->precio_unitario = $item['precioVenta'];
            $detalle->cantidad = $item['cantidad'];
            $detalle->subtotal = $item['precioVenta'] * $item['cantidad'];
            $detalle->save();
            $producto = ProductoModel::find($item['id_producto']);
            $producto->stock -= $item['cantidad'];
            $producto->save();
        }   
        $this->openModal();
    }
    public function clickBuscar(){

    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->limpiarDatosReceta();
        $this->reset(['carrito', 'total', 'id_pago', 'ciCliente', 'clienteId', 'nombre', 'telefono']);
    }

    public function buscarCliente()
    {
        $cliente = ClienteModel::where('ci', $this->ciCliente)->first();

        if ($cliente) {
            $this->clienteId = $cliente->id_cliente;
            $this->nombre = $cliente->nombre;
            $this->telefono = $cliente->telefono;
        } else {
            $this->reset(['clienteId', 'nombre', 'telefono']);
        }
    }
    public function updatedClienteId($value)
    {
        $cliente = ClienteModel::find($value);
        if ($cliente) {
            $this->nombre = $cliente->nombre;
            $this->telefono = $cliente->telefono;
        } else {
            $this->reset(['nombre', 'telefono']);
        }
    }



    public function openModalReceta()
    {
        $this->showModalReceta = true;
    }

    public function closeModalReceta()
    {
        $this->showModalReceta = false;
    }

    public function limpiarDatosReceta(){
        $this->fechaEmision = '';
        $this->nombreDoctor = '';
        $this->diagnostico = '';
        $this->idReceta = '';
    }

    public function enviarRecetaClick()
    {
        $this->validate($this->rulesReceta());

        $fechaEmision = Carbon::parse($this->fechaEmision)->setTimezone('America/La_Paz');
        $fechaActual = Carbon::now('America/La_Paz');

        // Validar que no sea futura
        if ($fechaEmision->gt($fechaActual)) {
            $this->addError('fechaEmision', 'La fecha de emisión no puede ser futura.');
            return;
        }

        // Validar que no tenga más de 2 días de antigüedad
        if ($fechaEmision->lt($fechaActual->copy()->subDays(2)->startOfDay())) {
            $this->addError('fechaEmision', 'La receta no puede tener más de 2 días de antigüedad.');
            return;
        }

        // Guardar receta si pasa validaciones
        $receta = RecetaModel::create([
            'fechaEmision' => $this->fechaEmision,
            'nombreDoctor' => $this->nombreDoctor,
            'diagnostico' => $this->diagnostico,
        ]);

        $this->idReceta = $receta->id_receta;

        $this->closeModalReceta();
    }



    public function productosTipo($idTipo){
        $this->id_tipo = $idTipo;
        $this->searchProducto = '';
    }

    function calcularTotal(){
        $total = 0;
        foreach ($this->carrito as $item) {
            $total += $item['precioVenta'] * $item['cantidad'];
        }
        return $total;
    }

    public function addProducto($idProducto)
    {
        $producto = ProductoModel::find($idProducto);

        if (!$producto) return;

        // 🔒 Verificamos si el producto es controlado y si no hay receta
        if ($producto->controlado === 'si' && empty($this->idReceta)) {
            // Lanzamos un evento JS para mostrar alerta
            $this->dispatch('alerta-controlado', message: '⚠️ Este producto requiere registrar una RECETA antes de agregarlo.');

            return;
        }

        // --- Lógica normal para agregar al carrito ---
        $exists = false;
        foreach ($this->carrito as &$item) {
            if ($item['id_producto'] == $idProducto) {
                if ($item['cantidad'] + 1 > $producto->stock) {
                    return;
                } 
                $item['cantidad'] += 1;
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $this->carrito[] = [
                'precioVenta' => $producto->precioVenta,
                'cantidad' => 1,
                'id_producto' => $producto->id_producto,
                'producto' => $producto->toArray()
            ];
        }

        $this->total = $this->calcularTotal();
    }


    public function removeProducto($idProducto)
    {
        foreach ($this->carrito as $key => &$item) {
            if ($item['id_producto'] == $idProducto) {
                if ($item['cantidad'] > 1) {
                    $item['cantidad'] -= 1;
                } else {
                    unset($this->carrito[$key]);
                }
                break;
            }
        }
        $this->total = $this->calcularTotal();
    }
}
