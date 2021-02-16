<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;

use Session;
use DB;
use File;

use App\Dosen;

class ProfilDosenController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function profil_dosen()
    {
    	if(Session::get('dosen') != null)
        {
        	$user_dosen = DB::table('dosen')
            ->select('*')
            ->join('users','users.username','=', 'dosen.users_username')
            ->join('jurusan','jurusan.idjurusan','=', 'dosen.jurusan_idjurusan')
            ->join('fakultas','fakultas.idfakultas','=', 'jurusan.fakultas_idfakultas')
            ->where('users.username', Session::get('dosen'))
            ->get();

            $decrypted = Crypt::decryptString($user_dosen[0]->password);

            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan'))
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('dosen.npkdosen',$user_dosen[0]->npkdosen)
            ->groupBy('bulan')
            ->orderBy('bulan','DESC')
            ->get();

            return view('profil_user.profil_dosen',compact('user_dosen','total_konsultasi', 'decrypted'));
        }
        else
        {
            return redirect('/');  
        }
    }

    public function ubahprofildosen_proses(Request $request)
    {
    	try
    	{
    		$this->validate($request,[
                'name' =>'required',
                'password' =>'required|max:10'
            ]);

            $password = $request->get('password');
            $encrypted = Crypt::encryptString($password);
            
            $dosen = DB::table('dosen') 
                ->join('users','users.username','=','dosen.users_username')
                ->where('npkdosen',$request->get('npk_dosen'))
                ->update([    
                    'namadosen'=>$request->get('name'),              
                    'password' =>$encrypted
            ]);    


	        return redirect('dosen/profil/profildosen')->with(['Success' => 'Berhasil Mengubah Data Profil Anda']);
    	}
    	
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("dosen/profil/profildosen")->with(['Error' => 'Gagal Mengubah Data Profil Anda <br> Pesan Kesalahan: '.$message[0]]);
        }

    }
}
