<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konsultasi_dosenwali extends Model
{
    protected $table = "konsultasi_dosenwali";
    protected $fillable = ['idkonsultasi','tanggalkonsultasi','permasalahan', 'solusi', 'konsultasiselanjutnya','konfirmasi','dosen_npkdosen','topik_idtopikkonsultasi','mahasiswa_nrpmahasiswa','semester_idsemester','thnakademik_idthnakademik']; 
}
