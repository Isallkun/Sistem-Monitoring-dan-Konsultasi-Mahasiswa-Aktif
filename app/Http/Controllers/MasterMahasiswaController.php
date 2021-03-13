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
            ->select('mahasiswa.*', 'dosen.npkdosen','dosen.namadosen','tahun_akademik.tahun','jurusan.namajurusan',
                     DB::raw("gamifikasi.aspek_durasi_konsultasi/5 AS avg_aspek1"),
                     DB::raw("gamifikasi.aspek_manfaat_konsultasi/5 AS avg_aspek2"),
                     DB::raw("gamifikasi.aspek_sifat_konsultasi/5 AS avg_aspek3"),
                     DB::raw("gamifikasi.aspek_interaksi/5 AS avg_aspek4"),
                     DB::raw("gamifikasi.aspek_pencapaian/5 AS avg_aspek5"),
                    'gamifikasi.*')

            ->join('dosen', 'dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->join('jurusan', 'jurusan.idjurusan', '=', 'mahasiswa.jurusan_idjurusan')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->get();

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
                'tanggal_lahir'=>'required|before:today',
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
                'aspek_durasi_konsultasi' =>'0',
                'aspek_manfaat_konsultasi' =>'0',
                'aspek_sifat_konsultasi' =>'0',
                'aspek_interaksi' =>'0',
                'aspek_pencapaian' =>'0',
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
            $message= explode("in C:",$e);

            return redirect('admin/master/mahasiswa/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
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
                'tanggal_lahir'=>'required|before:today',
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
            $message= explode("in C:",$e);

            return redirect("admin/master/mahasiswa/ubah/{$request->get('nrp_mahasiswa')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_mahasiswa')." - ".$request->get('nrp_mahasiswa')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }

    public function hapusmahasiswa (Request $request,$id)
    {
        try
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->where('konsultasi_dosenwali.mahasiswa_nrpmahasiswa',$id)
            ->count();

            $hukuman = DB::table('hukuman')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('hukuman.mahasiswa_nrpmahasiswa',$id)
            ->count();

            if($konsultasi == 0 && $hukuman == 0)
            {
                $datamahasiswa = DB::table('mahasiswa')
                        ->where('mahasiswa.nrpmahasiswa', $id)
                        ->get();
                //Hapus File gambar 
                File::delete('data_pengguna/'.$datamahasiswa[0]->profil);

                $mahasiswa = DB::table('mahasiswa')
                    ->where('nrpmahasiswa',$id)
                    ->delete();

                $user = DB::table('users')
                    ->where('username',$request->get('username'))
                    ->delete();

                $gamifikasi = DB::table('gamifikasi')
                    ->where('idgamifikasi',$request->get('idgamifikasi'))
                    ->delete();

                return redirect('admin/master/mahasiswa')->with(['Success' => 'Berhasil Menghapus Data '." ".$request->get('username')." - ".$id]);
            }
            else
            {
                 return redirect('admin/master/mahasiswa')->with(['Failed' => "Tidak Dapat Melakukan Hapus Data <br> Pesan Kesalahan: Mahasiswa masih memiliki data yang terhubung dengan tabel lain"]);
            }
           
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/mahasiswa")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('username')." - ".$id]."<br> Pesan Kesalahan: ".$message[0]);
        }
    }

   
}
