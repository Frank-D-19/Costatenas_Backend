<?php

namespace Agencia;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    public function user(){
        return $this->belongsTo('Agencia\User');
    }
}
