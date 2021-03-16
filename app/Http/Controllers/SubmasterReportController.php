<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use File;
use ZipArchive;
use Carbon\Carbon;

class SubmasterReportController extends Controller
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


    public function daftarkonsultasi()
    {
    	if(Session::get('ketuajurusan') != null)
        {
        	$data_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
          	->orderBy('konsultasi_dosenwali.idkonsultasi','DESC')
            ->get();

        	return view('submaster_konsultasi_nonkonsultasi.daftarkonsultasi_ketuajurusan', compact('data_konsultasi'));
        }
        else
        {
        	return redirect("/");
        }
    }
    
    public function daftarnonkonsultasi()
    {
    	if(Session::get('ketuajurusan') != null)
        {
        	
        	$data_non_konsultasi = DB::table('non_konsultasi')
        	->select('*')
        	->join('dosen','dosen.npkdosen','=','non_konsultasi.dosen_npkdosen')
			->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
			->get();
			
        	return view('submaster_konsultasi_nonkonsultasi.daftarnonkonsultasi_ketuajurusan', compact('data_non_konsultasi'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function daftarhukuman()
    {
        if(Session::get('ketuajurusan') != null)
        {
            $data_hukuman = DB::table('hukuman')
            ->select('hukuman.*','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','dosen.namadosen','dosen.npkdosen')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->get();

            return view('submaster_hukuman.daftarhukuman_ketuajurusan', compact('data_hukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function unduhberkas_proses(Request $request, $id)
    {
        if(Session::get('ketuajurusan') != null)
        {
        	$nrpmahasiswa = $request->get('nrpmahasiswa');

            $berkas_hukuman = DB::table('hukuman')
            ->select('berkas_hukuman.berkas')
            ->join('berkas_hukuman','berkas_hukuman.hukuman_idhukuman','=','hukuman.idhukuman')
            ->where('idhukuman',$id)
            ->get();


            //Load ZIP Library  
            $zip = new ZipArchive;
            $zipname = time()."_".$nrpmahasiswa.".zip";

            //Membuat File ZIP
            if ($zip->open(public_path($zipname), ZipArchive::CREATE) === TRUE)
            {
                $files = File::files(public_path('data_hukuman'));
       
                foreach ($berkas_hukuman as $key => $value) 
                {
                    $zip->addFile("data_hukuman/".$value->berkas);
                }
                 
                $zip->close();
            } 
           
            //Mengunduh File ZIP yang telah dibentuk
            if(file_exists($zipname))
            {
                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename="'.$zipname.'"');
                header('Content-Length: ' . filesize($zipname));
                readfile($zipname);
                unlink($zipname);
            } 
            else
            {
                $informasi = "Proses mengkompresi file gagal";
            } 
        }
        
        else
        {
            return redirect("/");
        }
    }

    
}
