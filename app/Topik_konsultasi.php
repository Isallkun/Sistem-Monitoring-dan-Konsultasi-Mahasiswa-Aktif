<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topik_konsultasi extends Model
{
    protected $table = "topik_konsultasi";
    protected $fillable = ['idtopikkonsultasi','namatopik']; 
}
