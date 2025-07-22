<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'client_name',
        'descricao',
        'data_inicio',
        'data_fim',
        'status',
        'prioridade',
        'orcamento_estimado',
        'orcamento_real',
        'phase',
        'user_id',
        'active',
        'signature_path',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'orcamento_estimado' => 'decimal:2',
        'orcamento_real' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
