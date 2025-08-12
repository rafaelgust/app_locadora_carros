<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'imagem',
    ];

    public function rules($id = null): array
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$id.'|string|max:50',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        /**
         * unique
         * 1)tabela
         * 2)nome da coluna
         * 3)id do registro a ser ignorado (no caso de update)
         * unique:marca,nome,{{id}}
         */
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.max' => 'O campo nome deve ter no máximo 50 caracteres.',
            'nome.unique' => 'Já existe uma marca com este nome.',
            'imagem.required' => 'O campo imagem é obrigatório.'
        ];
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}