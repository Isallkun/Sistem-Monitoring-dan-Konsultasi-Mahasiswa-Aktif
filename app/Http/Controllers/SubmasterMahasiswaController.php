<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use File;

class SubmasterMahasiswaController extends Controller
{
	public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarmahasiswa()
    {
        if(Session::get('ketuajurusan') != null)
        {
            $mahasiswa = DB::table('mahasiswa')
            ->select('*')
            ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('gamifikasi','idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->join('tahun_akademik','idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->get();

            return view('submaster_mahasiswa.daftarmahasiswa_ketuajurusan', compact('mahasiswa'));
        }
        else
        {
            return redirect("/");
        }
    }

     public function detailmahasiswa($id)
    {
        if(Session::get('ketuajurusan') != null)
        {    
            $data_mahasiswa = DB::table('mahasiswa')
            ->select('*',
                     DB::raw("gamifikasi.aspek_durasi_konsultasi/5 AS avg_aspek1"),
                     DB::raw("gamifikasi.aspek_manfaat_konsultasi/5 AS avg_aspek2"),
                     DB::raw("gamifikasi.aspek_sifat_konsultasi/5 AS avg_aspek3"),
                     DB::raw("gamifikasi.aspek_interaksi/5 AS avg_aspek4"),
                     DB::raw("gamifikasi.aspek_pencapaian/5 AS avg_aspek5"))

            ->join('jurusan','jurusan.idjurusan','=','mahasiswa.jurusan_idjurusan')
            ->join('fakultas','fakultas.idfakultas','=', 'jurusan.fakultas_idfakultas')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->get();

            //5. Detail Konsultasi Mahasiswa
            $data_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->orderBy('konsultasi_dosenwali.idkonsultasi','DESC')
            ->get();
            
            $data_nonkonsultasi = DB::table('non_konsultasi')
            ->join('dosen','dosen.npkdosen','=','non_konsultasi.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->orderBy('non_konsultasi.idnonkonsultasi','DESC')
            ->get();

            //6. Detail Hukuman Mahasiswa
            $data_hukuman = DB::table('hukuman')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->get();
            
            
            return view('submaster_mahasiswa.detailmahasiswa_ketuajurusan', compact('data_mahasiswa','data_konsultasi','data_nonkonsultasi','data_hukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

}
