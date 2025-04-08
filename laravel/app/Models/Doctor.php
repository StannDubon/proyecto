<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Model
{
    use HasApiTokens;
    protected $table ='doctores';

    protected $fillable = [
        'nombre',
        'apellido',
        'clinica_diaria',
        'imagen'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'doctor_id');
    }
}
