<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Non_konsultasi extends Model
{
    protected $table = "non_konsultasi";
    protected $fillable = ['idnonkonsultasi','tanggalinput','tanggalpertemuan','status','pesan','mahasiswa_nrpmahasiswa','dosen_npkdosen']; 
}
