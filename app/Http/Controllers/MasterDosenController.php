<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;

use DB;
use Session;
use File;

use App\User;
use App\Dosen;


class MasterDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */  
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftardosen()
    {
        if(Session::get('admin') != null)
        {
            $dosen = DB::table('dosen')
            ->select('dosen.*','jurusan.namajurusan')
            ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
            ->get();

            return view('master_dosen.daftardosen_admin', compact('dosen'));
        }
        else
        {
            return redirect("/");
        }
       
    }

    public function tambahdosen()
    {
        if(Session::get('admin') != null)
        {
            $jurusan = DB::table('jurusan')
                    ->select('*')
                    ->get();


            $role = DB::table('role')
                    ->select('*')
                    ->get();

            return view('master_dosen.tambahdosen_admin', compact('jurusan', 'role'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahdosen_proses(Request $request)
    {
        try
        {
            $npk_dosen = $request->get('npk_dosen');
            $nama_dosen = $request->get('nama_dosen');
            $jenis_kelamin = $request->get('jenis_kelamin');
            $email = $request->get('email');
            $telepon = $request->get('telepon');
            $status = $request->get('status');
            $kode_jurusan = $request->get('kode_jurusan');
            $profil = $request->file('profil_pengguna');
            $id_role = $request->get('id_role');
            $username = $request->get('username');
            $password = $request->get('password');

       
            // Form Validasi Input User
            $this->validate($request,[
                'npk_dosen' => 'required|numeric|min:6',
                'nama_dosen' => 'required',
                'jenis_kelamin' => 'required',
                'email' => 'required|email',
                'telepon' => 'required|numeric|min:12',
                'status' => 'required',
                'kode_jurusan' => 'required',
                'profil_pengguna' =>'required|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'username' => 'required',
                'password'=>'required|max:10'
            ]);

            //Untuk proses enkripsi password
            $encrypted = Crypt::encryptString($password);


            // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'data_pengguna';
            // ubah nama file gambar sesuai format yang diinginkan
            $file_name = time()."_".$npk_dosen.".".$profil->getClientOriginalExtension();


            $tambahdata_user= User::insert([
               'username'=>$username, 
               'password'=>$encrypted,
               'role_idrole'=>$id_role
            ]);

            $tambahdata_dosen= Dosen::insert([
                'npkdosen'=>$npk_dosen,
                'namadosen'=>$nama_dosen,
                'jeniskelamin'=>$jenis_kelamin,
                'email'=>$email,
                'telepon'=>$telepon,
                'status'=>$status,
                'profil'=>$file_name,
                'users_username'=>$username, 
                'jurusan_idjurusan'=>$kode_jurusan
            ]);
            
            // simpan upload gambar pada folder public
            $profil->move($tujuan_upload,$file_name);
            
            return redirect('admin/master/dosen')->with(['Success' => 'Berhasil Menambahkan Data']);
        }
       
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('admin/master/dosen/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database. <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

    public function ubahdosen($id)
    {  
        if(Session::get('admin') != null)
        {
            $jurusan = DB::table('jurusan')
                    ->select('*')
                    ->get();

            
            $datadosen = DB::table('dosen')
                    ->select('dosen.*','users.*')
                    ->join('users', 'users.username', '=', 'dosen.users_username')
                    ->where('dosen.npkdosen', $id)
                    ->get();

            $decrypted = Crypt::decryptString($datadosen[0]->password);   

            return view('master_dosen.ubahdosen_admin', compact('jurusan', 'role', 'datadosen', 'decrypted'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahdosen_proses(Request $request)
    {
        try
        {
            // Validasi Form
            $this->validate($request,[
                'npk_dosen' => 'required|numeric|min:6',
                'nama_dosen' => 'required',
                'email' => 'required|email',
                'telepon' => 'required|numeric|min:12',
                'password'=>'required|max:10'
            ]);

            $encrypted = Crypt::encryptString($request->get('password'));

            $pengguna = DB::table('users') 
                    ->where('username',$request->get('username'))
                    ->update([
                        'password' => $encrypted,
                        'role_idrole' => $request->get('id_role')
                    ]);

            $profil_pengguna = $request->file('profil_pengguna');
            if($profil_pengguna == "")
            {
                $dosen = DB::table('dosen') 
                    ->where('npkdosen',$request->get('npk_dosen'))
                    ->update([
                        'namadosen' => $request->get('nama_dosen'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'email' => $request->get('email'),
                        'telepon' => $request->get('telepon'),
                        'status' => $request->get('status'),
                        'jurusan_idjurusan' => $request->get('kode_jurusan')
                ]);    
            }

            else
            {
                $datadosen = DB::table('dosen')
                            ->join('users', 'users.username', '=', 'dosen.users_username')
                            ->where('dosen.npkdosen', $request->get('npk_dosen'))
                            ->get();
                //Hapus File gambar sebelumnya
                File::delete('data_pengguna/'.$datadosen[0]->profil);

                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = 'data_pengguna';
                // ubah nama file gambar sesuai format yang diinginkan
                $file_name = time()."_".$request->get('npk_dosen').".".$profil_pengguna->getClientOriginalExtension();
                
                $dosen = DB::table('dosen') 
                    ->where('npkdosen',$request->get('npk_dosen'))
                    ->update([
                        'namadosen' => $request->get('nama_dosen'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'email' => $request->get('email'),
                        'telepon' => $request->get('telepon'),
                        'status' => $request->get('status'),
                        'profil' =>$file_name,
                        'jurusan_idjurusan' => $request->get('kode_jurusan')
                ]);    

                // simpan upload gambar pada folder public
                $profil_pengguna->move($tujuan_upload,$file_name);
            }
            

            return redirect('admin/master/dosen')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_dosen')." - ".$request->get('npk_dosen')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/dosen/ubah/{$request->get('npk_dosen')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_dosen')." - ".$request->get('npk_dosen')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }

    public function hapusdosen (Request $request,$id)
    {
        try
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$id)
            ->count();

            $hukuman = DB::table('hukuman')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->where('hukuman.dosen_npkdosen',$id)
            ->count();


            if($konsultasi == 0 && $hukuman == 0)
            {
                $datadosen = DB::table('dosen')
                        ->join('users', 'users.username', '=', 'dosen.users_username')
                        ->where('dosen.npkdosen', $id)
                        ->get();
                //Hapus File gambar 
                File::delete('data_pengguna/'.$datadosen[0]->profil);


                $dosen = DB::table('dosen')
                ->where('npkdosen',$id)
                ->delete();

                $user = DB::table('users')
                ->where('username',$request->get('username'))
                ->delete();

                return redirect('admin/master/dosen')->with(['Success' => 'Berhasil Menghapus Data '." ".$request->get('username')." - ".$id]);
            }
            else
            {
                return redirect('admin/master/dosen')->with(['Failed' => "Tidak Dapat Melakukan Hapus Data <br> Pesan Kesalahan: Dosen masih memiliki data yang terhubung dengan tabel lain"]);
            }

            // dd($konsultasi);
            
            
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/dosen")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('username')." - ".$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
     
    }
}
