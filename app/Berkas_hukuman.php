<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Berkas_hukuman extends Model
{
    protected $table = "berkas_hukuman";
    protected $fillable = ['idberkashukuman','berkas','hukuman_idhukuman']; 
}
