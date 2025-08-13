<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    protected $modelo;

    protected function uploadImagem(Request $request): string
    {
        try {
            $image = $request->file('imagem');
            return $image->store('imagens/modelos', 'public');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao fazer upload da imagem.'], 500);
        }
    }

    protected function removeImagem(string $imagemPath): bool
    {
        try {
            Storage::disk('public')->delete($imagemPath);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->filled('atributos_marca')) {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca', 'id', $request->atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca', 'id');
        }

        if($request->filled('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $modeloRepository->filtrarRegistros($request->filtro);
        }

        return response()->json($modeloRepository->getResult());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // PRIMEIRO VALIDA OS CAMPOS
        $request->validate($this->modelo->rules(), $this->modelo->feedback());
        // NÃO É NECESSÁRIO VERIFICAR SE JÁ EXISTE, UMA VEZ QUE A VALIDAÇÃO FAZ ISSO
        // FAZ UPLOAD DA IMG
        $imagem_urn = $this->uploadImagem($request);

        //CRIA O OBJ E SALVA
        $modelo = Modelo::create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        //RETORNA
        return response()->json($modelo, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->filled('atributos_marca')) {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca', 'id', $request->atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca', 'id');
        }

        if($request->filled('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $modeloRepository->filtrarRegistros($request->filtro);
        }

        $modelo = $modeloRepository->findById($id);

        if (!$modelo) {
            return response()->json(['error' => 'Modelo não encontrado.'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $modelo = Modelo::find($id);
        if (!$modelo) {
            return response()->json(['error' => 'Modelo não encontrado.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para PATCH
            $regrasDinamicas = [];
            foreach ($this->modelo->rules($modelo->id) as $field => $rule) {
            if ($request->has($field)) {
                $regrasDinamicas[$field] = $rule;
            }
            }

            if (empty($regrasDinamicas) && !$request->hasFile('imagem')) {
            return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

            $request->validate($regrasDinamicas, $this->modelo->feedback());

            // Atualiza apenas os campos enviados na requisição
            $dadosAtualizados = $request->only(array_keys($regrasDinamicas));

            // Se houver upload de imagem, processa e atualiza o campo
            if ($request->hasFile('imagem')) {
            $isRemoved = $this->removeImagem($modelo->imagem);
            // Removendo a imagem antiga
            if (!$isRemoved) {
                return response()->json(['error' => 'Falha ao atualizar a imagem.'], 500);
            }
            // Atualizando para a imagem nova
            $imagem_urn = $this->uploadImagem($request);
            $dadosAtualizados['imagem'] = $imagem_urn;
            }

            // Atualiza os campos no modelo
            $modelo->fill($dadosAtualizados);
            $modelo->save();

            return response()->json($modelo, 200);

        } elseif ($request->isMethod('put')) {
            $request->validate($this->modelo->rules($modelo->id), $this->modelo->feedback());

            $modelo->marca_id = $request->marca_id;
            $modelo->nome = $request->nome;
            $modelo->numero_portas = $request->numero_portas;
            $modelo->lugares = $request->lugares;
            $modelo->air_bag = $request->air_bag;
            $modelo->abs = $request->abs;

            if ($request->hasFile('imagem')) {
            $this->removeImagem($modelo->imagem);
            $modelo->imagem = $this->uploadImagem($request);
            }

            $modelo->save();

            return response()->json($modelo, 200);

        } else {
            return response()->json(['error' => 'Método não permitido.'], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $modelo = Modelo::find($id);
        if (!$modelo) {
            return response()->json(['error' => 'Modelo não encontrado.'], 404);
        }

        $nome = $modelo->nome;

        $isRemoved = $this->removeImagem($modelo->imagem);
        if (!$isRemoved) {
            return response()->json(['error' => 'Falha ao remover a imagem.'], 500);
        }

        $modelo->delete();

        return response()->json(['message' => 'Modelo ' . $nome . ' removido com sucesso.'], 200);
    }
}
