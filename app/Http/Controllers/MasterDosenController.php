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

            // Validasi Form
            $this->validate($request,[
                'npk_dosen' => 'required|numeric|min:6',
                'nama_dosen' => 'required',
                'email' => 'required|email',
                'telepon' => 'required|numeric|min:12',
                'status' => 'required',
                'kode_jurusan' => 'required',
                'id_role' => 'required',
                'username' => 'required|min:5',
                'password'=>'required|max:8'
            ]);

            //Untuk proses enkripsi password
            $encrypted = Crypt::encryptString($password);
            
            $tambahdata_user= User::insert(['username'=>$username, 
               'password'=>$encrypted,
               'id_role'=>$id_role
            ]);

            $tambahdata_dosen= Dosen::insert([
                'npkdosen'=>$npk_dosen,
                'namadosen'=>$nama_dosen,
                'jeniskelamin'=>$jenis_kelamin,
                'email'=>$email,
                'telepon'=>$telepon,
                'status'=>$status,
                'kode_jurusan'=>$kode_jurusan,
                'users_username'=>$username    
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
                'username' => 'required|min:5',
                'password'=>'required|max:8'
            ]);

            $encrypted = Crypt::encryptString($request->get('password'));

             $pengguna = DB::table('users') 
                    ->where('username',$request->get('username'))
                    ->update([
                        'password' => $encrypted,
                        'id_role' => $request->get('id_role')
                    ]);

             $dosen = DB::table('dosen') 
                    ->where('npkdosen',$request->get('npk_dosen'))
                    ->update([
                        'namadosen' => $request->get('nama_dosen'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'email' => $request->get('email'),
                        'telepon' => $request->get('telepon'),
                        'status' => $request->get('status'),
                        'kode_jurusan' => $request->get('kode_jurusan')
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
        else if($jenis_pencarian=="kodejurusan")
        {
            $dosen = DB::table('dosen')
                ->select('*')
                ->where('kode_jurusan',$keyword )
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

        if($request->get('query'))
        {
            $query = $request->get('query');

            $data = DB::table('dosen')
                ->where('npkdosen', 'LIKE', "%{$query}%")
                ->get();
            
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            
            foreach($data as $row)
            {
                $output .= '
                <li><a href="#">'.$row->npkdosen.'</a></li>';
            }
            
            $output .= '</ul>';
            echo $output;
         }
    }
    
}
