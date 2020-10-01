<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;

use DB;
use Session;
use App\User;
use App\Mahasiswa;


class MasterMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daftarmahasiswa()
    {
        if(Session::get('admin') != null)
        {
            $mahasiswa = DB::table('mahasiswa')
            ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
            ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->paginate(10);
            
            return view('master_mahasiswa.daftarmahasiswa_admin', compact('mahasiswa'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahmahasiswa()
    {
        if(Session::get('admin') != null)
        {
            $dosen = DB::table('dosen')
                    ->select('*')
                    ->get();

            $jurusan = DB::table('jurusan')
                    ->select('*')
                    ->get();

            $role = DB::table('role')
                    ->select('*')
                    ->get();

            return view('master_mahasiswa.tambahmahasiswa_admin',compact('dosen','jurusan', 'role'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahmahasiswa_proses(Request $request)
    {
        try
        {
            $nrp_mahasiswa = $request->get('nrp_mahasiswa');
            $nama_mahasiswa = $request->get('nama_mahasiswa');
            $jenis_kelamin = $request->get('jenis_kelamin');
            $tanggal_lahir = $request->get('tanggal_lahir');
            $tempat_lahir = $request->get('tempat_lahir');
            $email=$request->get('email');
            $telepon=$request->get('telepon');
            $angkatan = $request->get('angkatan');
            $alamat = $request->get('alamat');
            $status = $request->get('status');
            $npk_dosenwali = $request->get('npk_dosenwali');
            $kode_jurusan = $request->get('kode_jurusan');
            $id_role = $request->get('id_role');
            $username = $request->get('username');
            $password = $request->get('password');

            //Untuk proses enkripsi password
            $encrypted = Crypt::encryptString($password);

            // Form Validasi Input User
            $this->validate($request,[
                'nrp_mahasiswa' =>'required|numeric|min:9',
                'nama_mahasiswa' =>'required',
                'jenis_kelamin'=> 'required',
                'tanggal_lahir'=>'required',
                'tempat_lahir'=>'required',
                'email'=>'required|email',
                'telepon' => 'required|numeric|min:12',
                'angkatan' => 'required|numeric|min:4',
                'alamat'=>'required',
                'status' => 'required',
                'npk_dosenwali' => 'required',
                'kode_jurusan' => 'required',
                'id_role' => 'required',
                'username' => 'required',
                'password'=>'required|max:10'
            ]);

            $tambahdata_user= User::insert([
               'username'=>$username, 
               'password'=>$encrypted,
               'role_idrole'=>$id_role
            ]);

            $tambahdata_mahasiswa= Mahasiswa::insert([
                'nrpmahasiswa'=>$nrp_mahasiswa,
                'namamahasiswa'=>$nama_mahasiswa,
                'jeniskelamin'=>$jenis_kelamin,
                'tanggallahir'=>$tanggal_lahir,
                'tempatlahir'=>$tempat_lahir,
                'email'=>$email,
                'telepon'=>$telepon,
                'angkatan'=>$angkatan,
                'alamat'=>$alamat,
                'status'=>$status,
                'rate'=>'0',
                'users_username'=>$username,
                'dosen_npkdosen'=>$npk_dosenwali,
                'jurusan_kodejurusan'=>$kode_jurusan
            ]);

            return redirect('admin/master/mahasiswa')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
             return redirect('admin/master/mahasiswa/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database']);
        }
    }

    public function ubahmahasiswa($id)
    {  
        if(Session::get('admin') != null)
        {
            $jurusan = DB::table('jurusan')
                    ->select('*')
                    ->get();

            $role = DB::table('role')
                    ->select('*')
                    ->get();

            $dosen = DB::table('dosen')
                    ->select('*')
                    ->get();

            $datamahasiswa = DB::table('mahasiswa')
                    ->select('*')
                    ->join('users', 'users.username', '=', 'mahasiswa.users_username')
                    ->where('mahasiswa.nrpmahasiswa', $id)
                    ->get();


            $decrypted = Crypt::decryptString($datamahasiswa[0]->password);   

            // dd($datamahasiswa);

            return view('master_mahasiswa.ubahmahasiswa_admin', compact('jurusan', 'role', 'dosen','datamahasiswa', 'decrypted'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahmahasiswa_proses(Request $request)
    {
        try
        {
            // Form Validasi Input User
            $this->validate($request,[
                'nama_mahasiswa' =>'required',
                'jenis_kelamin'=> 'required',
                'tanggal_lahir'=>'required',
                'tempat_lahir'=>'required',
                'email'=>'required|email',
                'telepon' => 'required|numeric|min:12',
                'angkatan' => 'required|numeric|min:4',
                'alamat' => 'required',
                'status' => 'required',
                'npk_dosenwali' => 'required',
                'kode_jurusan' => 'required',
                'id_role' => 'required',
                'password'=>'required|max:10'
            ]);

            $encrypted = Crypt::encryptString($request->get('password'));

            $pengguna = DB::table('users')
                    ->where('username',$request->get('username'))
                    ->update([
                        'password' => $encrypted,
                        'role_idrole' => $request->get('id_role')
                    ]);

            $mahasiswa = DB::table('mahasiswa') 
                    ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
                    ->update([
                        'namamahasiswa' => $request->get('nama_mahasiswa'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'tanggallahir'=> $request->get('tanggal_lahir'),
                        'tempatlahir' =>$request->get('tempat_lahir'),
                        'email'=>$request->get('email'),
                        'telepon'=>$request->get('telepon'),
                        'angkatan'=>$request->get('angkatan'),
                        'alamat'=>$request->get('alamat'),
                        'status'=>$request->get('status'),
                        'dosen_npkdosen'=>$request->get('npk_dosenwali'),
                        'jurusan_kodejurusan'=>$request->get('kode_jurusan')
                    ]);




            return redirect('admin/master/mahasiswa')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_mahasiswa')." - ".$request->get('nrp_mahasiswa')]);



        }
        catch(QueryException $e)
        {
            return redirect("admin/master/mahasiswa/ubah/{$request->get('nrp_mahasiswa')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_mahasiswa')." - ".$request->get('nrp_mahasiswa')]);
        }
    }

    public function hapusmahasiswa (Request $request,$id)
    {
        try
        {
            $dosen = DB::table('mahasiswa')
                ->where('nrpmahasiswa',$id)
                ->delete();

            $user = DB::table('users')
                ->where('username',$request->get('username'))
                ->delete();

            return redirect('admin/master/mahasiswa')->with(['Success' => 'Berhasil Menghapus Data '." ".$request->get('username')." - ".$id]);
        }

        catch(QueryException $e)
        {
            return redirect("admin/master/mahasiswa")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('username')." - ".$id]);
        }
    }

    public function carimahasiswa (Request $request)
    {
        $jenis_pencarian = $request->get('pencarian');
        $keyword = $request->get('keyword');

         $this->validate($request,[
            'pencarian' => 'required',
            'keyword' =>'required'
        ]);

        if($jenis_pencarian == "nrpmahasiswa")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('nrpmahasiswa',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "namamahasiswa")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('namamahasiswa',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "jeniskelamin")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('jeniskelamin',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "email")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('email',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "telepon")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('telepon',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "angkatan")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('angkatan',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "alamat")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('alamat',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "status")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('status',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "username")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('username',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "namadosen")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('dosen.namadosen',$keyword )
                ->paginate(10);
        }
        else
        {
           return redirect("admin/master/mahasiswa")->with(['Error' => 'Gagal melakukan proses pencarian']);

        }

        return view('master_mahasiswa.daftarmahasiswa_admin', compact('mahasiswa'));
    }
}
