<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jadwal_konsultasi extends Model
{
    protected $table = "jadwal_konsultasi";
    protected $fillable = ['idjadwalkonsultasi','judul','tanggalinput','status','tanggalmulai','tanggalberakhir','keterangan']; 
}
