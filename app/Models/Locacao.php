<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    protected $table = "locacoes";

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];

    public function rules($id = null): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'carro_id' => 'required|exists:carros,id',
            'data_inicio_periodo' => 'required|date',
            'data_final_previsto_periodo' => 'required|date',
            'data_final_realizado_periodo' => 'nullable|date',
            'valor_diaria' => 'required|numeric|min:0',
            'km_inicial' => 'required|integer|min:0',
            'km_final' => 'required|integer|min:0'
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'cliente_id.exists' => 'O cliente selecionado não é válido.',
            'carro_id.exists' => 'O carro selecionado não é válido.',
            'data_inicio_periodo.date' => 'O campo data de início deve ser uma data válida.',
            'data_final_previsto_periodo.date' => 'O campo data final previsto deve ser uma data válida.',
            'data_final_realizado_periodo.date' => 'O campo data final realizado deve ser uma data válida.',
            'valor_diaria.numeric' => 'O campo valor da diária deve ser um número.',
            'valor_diaria.min' => 'O campo valor da diária deve ser pelo menos 0.',
            'km_inicial.integer' => 'O campo km inicial deve ser um número inteiro.',
            'km_inicial.min' => 'O campo km inicial deve ser pelo menos 0.',
            'km_final.integer' => 'O campo km final deve ser um número inteiro.',
            'km_final.min' => 'O campo km final deve ser pelo menos 0.'
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
