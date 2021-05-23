<?php

namespace Agencia;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    public function parametros(){
       return $this->hasMany('Agencia\Parametro');
    }
}
