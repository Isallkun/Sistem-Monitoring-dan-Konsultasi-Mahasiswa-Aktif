<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
     protected $table = "dosen";
     protected $fillable = ['npkdosen','namadosen','jeniskelamin','email','telepon','status','users_username','jurusan_kodejurusan']; 

     
}
