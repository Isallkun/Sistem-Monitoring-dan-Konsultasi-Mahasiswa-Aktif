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
            ->select('*')
            ->paginate(10);

            $dosen = DB::table('mahasiswa')
            ->select('*')
            ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->get();

            return view('master_mahasiswa.daftarmahasiswa_admin', compact('mahasiswa', 'dosen'));
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

//            Form Validasi Input User
            $this->validate($request,[
                'nrp_mahasiswa' =>'required|numeric|min:9',
                'nama_mahasiswa' =>'required',
                'jenis_kelamin'=> 'required',
                'tanggal_lahir'=>'required',
                'tempat_lahir'=>'required',
                'email'=>'required|email',
                'telepon' => 'required|numeric|min:12',
                'angkatan' => 'required|numeric|min:4',
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
