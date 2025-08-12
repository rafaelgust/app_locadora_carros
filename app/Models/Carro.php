<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo_id',
        'placa',
        'disponivel',
        'km'
    ];

    public function rules($id = null): array
    {
        return [
            'modelo_id' => 'required|exists:modelos,id',
            'placa' => 'required|string|max:7|unique:carros,placa,'.$id,
            'disponivel' => 'required|boolean',
            'km' => 'required|integer|min:0'
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'modelo_id.exists' => 'O modelo selecionado não é válido.',
            'placa.max' => 'O campo placa deve ter no máximo 7 caracteres.',
            'disponivel.required' => 'O campo disponível é obrigatório.',
            'km.required' => 'O campo km é obrigatório.',
            'km.min' => 'O campo km deve ser pelo menos 0.'
        ];
    }

    public function modelo()
    {
    return $this->belongsTo(Modelo::class, 'modelo_id', 'id');
    }

}
