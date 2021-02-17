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
    		$message=[];

    		$namalengkap = $request->get('namalengkap');
    		$password_lama = $request->get('password_lama');
    		$password_baru = $request->get('password_baru');
    		$password_re_baru = $request->get('password_re-baru');


            if($namalengkap)
            {
            	$mahasiswa = DB::table('mahasiswa') 
                ->join('users','users.username','=','mahasiswa.users_username')
                ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
                ->update([    
                    'namamahasiswa'=>$namalengkap          
            	]);   

            	 $message=["Success","Berhasil Mengubah Profil Pengguna"];
            }
            else
            {
            	if($password_lama == $request->get('password'))
            	{
            		if($password_baru == $password_re_baru)
            		{
            			$encrypted = Crypt::encryptString($password_baru);

            			$mahasiswa = DB::table('mahasiswa') 
			                ->join('users','users.username','=','mahasiswa.users_username')
			                ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
			                ->update([                  
			                    'password' =>$encrypted
			            ]);    

			            $message=["Success","Berhasil Mengubah Password Pengguna"];
            		}
            		else
            		{
            			 $message=["Error","Harap Periksa Ulang Password Baru dan Konfirmasi Password yang di Inputkan"];
            		}
            	}
            	else
            	{
            		$message=["Error","Password Lama Yang Anda Masukan Salah"];
            	}
            }

	        return redirect('mahasiswa/profil/profilmahasiswa')->with([ $message[0] => $message[1]]);
    	}
    	
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("mahasiswa/profil/profilmahasiswa")->with(['Error' => 'Gagal Mengubah Data Pengguna <br> Pesan Kesalahan: '.$message[0]]);
        }

    }
}
