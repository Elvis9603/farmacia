<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoModel;

class VencimientoController extends Controller
{
    public function index()
    {
        return view('vencimientos');
    }

    public function vencidos()
    {
        $productos = ProductoModel::vencidos();
        return response()->json($productos);
    }

    public function proximosAVencer($dias = 30)
    {
        $productos = ProductoModel::proximosAVencer($dias);
        return response()->json($productos);
    }
}
