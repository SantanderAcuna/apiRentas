<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peticion extends Model
{
    use HasFactory;

    protected $table = 'peticiones';

    protected $fillable = [
        'tipo_peticion',
        'contribuyente_id',
        'funcionario_id'
    ];

    protected $dates = ['fecha_asignacion', 'fecha_vencimiento'];


    public function contribuyente()
    {
        return $this->belongsTo(Contribuyente::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }
}
