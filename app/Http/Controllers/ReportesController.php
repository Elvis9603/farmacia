<?php

namespace App\Http\Controllers;

use App\Models\VentaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;



class ReportesController extends Controller
{
    public function ventas(Request $request)
    {
        $query = VentaModel::with(['cliente', 'usuario', 'detalleVentas.producto']);
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }
        if ($request->has('usuario_id') && !empty($request->usuario_id)) {
            $usuarioId = $request->input('usuario_id');
            $query->where('id_usuario', $usuarioId);
        }
        if (empty($request->usuario_id)) {
            $query->orWhereNull('id_usuario');
        }
        $ventas = $query->get();
        $usuarios = \App\Models\UsuarioModel::all();
        return view('reportes.ventas', compact('ventas', 'usuarios'));
    }

    public function exportPDF(Request $request)
    {
        $query = ventaModel::with(['cliente', 'usuario', 'detalleVentas.producto']); // Cargar relaciones
        if ($request->has('semana')) {
            $semana = $request->input('semana');
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeeks($semana - 1);
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            $query->whereBetween('fecha_venta', [$startOfWeek, $endOfWeek]);
        }
        if ($request->has('usuario_id')) {
            $usuarioId = $request->input('usuario_id');
            $query->where('id_usuario', $usuarioId);
        }
        $ventas = $query->get();
        $usuarios = \App\Models\UsuarioModel::all();
        $html = view('reportes.pdfVentas', compact('ventas', 'usuarios'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream('ventas.pdf');
    }

    public function inventario(Request $request)
    {
        $query = \App\Models\ProductoModel::with('proveedor');

        if ($request->has('proveedor_id') && !empty($request->proveedor_id)) {
            $query->where('id_proveedor', $request->proveedor_id);
        }

        $productos = $query->get();
        $proveedores = \App\Models\ProveedorModel::all();

        return view('reportes.inventario', compact('productos', 'proveedores'));
    }
    public function exportInventarioPDF(Request $request)
    {
        $query = \App\Models\ProductoModel::with('proveedor');
        if ($request->has('proveedor_id') && !empty($request->proveedor_id)) {
            $query->where('id_proveedor', $request->proveedor_id);
        }
        $productos = $query->get();
        $proveedores = \App\Models\ProveedorModel::all();
        $html = view('reportes.pdfInventario', compact('productos', 'proveedores'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream('inventario.pdf');
    }
    public function exportInventario2PDF(Request $request)
    {
        $query = \App\Models\ProductoModel::with('proveedor');

        // Filtrar solo productos controlados
        $query->where('controlado', 'si');

        $productos = $query->get();
        $proveedores = \App\Models\ProveedorModel::all();
        
        // Verificar si hay productos controlados
        if ($productos->isEmpty()) {
            return response()->json(['message' => 'No hay productos controlados.'], 404);
        }

        $html = view('reportes.pdfInventario', compact('productos', 'proveedores'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream('inventario_controlados.pdf');
    }
}
