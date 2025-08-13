<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    protected $locacao;

    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if($request->filled('atributo_cliente')) {
            $locacaoRepository->selectAtributosRegistrosRelacionados('cliente', 'id', $request->atributo_cliente);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('cliente', 'id');
        }

        if($request->filled('atributo_carro')) {
            $locacaoRepository->selectAtributosRegistrosRelacionados('carro', 'id', $request->atributo_carro);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('carro', 'id');
        }

        if($request->filled('atributos')) {
            $locacaoRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $locacaoRepository->filtrarRegistros($request->filtro);
        }

        return response()->json($locacaoRepository->getResult());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules(), $this->locacao->feedback());

        $locacao = Locacao::create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);

        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if($request->filled('atributos_cliente')) {
            $locacaoRepository->selectAtributosRegistrosRelacionados('cliente', 'id', $request->atributos_cliente);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('cliente', 'id');
        }

        if($request->filled('atributos_carro')) {
            $locacaoRepository->selectAtributosRegistrosRelacionados('carro', 'id', $request->atributos_carro);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('carro', 'id');
        }

        if($request->filled('atributos')) {
            $locacaoRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $locacaoRepository->filtrarRegistros($request->filtro);
        }

        $locacao = $locacaoRepository->findById($id);

        if(!$locacao) {
            return response()->json(['error' => 'Locação não encontrada.'], 404);
        }

        return response()->json($locacao);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $locacao = Locacao::find($id);
        if (!$locacao) {
            return response()->json(['error' => 'Locação não encontrada.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para PATCH
            $regrasDinamicas = [];
            foreach ($this->locacao->rules($locacao->id) as $field => $rule) {
                if ($request->has($field)) {
                    $regrasDinamicas[$field] = $rule;
                }
            }

            if (empty($regrasDinamicas)) {
                return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

            $request->validate($regrasDinamicas, $this->locacao->feedback());

            // Atualiza apenas os campos enviados na requisição
            $dadosAtualizados = $request->only(array_keys($regrasDinamicas));

            $locacao->fill($dadosAtualizados);
            $locacao->save();

            return response()->json($locacao, 200);

        } elseif ($request->isMethod('put')) {
            $request->validate($this->locacao->rules($locacao->id), $this->locacao->feedback());

            $locacao->cliente_id = $request->cliente_id;
            $locacao->carro_id = $request->carro_id;
            $locacao->data_inicio_periodo = $request->data_inicio_periodo;
            $locacao->data_final_previsto_periodo = $request->data_final_previsto_periodo;
            $locacao->data_final_realizado_periodo = $request->data_final_realizado_periodo;
            $locacao->valor_diaria = $request->valor_diaria;
            $locacao->km_inicial = $request->km_inicial;
            $locacao->km_final = $request->km_final;

            $locacao->save();

            return response()->json($locacao, 200);

        } else {
            return response()->json(['error' => 'Método não permitido.'], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $locacao = Locacao::find($id);
        if (!$locacao) {
            return response()->json(['error' => 'Locação não encontrada.'], 404);
        }

        $nome = $locacao->nome;

        $locacao->delete();

        return response()->json(['message' => 'Locação ' . $nome . ' foi removida com sucesso.'], 200);
    }
}
