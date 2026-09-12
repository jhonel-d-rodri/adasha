<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
{
    $materials = Material::all();

    return view('materials.index', compact('materials'));
}

    public function store(Request $request)
    {
        $request->validate([

            'nombre' => 'required',

            'unidad' => 'required',

            'stock' => 'required|numeric',

            'descripcion' => 'nullable'

        ]);

        Material::create($request->all());

        return redirect()
            ->route('materials.index')
            ->with('success','Material registrado correctamente.');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success','Material eliminado correctamente.');
    }
}