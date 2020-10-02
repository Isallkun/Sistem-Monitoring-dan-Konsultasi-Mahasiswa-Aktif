<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;

use DB;
use Session;
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
            ->select('*')
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
                'id_role' => 'required',
                'username' => 'required',
                'password'=>'required|max:10'
            ]);

            //Untuk proses enkripsi password
            $encrypted = Crypt::encryptString($password);
            
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
                'users_username'=>$username, 
                'jurusan_kodejurusan'=>$kode_jurusan
                   
            ]);

            return redirect('admin/master/dosen')->with(['Success' => 'Berhasil Menambahkan Data']);
        }
       
        catch(QueryException $e)
        {
            return redirect('admin/master/dosen/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database']);
        }
    }

    public function ubahdosen($id)
    {  
        if(Session::get('admin') != null)
        {
            $jurusan = DB::table('jurusan')
                    ->select('*')
                    ->get();

            $role = DB::table('role')
                    ->select('*')
                    ->get();

            $datadosen = DB::table('dosen')
                    ->select('*')
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
                'status' => 'required',
                'kode_jurusan' => 'required',
                'id_role' => 'required',
                'password'=>'required|max:8'
            ]);

            $encrypted = Crypt::encryptString($request->get('password'));

            $pengguna = DB::table('users') 
                    ->where('username',$request->get('username'))
                    ->update([
                        'password' => $encrypted,
                        'role_idrole' => $request->get('id_role')
                    ]);

            $dosen = DB::table('dosen') 
                    ->where('npkdosen',$request->get('npk_dosen'))
                    ->update([
                        'namadosen' => $request->get('nama_dosen'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'email' => $request->get('email'),
                        'telepon' => $request->get('telepon'),
                        'status' => $request->get('status'),
                        'jurusan_kodejurusan' => $request->get('kode_jurusan')
                    ]);

            return redirect('admin/master/dosen')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_dosen')." - ".$request->get('npk_dosen')]);
        }
        catch(QueryException $e)
        {
            return redirect("admin/master/dosen/ubah/{$request->get('npk_dosen')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_dosen')." - ".$request->get('npk_dosen')]);
        }
    }

    public function hapusdosen (Request $request,$id)
    {
        try
        {
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
            return redirect("admin/master/dosen")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('username')." - ".$id]);
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
                ->select('*')
                ->where('npkdosen',$keyword )
                ->paginate(10);
        }
        else if ($jenis_pencarian == "namadosen")
        {
            $dosen = DB::table('dosen')
                ->select('*')
                ->where('namadosen',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian =="jeniskelamin")
        {
             $dosen = DB::table('dosen')
                ->select('*')
                ->where('jeniskelamin',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian =="email")
        {
             $dosen = DB::table('dosen')
                ->select('*')
                ->where('email',$keyword )
                ->paginate(10);
        }
        else if ($jenis_pencarian=="telepon")
        {
            $dosen = DB::table('dosen')
                ->select('*')
                ->where('telepon',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian=="status")
        {
            $dosen = DB::table('dosen')
                ->select('*')
                ->where('status',$keyword )
                ->paginate(10);

        }
        else if ($jenis_pencarian=="username")
        {
            $dosen = DB::table('dosen')
                ->select('*')
                ->where('users_username',$keyword )
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
                ->where('npkdosen', 'LIKE', "%{$query}%")
                ->get();
            
                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->npkdosen.'</a></li>';
                }
            }      
            else if( $pencarian== 'namadosen')
            {
                $datadosen = DB::table('dosen')
                ->where('namadosen', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->namadosen.'</a></li>';
                }
            }    
            else if( $pencarian== 'jeniskelamin')
            {
                $datadosen = DB::table('dosen')
                ->where('jeniskelamin', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->jeniskelamin.'</a></li>';
                }
            }   
            else if( $pencarian== 'email')
            {
                $datadosen = DB::table('dosen')
                ->where('email', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->email.'</a></li>';
                }
            }    
            else if( $pencarian== 'telepon')
            {
                $datadosen = DB::table('dosen')
                ->where('telepon', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->telepon.'</a></li>';
                }
            }  
            else if( $pencarian== 'status')
            {
                $datadosen = DB::table('dosen')
                ->where('status', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->status.'</a></li>';
                }
            }    
            else if($pencarian== 'username')
            {
                $datadosen = DB::table('dosen')
                ->where('users_username', 'LIKE', "%{$query}%")
                ->get();

                foreach($datadosen as $row)
                {
                    $output .= '<li><a href="#">'.$row->users_username.'</a></li>';
                }
            }    
           
            $output .= '</ul>';
            echo $output;
        }
    }
    
}
