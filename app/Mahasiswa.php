<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = "mahasiswa";
     protected $fillable = ['nrpmahasiswa','namamahasiswa','jeniskelamin','tanggallahir','tempatlahir','email','telepon','angkatan','alamat','status','rate','users_username','dosen_npkdosen','jurusan_kodejurusan']; 
}
