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


            $kartu_studi= DB::table('kartu_studi')
            ->select(DB::raw("max(idkartustudi) as idkartustudi"))
            ->groupBy('mahasiswa_nrpmahasiswa')
            ->get();  // [ 0=>idkartustudi=>1 , 1=>idkartustudi=>2 ]
          
            $whereinCondition=[];
            foreach ($kartu_studi as $key => $value) 
            {
            	$whereinCondition[] = $value->idkartustudi;
            }

           // dd($whereinCondition);

            //QUERY BELUM SEMPURNA

            $mahasiswa = DB::table('mahasiswa')
            ->select('mahasiswa.*','tahun_akademik.tahun', 'kartu_studi.*')
            ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->leftjoin('kartu_studi','kartu_studi.mahasiswa_nrpmahasiswa','=','mahasiswa.nrpmahasiswa')
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
}
