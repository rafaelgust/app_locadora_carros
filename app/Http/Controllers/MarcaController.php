<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Marca::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                //'imagem' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $exists = Marca::where('nome', $request->nome)->exists();

        if ($exists) {
            return response()->json(['error' => 'Marca já existe.'], 409);
        }

        $marca = new Marca();
        $marca->nome = $request->nome;
        $marca->imagem = $request->imagem;
        $marca->save();

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                //'imagem' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $marca->nome = $request->nome;
        $marca->imagem = $request->imagem;
        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        $marca->delete();

        return response()->json(['message' => 'Marca deletada com sucesso.'], 200);
    }
}
