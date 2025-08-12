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

        $request->validate( $this->marca->rules(), $this->marca->feedback());

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
        $marca = $this->marca->find($id);

        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para o método PATCH
            $regrasDinamicas = array();

            foreach ($this->marca->rules() as $field => $rule) {
                if ($request->has($field)) {
                    $regrasDinamicas[$field] = $rule;
                }
            }
            // Validação das regras dinâmicas, apenas os parâmetros que tem no request
            if (!empty($regrasDinamicas)) {
                $request->validate($regrasDinamicas, $this->marca->feedback());

                // Preenche os atributos do modelo $marca apenas com os dados do request que correspondem às chaves definidas em $regrasDinamicas
                $marca->fill($request->only(array_keys($regrasDinamicas)));
            } else {
                return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

        } else {
            $request->validate($this->marca->rules(), $this->marca->feedback());
        }

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
