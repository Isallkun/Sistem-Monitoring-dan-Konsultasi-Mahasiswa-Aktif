<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gamifikasi extends Model
{
    protected $table = "gamifikasi";
    protected $fillable = ['idgamifikasi','aspek_durasi_konsultasi','aspek_manfaat_konsultasi','aspek_sifat_konsultasi','aspek_interaksi','aspek_pencapaian','total','level']; 
}
