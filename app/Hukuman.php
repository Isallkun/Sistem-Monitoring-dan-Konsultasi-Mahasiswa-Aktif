<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hukuman extends Model
{
    protected $table = "hukuman";
    protected $fillable = ['idhukuman','tanggalinput','kategori','namahukuman','keterangan','status','penilaian','tanggalkonfirmasi','masaberlaku','dosen_npkdosen','mahasiswa_nrpmahasiswa']; 
}
 