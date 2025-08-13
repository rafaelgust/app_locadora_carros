<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClientRepository($this->cliente);

        if($request->filled('atributo_locacoes')) {
            $clienteRepository->selectAtributosRegistrosRelacionados('locacoes', 'id', $request->atributo_locacoes);
        } else {
            $clienteRepository->selectAtributosRegistrosRelacionados('locacoes', 'id');
        }

        if($request->filled('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $clienteRepository->filtrarRegistros($request->filtro);
        }

        return response()->json($clienteRepository->getResult());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = Cliente::create([
            'nome' => $request->nome,
        ]);

        return response()->json($cliente, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $clienteRepository = new ClientRepository($this->cliente);

        if($request->filled('atributos_locacoes')) {
            $clienteRepository->selectAtributosRegistrosRelacionados('locacoes', 'id', $request->atributos_locacoes);
        } else {
            $clienteRepository->selectAtributosRegistrosRelacionados('locacoes', 'id');
        }

        if($request->filled('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $clienteRepository->filtrarRegistros($request->filtro);
        }

        $cliente = $clienteRepository->findById($id);

        if(!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }

        return response()->json($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para PATCH
            $regrasDinamicas = [];
            foreach ($this->cliente->rules($cliente->id) as $field => $rule) {
            if ($request->has($field)) {
                $regrasDinamicas[$field] = $rule;
            }
            }

            if (empty($regrasDinamicas)) {
            return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

            $request->validate($regrasDinamicas, $this->cliente->feedback());

            // Atualiza apenas os campos enviados na requisição
            $dadosAtualizados = $request->only(array_keys($regrasDinamicas));

            // Atualiza os campos no modelo
            $cliente->fill($dadosAtualizados);
            $cliente->save();

            return response()->json($cliente, 200);

        } elseif ($request->isMethod('put')) {
            $request->validate($this->cliente->rules($cliente->id), $this->cliente->feedback());

            $cliente->nome = $request->nome;

            $cliente->save();

            return response()->json($cliente, 200);

        } else {
            return response()->json(['error' => 'Método não permitido.'], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }

        $nome = $cliente->nome;

        $cliente->delete();

        return response()->json(['message' => 'Cliente ' . $nome . ' foi removido com sucesso.'], 200);
    }
}
