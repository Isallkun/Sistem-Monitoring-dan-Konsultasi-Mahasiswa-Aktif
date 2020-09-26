<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

use DB;
use App\User;
use App\Dosen;


class MasterDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */  

    public function daftardosen()
    {
       $dosen = DB::table('dosen')
            ->select('*')
            ->paginate(10);

        return view('master_dosen.daftardosen_admin', compact('dosen'));
    }

    public function tambahdosen()
    {
        $jurusan = DB::table('jurusan')
                ->select('*')
                ->get();

        $role = DB::table('role')
                ->select('*')
                ->get();

        return view('master_dosen.tambahdosen_admin', compact('jurusan', 'role'));
    }

    public function tambahdosen_proses(Request $request)
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
        
        $tambahdata_user= User::insert([
           'username'=>$username, 
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
        
        return redirect('admin/master/dosen');

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
