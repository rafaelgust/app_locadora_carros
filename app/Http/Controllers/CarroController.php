<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;

use Illuminate\Http\Request;


class CarroController extends Controller
{
    protected $carro;

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if($request->filled('atributo_modelo')) {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo', 'id', $request->atributo_modelo);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo', 'id');
        }

        if($request->filled('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $carroRepository->filtrarRegistros($request->filtro);
        }

        return response()->json($carroRepository->getResult());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = Carro::create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $carroRepository = new CarroRepository($this->carro);

        if($request->filled('atributo_modelo')) {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo', 'id', $request->atributo_modelo);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo', 'id');
        }

        if($request->filled('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $carroRepository->filtrarRegistros($request->filtro);
        }

        $carro = $carroRepository->findById($id);

        if(!$carro) {
            return response()->json(['error' => 'Carro não encontrado.'], 404);
        }

        return response()->json($carro);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $carro = Carro::find($id);
        if (!$carro) {
            return response()->json(['error' => 'Carro não encontrado.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para PATCH
            $regrasDinamicas = [];
            foreach ($this->carro->rules($carro->id) as $field => $rule) {
            if ($request->has($field)) {
                $regrasDinamicas[$field] = $rule;
            }
            }

            if (empty($regrasDinamicas)) {
            return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

            $request->validate($regrasDinamicas, $this->carro->feedback());

            // Atualiza apenas os campos enviados na requisição
            $dadosAtualizados = $request->only(array_keys($regrasDinamicas));

            // Atualiza os campos no modelo
            $carro->fill($dadosAtualizados);
            $carro->save();

            return response()->json($carro, 200);

        } elseif ($request->isMethod('put')) {
            $request->validate($this->carro->rules($carro->id), $this->carro->feedback());

            $carro->modelo_id = $request->modelo_id;
            $carro->placa = $request->placa;
            $carro->disponivel = $request->disponivel;
            $carro->km = $request->km;

            $carro->save();

            return response()->json($carro, 200);

        } else {
            return response()->json(['error' => 'Método não permitido.'], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $carro = Carro::find($id);
        if (!$carro) {
            return response()->json(['error' => 'Carro não encontrado.'], 404);
        }

        $placa = $carro->placa;

        $carro->delete();

        return response()->json(['message' => 'Carro ' . $placa . ' removido com sucesso.'], 200);
    }
}
