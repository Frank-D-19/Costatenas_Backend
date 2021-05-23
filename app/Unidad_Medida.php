<?php

namespace Agencia;

use Illuminate\Database\Eloquent\Model;

class Unidad_Medida extends Model
{
    public function parametros(){
        return $this->hasMany('Agencia\Parametro','unidad_medida_id');
    }
}
