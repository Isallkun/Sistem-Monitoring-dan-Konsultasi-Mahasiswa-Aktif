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


            // $ks_mahasiswa1 = DB::table('kartu_studi')
            // ->distinct()
            // ->get();
            // $kartustudi1=[];
            // foreach ($ks_mahasiswa1 as $key => $value) 
            // {
            // 	$kartustudi1[] = $value->mahasiswa_nrpmahasiswa;
            // }

            // $mahasiswa_kosong = DB::table('mahasiswa')
            // ->select('nrpmahasiswa','namamahasiswa', DB::raw("'0' as totalsks,'0.0' as ips,'0.0' as ipk,'0.0' as ipkm"))
            // ->whereNotIn('nrpmahasiswa',$kartustudi1)
            // ->get();

            //---------------------------------------

            $kartu_studi= DB::table('kartu_studi')
            ->select(DB::raw("max(idkartustudi) as idkartustudi"))
            ->groupBy('mahasiswa_nrpmahasiswa')
            ->get();  // [ 0=>idkartustudi=>1 , 1=>idkartustudi=>2 ]
            $whereinCondition=[];
            foreach ($kartu_studi as $key => $value) 
            {
             $whereinCondition[] = $value->idkartustudi;
            }

            $mahasiswa = DB::table('mahasiswa')
            ->select('mahasiswa.*','tahun_akademik.tahun', 'kartu_studi.*')
            ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->join('kartu_studi','kartu_studi.mahasiswa_nrpmahasiswa','=','mahasiswa.nrpmahasiswa')
            ->where('mahasiswa.dosen_npkdosen',$dosen[0]->npkdosen )
            ->where('mahasiswa.status','aktif')
            ->whereIn('kartu_studi.idkartustudi', $whereinCondition) // wherein condition [1,2,3,4]
            ->orderBy('kartu_studi.idkartustudi','DESC')
            ->paginate(10);

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
            //a. informasi dalam bentuk total angka
            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
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

            //b. informasi dalam bentuk grafik 
            $grafik_akademik= DB::table('kartu_studi')
            ->select('ipk','ips','semester','tahun','totalsks')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('semester','semester.idsemester','=','kartu_studi.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->get();
           

            //2. Detail Profil Mahasiswa
            $data_mahasiswa = DB::table('kartu_studi')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('jurusan','jurusan.idjurusan','=','mahasiswa.jurusan_idjurusan')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->where('idkartustudi',$whereinCondition)
            ->get();



            //5. Detail Hukuman
            $data_hukuman = DB::table('hukuman')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->paginate(10);
            
            
            return view('data_mahasiswa.detailmahasiswa_dosen', compact('total_konsultasi','total_hukuman','total_nisbi_d','sisasks','grafik_akademik','data_mahasiswa','data_hukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

   
}
