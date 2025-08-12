<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

     protected $fillable = [
        'marca_id',
        'nome',
        'imagem',
        'numero_portas',
        'lugares',
        'air_bag',
        'abs'
    ];

    public function rules($id = null): array
    {
        return [
            'marca_id' => 'required|exists:marcas,id',
            'nome' => 'required|string|max:50',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'numero_portas' => 'required|integer|min:2|max:5',
            'lugares' => 'required|integer|min:2|max:7',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean',
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'marca_id.exists' => 'A marca selecionada não é válida.',
            'nome.max' => 'O campo nome deve ter no máximo 50 caracteres.',
            'imagem.required' => 'O campo imagem é obrigatório.',
            'numero_portas.min' => 'O campo número de portas deve ter no mínimo 2.',
            'numero_portas.max' => 'O campo número de portas deve ter no máximo 5.',
            'lugares.min' => 'O campo lugares deve ter no mínimo 2.',
            'lugares.max' => 'O campo lugares deve ter no máximo 7.',
            'air_bag.required' => 'O campo air bag é obrigatório.',
            'abs.required' => 'O campo abs é obrigatório.'
        ];
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function carro()
    {
        return $this->hasMany(Carro::class);
    }

}
