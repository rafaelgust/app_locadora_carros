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

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'imagem' => 'required'
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.max' => 'O campo nome deve ter no máximo 255 caracteres.',
            'imagem.required' => 'O campo imagem é obrigatório.'
        ];
    }
}
