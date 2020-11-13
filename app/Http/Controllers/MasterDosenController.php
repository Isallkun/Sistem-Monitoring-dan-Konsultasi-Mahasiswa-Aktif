<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
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
            ->paginate(10);

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

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/dosen")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('username')." - ".$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
     
    }

    public function caridosen (Request $request)
    {
        $jenis_pencarian = $request->get('pencarian');
        $keyword = $request->get('keyword');
        
        $this->validate($request,[
            'pencarian' => 'required',
            'keyword' =>'required'
        ]);
       

        if($jenis_pencarian == "npkdosen")
        {
            $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('npkdosen',$keyword )
                ->paginate(10);
        }
        else if ($jenis_pencarian == "namadosen")
        {
            $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('namadosen',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian =="email")
        {
             $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('email',$keyword )
                ->paginate(10);
        }
        else if ($jenis_pencarian=="telepon")
        {
            $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->paginate(10);
        }
        else if($jenis_pencarian=="jurusan")
        {
            $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('jurusan.namajurusan',$keyword )
                ->paginate(10);

        }
        else if ($jenis_pencarian=="username")
        {
            $dosen = DB::table('dosen')
                ->select('dosen.*','jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('dosen.users_username',$keyword )
                ->paginate(10);
        }
        else 
        {
           return redirect("admin/master/dosen")->with(['Error' => 'Gagal melakukan proses pencarian']);
        }

        return view('master_dosen.daftardosen_admin', compact('dosen'));
    }
    
    function fetch(Request $request)
    {
        $query = $request->get('query');
        $pencarian =$request->get('jenis');

        if($request->get('query') && $request->get('jenis'))
        {           
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';    

            if($pencarian == 'npkdosen')
            {
                $datadosen = DB::table('dosen')
                ->select('npkdosen')
                ->where('npkdosen', 'LIKE', "%{$query}%")
                ->groupBy('npkdosen')
                ->get();
            
                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->npkdosen.'</a></li>';
                }
            }      
            else if( $pencarian== 'namadosen')
            {
                $datadosen = DB::table('dosen')
                ->select('namadosen')
                ->where('namadosen', 'LIKE', "%{$query}%")
                ->groupBy('namadosen')
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->namadosen.'</a></li>';
                }
            }    
            else if( $pencarian== 'email')
            {
                $datadosen = DB::table('dosen')
                ->select('email')
                ->where('email', 'LIKE', "%{$query}%")
                ->groupBy('email')
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->email.'</a></li>';
                }
            }    
            else if( $pencarian== 'telepon')
            {
                $datadosen = DB::table('dosen')
                ->select('telepon')
                ->where('telepon', 'LIKE', "%{$query}%")
                ->groupBy('telepon')
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->telepon.'</a></li>';
                }
            }  
            else if( $pencarian== 'jurusan')
            {
                $datadosen = DB::table('dosen')
                ->select('jurusan.namajurusan')
                ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
                ->where('jurusan.namajurusan', 'LIKE', "%{$query}%")
                ->groupBy('jurusan.namajurusan')
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->namajurusan.'</a></li>';
                }
            }    
            else if($pencarian== 'username')
            {
                $datadosen = DB::table('dosen')
                ->select('users.username')
                ->join('users', 'users.username', '=', 'dosen.users_username')
                ->where('users.username', 'LIKE', "%{$query}%")
                ->groupBy('users.username')
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->username.'</a></li>';
                }
            }    
           
            $output .= '</ul>';
            echo $output;
        }
    }
    
}
