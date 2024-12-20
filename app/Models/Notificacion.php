<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'id_funcionario',
        'id_peticion',
        'fecha_vencimiento',
        'id_lider_area'
       
    ];

    public function peticion()
    {
        return $this->belongsTo(Peticion::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    public function liderArea()
    {
        return $this->belongsTo(User::class, 'lider_area_id');
    }
}
