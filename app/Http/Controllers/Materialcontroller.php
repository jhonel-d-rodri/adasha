<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MovimientoMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
{
    $materials = Material::with(['movimientos' => fn ($query) => $query->latest()->limit(5)])->orderBy('nombre')->get();

    return view('materials.index', compact('materials'));
}

    public function store(Request $request)
    {
        $request->validate([

            'nombre' => 'required',

            'unidad' => 'required',

            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
            'precio_unitario' => 'nullable|numeric|min:0',

            'descripcion' => 'nullable'

        ]);

        Material::create($request->only(['nombre', 'unidad', 'stock', 'stock_minimo', 'precio_unitario', 'descripcion']));

        return redirect()
            ->route('materials.index')
            ->with('success','Material registrado correctamente.');
    }

    public function movimiento(Request $request, Material $material)
    {
        $datos = $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|numeric|gt:0',
            'motivo' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($datos, $material) {
            $material = Material::lockForUpdate()->findOrFail($material->id);
            $nuevoStock = $datos['tipo'] === 'entrada'
                ? $material->stock + $datos['cantidad']
                : $material->stock - $datos['cantidad'];

            if ($nuevoStock < 0) {
                abort(422, 'La salida no puede ser mayor al stock disponible.');
            }

            $material->update(['stock' => $nuevoStock]);
            MovimientoMaterial::create($datos + [
                'material_id' => $material->id,
                'user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Movimiento de inventario registrado correctamente.');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success','Material eliminado correctamente.');
    }
}
