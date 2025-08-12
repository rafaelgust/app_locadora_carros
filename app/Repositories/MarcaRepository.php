<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class MarcaRepository {

    protected $model;

    private array $filtroMarcas = array('id', 'nome', 'imagem');
    private array $filtroModelos = array('nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs');


    public function __construct(Model $model){
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados(string $tabela, string $identificador, string $atributos)
    {
        $atributos_modelos = array_map('trim', explode(',', $atributos));
        $filtroModelos = $atributos_modelos;

        $this->model = $this->model->with(''.$tabela.':'.$identificador.',' . implode(',', $filtroModelos));
    }

    public function selectAtributos(string $atributos)
    {
            $atributos = array_map('trim',  explode(',', $atributos));

            $filtroMarcas = $atributos;
            $this->model = $this->model->select($filtroMarcas);
    }

    public function filtrarRegistros(string $filtro)
    {
        $separarMultiplosFiltros = explode(';', $filtro);

        foreach ($separarMultiplosFiltros as $filtro) {
            $separar = explode(':', $filtro);

            $coluna = trim($separar[0]);
            $operador = trim($separar[1]);
            $valor = $separar[2];

            if(!in_array($coluna, $this->filtroMarcas)
                OR !in_array($operador, ['=', '!=', '>', '<', '>=', '<=', 'like'])
                OR is_null($valor)
            ) {
                return response()->json(['error' => 'Filtro inválido.'], 400);
            } else {
                $this->model = $this->model->where($coluna, $operador, $valor);
            }
        }
    }

    public function getResult(){
        return $this->model->get();
    }

}