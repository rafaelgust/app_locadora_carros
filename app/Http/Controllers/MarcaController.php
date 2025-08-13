<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    protected $marca;

    protected function uploadImagem(Request $request): string
    {
        try {
            $image = $request->file('imagem');
            return $image->store('imagens/marcas', 'public');
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

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if($request->filled('atributos_modelos')) {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos', 'marca_id', $request->atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos', 'marca_id');
        }

        if($request->filled('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $marcaRepository->filtrarRegistros($request->filtro);
        }

        return response()->json($marcaRepository->getResult());
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

        $imagem_urn = $this->uploadImagem($request);

        $marca = $this->marca;
        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn; // diretorio e nome do arquivo
        $marca->save();

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        if($request->filled('atributos_modelos')) {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos', 'marca_id', $request->atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos', 'marca_id');
        }

        if($request->filled('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }
                
        if($request->filled('filtro')) {
            $marcaRepository->filtrarRegistros($request->filtro);
        }

        $marca = $marcaRepository->findById($id);

        if(!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {  
        // PARA QUE O UPLOAD DA IMG DÊ CERTO, É PRECISO ADICIONAR
        // _method -> put COMO UM PARAMETRO ENCAMINHADO VIA POST
        // NA REQUISIÇÃO, POIS O LARAVEL NÃO PEGA O FORM-DATA COM PUT E PATCH

        $marca = $this->marca->find($id);

        if (!$marca) {
            return response()->json(['error' => 'Marca não encontrada.'], 404);
        }

        if ($request->isMethod('patch')) {
            // Regras dinâmicas para PATCH
            $regrasDinamicas = [];
            foreach ($this->marca->rules($id) as $field => $rule) {
            if ($request->has($field)) {
                $regrasDinamicas[$field] = $rule;
            }
            }

            if (empty($regrasDinamicas) && !$request->hasFile('imagem')) {
            return response()->json(['error' => 'Nenhum campo para atualizar.'], 422);
            }

            $request->validate($regrasDinamicas, $this->marca->feedback());

            // Atualiza apenas os campos enviados na requisição
            $dadosAtualizados = $request->only(array_keys($regrasDinamicas));

            // Se houver upload de imagem, processa e atualiza o campo
            if ($request->hasFile('imagem')) {
                $isRemoved = $this->removeImagem($marca->imagem);
                // Removendo a imagem antiga
                if (!$isRemoved) {
                    return response()->json(['error' => 'Falha ao atualizar a imagem.'], 500);
                }
                // Atualizando para a imagem nova
                $imagem_urn = $this->uploadImagem($request);
                $dadosAtualizados['imagem'] = $imagem_urn;
            }

            // Atualiza os campos no modelo
            $marca->fill($dadosAtualizados);
            $marca->save();

            return response()->json($marca, 200);

        } elseif ($request->isMethod('put')) {
            $request->validate($this->marca->rules($id), $this->marca->feedback());

            $marca->nome = $request->nome;

            if ($request->hasFile('imagem')) {
            $marca->imagem = $this->uploadImagem($request);
            }

            $marca->save();

            return response()->json($marca, 200);

        } else {
            return response()->json(['error' => 'Método não permitido.'], 405);
        }
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

        $nome = $marca->nome;

        $isRemoved = $this->removeImagem($marca->imagem);
        if (!$isRemoved) {
            return response()->json(['error' => 'Falha ao remover a imagem.'], 500);
        }

        $marca->delete();

        return response()->json(['message' => 'Marca '.$nome.' foi deletada com sucesso.'], 200);
    }
}
