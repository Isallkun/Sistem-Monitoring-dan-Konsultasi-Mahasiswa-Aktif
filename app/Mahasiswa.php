<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
    protected $fillable = ['nrpmahasiswa','namamahasiswa','jeniskelamin','tanggallahir','tempatlahir','email','telepon','alamat','status','flag','profil','users_username','dosen_npkdosen','jurusan_idjurusan', 'gamifikasi_idgamifikasi', 'thnakademik_idthnakademik']; 
}
