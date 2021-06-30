<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;


use DB;
use Session;
use File;

use App\User;
use App\Mahasiswa;
use App\Gamifikasi;

class DataMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarmahasiswa()
    {
        if(Session::get('dosen') != null)
        {
        	$dosen = DB::table('dosen')
            ->join('users','users.username','=', 'dosen.users_username')
            ->where('users.username', Session::get('dosen'))
            ->get();


            // KS untuk mendapatkan MAHALA
            $kartu_studi= DB::table('kartu_studi')
            ->select(DB::raw("max(idkartustudi) as idkartustudi"))
            ->join('semester','semester.idsemester','=','kartu_studi.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('semester.status','0')
            ->groupBy('mahasiswa_nrpmahasiswa')
            ->get();  // [ 0=>idkartustudi=>1 , 1=>idkartustudi=>2 ]
            $whereinCondition=[];
            foreach ($kartu_studi as $key => $value) 
            {
             $whereinCondition[] = $value->idkartustudi;
            }

            // KS untuk mendapatkan MAHARU
            // $tahunakademik_aktif = DB::table('tahun_akademik')
            // ->select('idtahunakademik','tahun')
            // ->where('status', '1')
            // ->get();
            $tahunakademik_aktif = DB::table('mahasiswa')
            ->select('mahasiswa.nrpmahasiswa')
            ->join('dosen','dosen.npkdosen', '=', 'mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->where('tahun_akademik.status', '1')
            ->where('mahasiswa.dosen_npkdosen', $dosen[0]->npkdosen)
            ->get();
            $whereinMaharu=[];
            foreach ($tahunakademik_aktif as $key => $value) 
            {
             $whereinMaharu[] = $value->nrpmahasiswa;
            }

            $mahasiswa = DB::table('mahasiswa')
            ->select('mahasiswa.*', 'kartu_studi.*','gamifikasi.*','tahun_akademik.*')
            ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('kartu_studi','kartu_studi.mahasiswa_nrpmahasiswa','=','mahasiswa.nrpmahasiswa')
            ->join('gamifikasi','idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->where('mahasiswa.dosen_npkdosen',$dosen[0]->npkdosen )
            ->where('mahasiswa.status','aktif')
            //Where In untuk mendapatkan KS mahasiswa lama
            ->whereIn('kartu_studi.idkartustudi', $whereinCondition)
            //Where untuk mendapatkan KS mahasiswa baru
            ->orwhereIn('mahasiswa.nrpmahasiswa',$whereinMaharu)
            ->groupBy('mahasiswa_nrpmahasiswa')
            ->orderBy('mahasiswa_nrpmahasiswa','ASC')
            ->get();

            // dd($mahasiswa);

            return view('data_mahasiswa.daftarmahasiswa_dosen', compact('mahasiswa'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahflag($id)
    {  
        if(Session::get('dosen') != null)
        {
            $status_flag = DB::table('mahasiswa')
            ->select("*")
            ->where('nrpmahasiswa', $id)
            ->get();

           	if($status_flag[0]->flag ==0)
           	{
           		$mahasiswa = DB::table('mahasiswa') 
                ->where('nrpmahasiswa',$id)
                ->update([
                	'flag' => 1
                ]);    
           	}
           	else if($status_flag[0]->flag ==1)
           	{
           		$mahasiswa = DB::table('mahasiswa') 
                ->where('nrpmahasiswa',$id)
                ->update([
                    'flag' => 2
                ]);    
           	}
           	else
           	{
           		$mahasiswa = DB::table('mahasiswa') 
                ->where('nrpmahasiswa',$id)
                ->update([
                    'flag' => 0
                ]);    
           	}

           	return redirect('dosen/data/mahasiswa');
        }
        else
        {
            return redirect("/");
        }
    }

    public function detailmahasiswa($id)
    {
        if(Session::get('dosen') != null)
        {
            //1. Detail Informasi Mahasiswa
            //1a. informasi dalam bentuk total angka
            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->count();
            $total_nonkonsultasi = DB::table('non_konsultasi')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->count();

            $total_hukuman = DB::table('hukuman')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->count();

            $total_nisbi_d = DB::table('kartu_studi')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->where('detail_kartu_studi.nisbi','D')
            ->count();

            $kartu_studi= DB::table('kartu_studi')
            ->select(DB::raw("max(idkartustudi) as idkartustudi"))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->groupBy('mahasiswa_nrpmahasiswa')
            ->get();  // [ 0=>idkartustudi=>1 , 1=>idkartustudi=>2 ]
            $whereinCondition=[];
            foreach ($kartu_studi as $key => $value) 
            {
             $whereinCondition[] = $value->idkartustudi;
            }
            $total_sks = DB::table('kartu_studi')
            ->select('idkartustudi',DB::raw("(144-totalsks) as sisasks"))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->whereIn('kartu_studi.idkartustudi', $whereinCondition)
            ->get();
            $sisasks = $total_sks[0]->sisasks;
 
            //1b. informasi dalam bentuk grafik 
            $grafik_akademik= DB::table('kartu_studi')
            ->select('idkartustudi','ipk','ips','semester','tahun','totalsks')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('semester','semester.idsemester','=','kartu_studi.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->orderBy('idkartustudi','ASC')
            ->get();

            //Hitung sks mahasiswa per semester
            
            
            //2. Detail Profil Mahasiswa
            $data_mahasiswa = DB::table('kartu_studi')
            ->select('*',
                     DB::raw("gamifikasi.aspek_durasi_konsultasi/5 AS avg_aspek1"),
                     DB::raw("gamifikasi.aspek_manfaat_konsultasi/5 AS avg_aspek2"),
                     DB::raw("gamifikasi.aspek_sifat_konsultasi/5 AS avg_aspek3"),
                     DB::raw("gamifikasi.aspek_interaksi/5 AS avg_aspek4"),
                     DB::raw("gamifikasi.aspek_pencapaian/5 AS avg_aspek5"))

            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('jurusan','jurusan.idjurusan','=','mahasiswa.jurusan_idjurusan')
            ->join('fakultas','fakultas.idfakultas','=', 'jurusan.fakultas_idfakultas')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->where('idkartustudi',$whereinCondition)
            ->get(); 

            //3. Kartu Hasil Studi
            // Menampilkan data semester dan tahun akademik di Combobox
            $semester = DB::table('semester')
            ->get();
            $tahunakademik = DB::table('tahun_akademik')
            ->get();

            // Mendapatkan semester dan tahun akademik (AKTIF)
            $semester_aktif = DB::table('semester')
            ->select('idsemester','semester')
            ->where('status', '1')
            ->get();
            $tahunakademik_aktif = DB::table('tahun_akademik')
            ->select('idtahunakademik','tahun')
            ->where('status', '1')
            ->get();

            $data_kartustudi = DB::table('kartu_studi')
            ->join('mahasiswa','nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
            ->join('matakuliah','matakuliah.kodematakuliah','=','detail_kartu_studi.matakuliah_kodematakuliah')
            ->where('kartu_studi.semester_idsemester',$semester_aktif[0]->idsemester)
            ->where('kartu_studi.thnakademik_idthnakademik',$tahunakademik_aktif[0]->idtahunakademik)
            ->where('kartu_studi.mahasiswa_nrpmahasiswa',$id)
            ->get();

            //4. Transkrip
            // $tahunakademik_tidakaktif = DB::table('tahun_akademik')
            // ->select('idtahunakademik','tahun')
            // ->where('status',0)
            // ->get();
            // $whereinTahun=[];
            // foreach ($tahunakademik_tidakaktif as $key => $value) 
            // {
            //     $whereinTahun[] = $value->idtahunakademik;
            // }

            $data_transkrip = DB::table('kartu_studi')
            ->join('mahasiswa','nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
            ->join('matakuliah','matakuliah.kodematakuliah','=','detail_kartu_studi.matakuliah_kodematakuliah')
            ->where('kartu_studi.mahasiswa_nrpmahasiswa',$id)
            ->where('detail_kartu_studi.na','!=','0')
            // ->whereIn('kartu_studi.thnakademik_idthnakademik', $whereinTahun)
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
            ->select('dosen.namadosen','dosen.npkdosen','hukuman.tanggalinput','hukuman.namahukuman','hukuman.keterangan', 'hukuman.status','hukuman.penilaian','hukuman.tanggalkonfirmasi', 'hukuman.masaberlaku')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->get();
            
            return view('data_mahasiswa.detailmahasiswa_dosen', compact('total_konsultasi','total_nonkonsultasi','total_hukuman','total_nisbi_d','sisasks','grafik_akademik','data_mahasiswa','data_konsultasi','data_nonkonsultasi','data_kartustudi','data_transkrip','data_hukuman','semester','tahunakademik'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function carikartustudi (Request $request)
    {
        $nrpmahasiswa = $request->get('nrpmahasiswa');
        $semesters = $request->get('semester');
        $tahun_akademiks = $request->get('tahunakademik');
       
        
        $this->validate($request,[
            'semester' => 'required',
            'tahunakademik' =>'required'
        ]);

        $data_kartustudi = DB::table('kartu_studi')
        ->join('mahasiswa','nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
        ->join('matakuliah','matakuliah.kodematakuliah','=','detail_kartu_studi.matakuliah_kodematakuliah')
        ->where('kartu_studi.semester_idsemester',$semesters)
        ->where('kartu_studi.thnakademik_idthnakademik',$tahun_akademiks)
        ->where('kartu_studi.mahasiswa_nrpmahasiswa',$nrpmahasiswa)
        ->get();
            
    
         //1. Detail Informasi Mahasiswa
        //1a. informasi dalam bentuk total angka
        $total_konsultasi = DB::table('konsultasi_dosenwali')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->count();
        $total_nonkonsultasi = DB::table('non_konsultasi')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->count();

        $total_hukuman = DB::table('hukuman')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->count();

        $total_nisbi_d = DB::table('kartu_studi')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->where('detail_kartu_studi.nisbi','D')
        ->count();

        $kartu_studi= DB::table('kartu_studi')
        ->select(DB::raw("max(idkartustudi) as idkartustudi"))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->groupBy('mahasiswa_nrpmahasiswa')
        ->get();  // [ 0=>idkartustudi=>1 , 1=>idkartustudi=>2 ]
        $whereinCondition=[];
        foreach ($kartu_studi as $key => $value) 
        {
         $whereinCondition[] = $value->idkartustudi;
        }
        $total_sks = DB::table('kartu_studi')
        ->select('idkartustudi',DB::raw("(144-totalsks) as sisasks"))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->whereIn('kartu_studi.idkartustudi', $whereinCondition)
        ->get();
        $sisasks = $total_sks[0]->sisasks;

        //1b. informasi dalam bentuk grafik 
        $grafik_akademik= DB::table('kartu_studi')
        ->select('idkartustudi','ipk','ips','semester','tahun','totalsks')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('semester','semester.idsemester','=','kartu_studi.semester_idsemester')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->orderBy('idkartustudi','ASC')
        ->get();

        //2. Detail Profil Mahasiswa
        $data_mahasiswa = DB::table('kartu_studi')
        ->select('*',
                 DB::raw("gamifikasi.aspek_durasi_konsultasi/5 AS avg_aspek1"),
                 DB::raw("gamifikasi.aspek_manfaat_konsultasi/5 AS avg_aspek2"),
                 DB::raw("gamifikasi.aspek_sifat_konsultasi/5 AS avg_aspek3"),
                 DB::raw("gamifikasi.aspek_interaksi/5 AS avg_aspek4"),
                 DB::raw("gamifikasi.aspek_pencapaian/5 AS avg_aspek5"))
        
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('jurusan','jurusan.idjurusan','=','mahasiswa.jurusan_idjurusan')
        ->join('fakultas','fakultas.idfakultas','=', 'jurusan.fakultas_idfakultas')
        ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->where('idkartustudi',$whereinCondition)
        ->get();

        //3. Detail Akademik
        //Load data isi combobox
        $semester = DB::table('semester')
        ->get();
        $tahunakademik = DB::table('tahun_akademik')
        ->get();

        $data_kartustudi = DB::table('kartu_studi')
        ->join('mahasiswa','nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
        ->join('matakuliah','matakuliah.kodematakuliah','=','detail_kartu_studi.matakuliah_kodematakuliah')
        ->where('kartu_studi.semester_idsemester',$semesters)
        ->where('kartu_studi.thnakademik_idthnakademik',$tahun_akademiks )
        ->where('kartu_studi.mahasiswa_nrpmahasiswa',$nrpmahasiswa)
        ->get();
 
        //4. Transkrip
        // $tahunakademik_tidakaktif = DB::table('tahun_akademik')
        // ->select('idtahunakademik','tahun')
        // ->where('status',0)
        // ->get();
        // $whereinTahun=[];
        // foreach ($tahunakademik_tidakaktif as $key => $value) 
        // {
        //     $whereinTahun[] = $value->idtahunakademik;
        // }

        $data_transkrip = DB::table('kartu_studi')
        ->join('mahasiswa','nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
        ->join('matakuliah','matakuliah.kodematakuliah','=','detail_kartu_studi.matakuliah_kodematakuliah')
        ->where('kartu_studi.mahasiswa_nrpmahasiswa',$nrpmahasiswa)
        ->where('detail_kartu_studi.na','!=','0')
        ->get();
        
        //5. Detail Konsultasi Mahasiswa
        $data_konsultasi = DB::table('konsultasi_dosenwali')
        ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
        ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
          ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->orderBy('konsultasi_dosenwali.idkonsultasi','DESC')
        ->get();

         $data_nonkonsultasi = DB::table('non_konsultasi')
        ->join('dosen','dosen.npkdosen','=','non_konsultasi.dosen_npkdosen')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->orderBy('non_konsultasi.idnonkonsultasi','DESC')
        ->get();
        
        //6. Detail Hukuman Mahasiswa
        $data_hukuman = DB::table('hukuman')
        ->select('dosen.namadosen','dosen.npkdosen','hukuman.tanggalinput','hukuman.namahukuman','hukuman.keterangan', 'hukuman.status','hukuman.penilaian','hukuman.tanggalkonfirmasi', 'hukuman.masaberlaku')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
        ->where('mahasiswa.nrpmahasiswa',$nrpmahasiswa)
        ->get();    
            
         return view('data_mahasiswa.detailmahasiswa_dosen', compact('total_konsultasi','total_nonkonsultasi','total_hukuman','total_nisbi_d','sisasks','grafik_akademik','data_mahasiswa','data_konsultasi','data_nonkonsultasi','data_kartustudi','data_transkrip','data_hukuman','semester','tahunakademik'));
    }

   
}
