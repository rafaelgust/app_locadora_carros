<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->marca->all();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $request->validate($this->marca->rules(), $this->marca->feedback());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $exists = $this->marca->where('nome', $request->nome)->exists();

        if ($exists) {
            return response()->json(['error' => 'Marca já existe.'], 409);
        }

        $marca = $this->marca;
        $marca->nome = $request->nome;
        $marca->imagem = $request->imagem;
        $marca->save();

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $marca = $this->marca->find($id);

        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {  
        try {
            $request->validate($this->marca->rules(), $this->marca->feedback());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }

        $marca = $this->marca->find($id);

        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        $marca->nome = $request->nome;
        $marca->imagem = $request->imagem;
        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $marca = $this->marca->find($id);

        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        $marca->delete();

        return response()->json(['message' => 'Marca deletada com sucesso.'], 200);
    }
}
