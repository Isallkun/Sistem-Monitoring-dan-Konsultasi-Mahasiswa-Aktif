<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hukuman extends Model
{
    protected $table = "hukuman";
    protected $fillable = ['idhukuman','tanggalinput','keterangan','status','penilaian','tanggalkonfirmasi','masaberlaku','jenishukuman_idjenishukuman','dosen_npkdosen','mahasiswa_nrpmahasiswa']; 
}
 