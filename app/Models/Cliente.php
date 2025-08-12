<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome'
    ];

    public function rules($id = null): array
    {
        return [
            'nome' => 'required|string|max:30|unique:clientes,nome,' . $id
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.max' => 'O campo nome deve ter no máximo 30 caracteres.'
        ];
    }

    public function locacoes()
    {
        return $this->hasMany(Locacao::class);
    }
}
