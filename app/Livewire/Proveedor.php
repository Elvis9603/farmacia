<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProveedorModel;

class Proveedor extends Component
{
    public $search = '';
    public $showModal = false;
    
    public $nombre = '';
    public $tipo = '';
    public $telefono = '';
    public $diasCambioAntesVencimiento = '';
    public $proveedor_id = '';

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:80',
            'tipo' => 'required|in:distribuidora,individual',
            'telefono' => 'required|string|max:30',
            'diasCambioAntesVencimiento' => 'required|integer|min:0',
        ];
    }

    public function render()
    {
        $proveedores = ProveedorModel::where('nombre', 'like', '%' . $this->search . '%')
            ->get();
        return view('livewire.proveedor', compact('proveedores'));
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->limpiarDatos();
    }

    public function limpiarDatos()
    {
        $this->nombre = '';
        $this->tipo = '';
        $this->telefono = '';
        $this->diasCambioAntesVencimiento = '';
        $this->proveedor_id = '';
    }

    public function enviarClick()
    {
        $this->validate();

        if ($this->proveedor_id) {
            $proveedor = ProveedorModel::find($this->proveedor_id);
            $proveedor->update([
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'telefono' => $this->telefono,
                'diasCambioAntesVencimiento' => $this->diasCambioAntesVencimiento,
            ]);
        } else {
            ProveedorModel::create([
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'telefono' => $this->telefono,
                'diasCambioAntesVencimiento' => $this->diasCambioAntesVencimiento,
            ]);
        }
        
        $this->limpiarDatos();
        $this->closeModal();
    }

    public function editar($id)
    {
        $proveedor = ProveedorModel::findOrFail($id);
        $this->nombre = $proveedor->nombre;
        $this->tipo = $proveedor->tipo;
        $this->telefono = $proveedor->telefono;
        $this->diasCambioAntesVencimiento = $proveedor->diasCambioAntesVencimiento;
        $this->proveedor_id = $id;
        $this->openModal();
    }
}
