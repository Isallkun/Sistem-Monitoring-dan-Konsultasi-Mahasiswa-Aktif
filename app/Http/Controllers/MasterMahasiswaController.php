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
use App\Mahasiswa;
use App\Gamifikasi;


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
            ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
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

            $tahun_akademik = DB::table('tahun_akademik')
                            -> select('*')
                            ->get();

            $role = DB::table('role')
                    ->select('*')
                    ->get();

            return view('master_mahasiswa.tambahmahasiswa_admin',compact('dosen','jurusan', 'tahun_akademik', 'role'));
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
            $tahun_akademik = $request->get('tahun_akademik');
            $alamat = $request->get('alamat');
            $status = $request->get('status');
            $npk_dosenwali = $request->get('npk_dosenwali');
            $id_jurusan = $request->get('kode_jurusan');
            $profil = $request->file('profil_pengguna');
            $id_role = $request->get('id_role');
            $username = $request->get('username');
            $password = $request->get('password');

            //Untuk proses enkripsi password
            $encrypted = Crypt::encryptString($password);


             // isi dengan nama folder tempat kemana file diupload
            $tujuan_upload = 'data_pengguna';
            // ubah nama file gambar sesuai format yang diinginkan
            $file_name = time()."_".$nrp_mahasiswa.".".$profil->getClientOriginalExtension();


            // Form Validasi Input User
            $this->validate($request,[
                'nrp_mahasiswa' =>'required|numeric|min:9',
                'nama_mahasiswa' =>'required',
                'jenis_kelamin'=> 'required',
                'tanggal_lahir'=>'required',
                'tempat_lahir'=>'required',
                'email'=>'required|email',
                'telepon' => 'required|numeric|min:12',
                'tahun_akademik' => 'required',
                'alamat'=>'required',
                'status' => 'required',
                'npk_dosenwali' => 'required',
                'kode_jurusan' => 'required',
                'profil_pengguna' =>'required|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'username' => 'required',
                'password'=>'required|max:10'
            ]);

            $tambahdata_user= User::insert([
               'username'=>$username, 
               'password'=>$encrypted,
               'role_idrole'=>$id_role
            ]);

            // Bronze - Silver - Gold
            $gamifikasi = Gamifikasi::insert([
                'poin' =>'0',
                'level' =>'Bronze'
            ]);
            $select_gamifikasi = DB::table('gamifikasi')
                                ->select('idgamifikasi')
                                ->orderby('idgamifikasi','desc')
                                ->limit(1)
                                ->get();

            $tambahdata_mahasiswa= Mahasiswa::insert([
                'nrpmahasiswa'=>$nrp_mahasiswa,
                'namamahasiswa'=>$nama_mahasiswa,
                'jeniskelamin'=>$jenis_kelamin,
                'tanggallahir'=>$tanggal_lahir,
                'tempatlahir'=>$tempat_lahir,
                'email'=>$email,
                'telepon'=>$telepon,
                'alamat'=>$alamat,
                'status'=>$status,
                'flag'=>'0',
                'profil'=>$file_name,
                'users_username'=>$username,
                'dosen_npkdosen'=>$npk_dosenwali,
                'jurusan_idjurusan'=>$id_jurusan,
                'gamifikasi_idgamifikasi'=> $select_gamifikasi[0]->idgamifikasi,
                'thnakademik_idthnakademik'=>$tahun_akademik
            ]);

            // simpan upload gambar pada folder public
            $profil->move($tujuan_upload,$file_name);

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

            $dosen = DB::table('dosen')
                    ->select('*')
                    ->get();

            $tahun_akademik = DB::table('tahun_akademik')
                            -> select('*')
                            ->get();

            $datamahasiswa = DB::table('mahasiswa')
                    ->select('mahasiswa.*','users.*')
                    ->join('users', 'users.username', '=', 'mahasiswa.users_username')
                    ->where('mahasiswa.nrpmahasiswa', $id)
                    ->get();


            $decrypted = Crypt::decryptString($datamahasiswa[0]->password);   

            return view('master_mahasiswa.ubahmahasiswa_admin', compact('jurusan','tahun_akademik' ,'dosen','datamahasiswa', 'decrypted'));
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
                'tanggal_lahir'=>'required',
                'tempat_lahir'=>'required',
                'email'=>'required|email',
                'telepon' => 'required|numeric|min:12',
                'alamat' => 'required',
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
            if($profil_pengguna =="")
            {
                $mahasiswa = DB::table('mahasiswa') 
                    ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
                    ->update([
                        'namamahasiswa' => $request->get('nama_mahasiswa'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'tanggallahir'=> $request->get('tanggal_lahir'),
                        'tempatlahir' =>$request->get('tempat_lahir'),
                        'email'=>$request->get('email'),
                        'telepon'=>$request->get('telepon'),
                        'alamat'=>$request->get('alamat'),
                        'status'=>$request->get('status'),
                        'dosen_npkdosen'=>$request->get('npk_dosenwali'),
                        'jurusan_idjurusan'=>$request->get('kode_jurusan'),
                        'thnakademik_idthnakademik'=>$request->get('tahun_akademik')
                    ]);
            }
            else
            {
                $datamahasiswa = DB::table('mahasiswa')
                            ->join('users', 'users.username', '=', 'mahasiswa.users_username')
                            ->where('mahasiswa.nrpmahasiswa', $request->get('nrp_mahasiswa'))
                            ->get();
                //Hapus File gambar sebelumnya
                File::delete('data_pengguna/'.$datamahasiswa[0]->profil);

                // isi dengan nama folder tempat kemana file diupload
                $tujuan_upload = 'data_pengguna';
                // ubah nama file gambar sesuai format yang diinginkan
                $file_name = time()."_".$request->get('nrp_mahasiswa').".".$profil_pengguna->getClientOriginalExtension();
                
                $mahasiswa = DB::table('mahasiswa') 
                    ->where('nrpmahasiswa',$request->get('nrp_mahasiswa'))
                    ->update([
                        'namamahasiswa' => $request->get('nama_mahasiswa'),
                        'jeniskelamin' => $request->get('jenis_kelamin'),
                        'tanggallahir'=> $request->get('tanggal_lahir'),
                        'tempatlahir' =>$request->get('tempat_lahir'),
                        'email'=>$request->get('email'),
                        'telepon'=>$request->get('telepon'),
                        'alamat'=>$request->get('alamat'),
                        'status'=>$request->get('status'),
                        'profil' =>$file_name,
                        'dosen_npkdosen'=>$request->get('npk_dosenwali'),
                        'jurusan_idjurusan'=>$request->get('kode_jurusan'),
                        'thnakademik_idthnakademik'=>$request->get('tahun_akademik')
                    ]);  

                // simpan upload gambar pada folder public
                $profil_pengguna->move($tujuan_upload,$file_name);
            }

            return redirect('admin/master/mahasiswa')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_mahasiswa')." - ".$request->get('nrp_mahasiswa')]);
        }
        catch(QueryException $e)
        {
            dd($e);
            return redirect("admin/master/mahasiswa/ubah/{$request->get('nrp_mahasiswa')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_mahasiswa')." - ".$request->get('nrp_mahasiswa')]);
        }
    }

    public function hapusmahasiswa (Request $request,$id)
    {
        try
        {
            $datamahasiswa = DB::table('mahasiswa')
                        ->join('users', 'users.username', '=', 'mahasiswa.users_username')
                        ->where('mahasiswa.nrpmahasiswa', $id)
                        ->get();
            //Hapus File gambar 
            File::delete('data_pengguna/'.$datamahasiswa[0]->profil);


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
                ->select('mahasiswa.*','dosen.npkdosen' ,'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.nrpmahasiswa',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "namamahasiswa")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*','dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.namamahasiswa',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "jeniskelamin")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*','dosen.npkdosen', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.jeniskelamin',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "email")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.email',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "telepon")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.npkdosen', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.telepon',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "tahunakademik")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*','dosen.npkdosen', 'dosen.namadosen','tahun_akademik.tahun')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'mahasiswa.thnakademik_idthnakademik')
                ->where('tahun_akademik.tahun',$keyword )
                ->paginate(10);
                // dd($mahasiswa);
        }
        else if($jenis_pencarian == "alamat")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.alamat',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "status")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*','dosen.npkdosen', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.status',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "username")
        {
            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*','dosen.npkdosen', 'dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('mahasiswa.username',$keyword )
                ->paginate(10);
        }
        else if($jenis_pencarian == "dosenwali")
        {
            $sub_keyword=explode('-',$keyword);

            $mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                ->where('dosen.npkdosen',$sub_keyword[0])
                ->paginate(10);
        }
        else
        {
           return redirect("admin/master/mahasiswa")->with(['Error' => 'Gagal melakukan proses pencarian']);

        }

        return view('master_mahasiswa.daftarmahasiswa_admin', compact('mahasiswa'));
    }

    function fetch(Request $request)
    {
        $query = $request->get('query');
        $pencarian =$request->get('jenis');

        if($request->get('query') && $request->get('jenis'))
        {           
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';    

            if($pencarian == 'nrpmahasiswa')
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('nrpmahasiswa', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();
            
                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->nrpmahasiswa.'</a></li>';
                }
            }   

            else if($pencarian == "namamahasiswa")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('namamahasiswa', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->namamahasiswa.'</a></li>';
                }
            }
            else if($pencarian == "jeniskelamin")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.jeniskelamin', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->jeniskelamin.'</a></li>';
                }
            }
            else if($pencarian == "email")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.email', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->email.'</a></li>';
                }
            }
            else if($pencarian == "telepon")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.telepon', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->telepon.'</a></li>';
                }
            }
            else if($pencarian == "tahunakademik")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen', 'tahun_akademik.tahun')
                                ->where('tahun_akademik.tahun', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'mahasiswa.thnakademik_idthnakademik')
                                ->get();
                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->tahun.'</a></li>';
                }
            }
            else if($pencarian == "alamat")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.alamat', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->alamat.'</a></li>';
                }
            }
            else if($pencarian == "status")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.status', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->status.'</a></li>';
                }
            }
            else if($pencarian == "username")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('mahasiswa.users_username', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->users_username.'</a></li>';
                }
            }
           
            else if($pencarian == "dosenwali")
            {
                $datamahasiswa = DB::table('mahasiswa')
                                ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen')
                                ->where('dosen.namadosen', 'LIKE', "%{$query}%")
                                ->orwhere('dosen.npkdosen', 'LIKE', "%{$query}%")
                                ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
                                ->get();

                foreach($datamahasiswa as $row)
                {
                    $output .= '<li><a href="#">'.$row->npkdosen." - ".$row->namadosen.'</a></li>';
                }
            }


           
           
            $output .= '</ul>';
            echo $output;
        }
    }
}
