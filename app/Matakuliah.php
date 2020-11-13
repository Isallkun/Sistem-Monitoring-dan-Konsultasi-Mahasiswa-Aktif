<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
     protected $table = "matakuliah";
     protected $fillable = ['kodematakuliah','namamatakuliah','sks', 'totalpertemuan', 'nisbimin','thnakademik_idthnakademik','semester_idsemester']; 
}
