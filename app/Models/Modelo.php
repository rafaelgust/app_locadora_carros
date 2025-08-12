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
            'nome' => 'required|string|max:30|min:3|unique:modelos,nome,'.$id,
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
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
            'numero_portas.min' => 'O campo número de portas deve ter no mínimo 1.',
            'numero_portas.max' => 'O campo número de portas deve ter no máximo 5.',
            'lugares.min' => 'O campo lugares deve ter no mínimo 1.',
            'lugares.max' => 'O campo lugares deve ter no máximo 20.',
            'air_bag.boolean' => 'O campo air bag deve ser verdadeiro ou falso.',
            'abs.boolean' => 'O campo abs deve ser verdadeiro ou falso.'
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
