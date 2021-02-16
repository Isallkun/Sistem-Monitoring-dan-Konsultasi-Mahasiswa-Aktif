<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;

use Session;
use DB;
use File;

use App\Mahasiswa;

class ProfilMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function profil_mahasiswa()
    {
    	if(Session::get('mahasiswa') != null)
    	{
    		$user_mahasiswa = DB::table('mahasiswa')
            ->select('*')
            ->join('users','users.username','=', 'mahasiswa.users_username')
            ->join('jurusan','jurusan.idjurusan','=', 'mahasiswa.jurusan_idjurusan')
            ->join('fakultas','fakultas.idfakultas','=', 'jurusan.fakultas_idfakultas')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->where('users.username', Session::get('mahasiswa'))
            ->get();

            $decrypted = Crypt::decryptString($user_mahasiswa[0]->password);

            
            return view('profil_user.profil_mahasiswa',compact('user_mahasiswa', 'decrypted'));
    	}
    	else
        {
            return redirect('/');  
        }
    }

    public function ubahprofilmahasiswa_proses(Request $request)
    {
    	try
    	{
    		$this->validate($request,[
                'name' =>'required',
                'password' =>'required|max:10'
            ]);

            $password = $request->get('password');
            $encrypted = Crypt::encryptString($password);
            
            $mahasiswa = DB::table('mahasiswa') 
                ->join('users','users.username','=','mahasiswa.users_username')
                ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
                ->update([    
                    'namamahasiswa'=>$request->get('name'),              
                    'password' =>$encrypted
            ]);    


	        return redirect('mahasiswa/profil/profilmahasiswa')->with(['Success' => 'Berhasil Mengubah Data Profil Anda']);
    	}
    	
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("mahasiswa/profil/profilmahasiswa")->with(['Error' => 'Gagal Mengubah Data Profil Anda <br> Pesan Kesalahan: '.$message[0]]);
        }

    }
}
