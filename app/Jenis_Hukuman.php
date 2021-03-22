<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenis_Hukuman extends Model
{
    protected $table = "jenis_hukuman";
    protected $fillable = ['idjenishukuman','kateogri','namahukuman']; 
}
