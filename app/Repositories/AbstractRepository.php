<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {

    protected $model;
    private $colunasPermitidas;

    public function __construct(Model $model){
        $this->model = $model;
        $this->colunasPermitidas = $model->getFillable();
    }

    public function selectAtributosRegistrosRelacionados(string $tabela, string $identificador, $atributos = null)
    {
        // Se não houver atributos, apenas carrega o relacionamento completo
        if ($atributos === null || trim($atributos) === '') {
            $this->model = $this->model->with($tabela);
            return;
        }

        // Transforma em array, remove espaços e vazios
        $atributos = array_filter(array_map('trim', explode(',', $atributos)));

        // Sempre inclui o identificador (ex: id)
        if (!in_array($identificador, $atributos)) {
            array_unshift($atributos, $identificador);
        }

        // Garante que não há elementos vazios
        $atributos = array_filter($atributos);

        // Monta a string corretamente
        $this->model = $this->model->with($tabela . ':' . implode(',', $atributos));
    }

    public function selectAtributos(string $atributos)
    {
            $atributos = array_map('trim',  explode(',', $atributos));

        $this->model = $this->model->select($atributos ?? $this->colunasPermitidas);
    }

    public function filtrarRegistros(string $filtro)
    {
         $separarMultiplosFiltros = explode(';', $filtro);

        foreach ($separarMultiplosFiltros as $filtro) {
            $separar = explode(':', $filtro);

            $coluna = trim($separar[0]);
            $operador = trim($separar[1]);
            $valor = $separar[2];

            if(!in_array($coluna, $this->colunasPermitidas)
                OR !in_array($operador, ['=', '!=', '>', '<', '>=', '<=', 'like'])
                OR is_null($valor)
            ) {
                return response()->json(['error' => 'Filtro inválido.'], 400);
            } else {
                $this->model = $this->model->where($coluna, $operador, $valor);
            }
        }
    }

    public function findById($id){
        return $this->model->find($id);
    }

    public function getResult(){
        return $this->model->get();
    }

}