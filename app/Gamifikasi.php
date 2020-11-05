<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gamifikasi extends Model
{
    protected $table = "gamifikasi";
     protected $fillable = ['idgamifikasi','poin','level']; 
}
