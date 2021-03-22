<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
 
use DB;
use Session;

use App\Jenis_Hukuman;

class MasterJenisHukumanController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarjenishukuman()
    {
        if(Session::get('admin') != null)
        {
            $jenishukuman= DB::table('jenis_hukuman')
            ->select("*")
            ->get();

            return view('master_jenishukuman.daftarjenishukuman_admin', compact('jenishukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahjenishukuman_proses (Request $request)
    {
        try
        {
            $this->validate($request,[
                'namahukuman' =>'required|max:50',
                'kategori' =>'required'
            ]);


            $namahukuman= $request->get('namahukuman');
            $kategori = $request->get('kategori');

            $tambahdata_jenishukuman= Jenis_Hukuman::insert([
                'namahukuman'=> $namahukuman,
                'kategori'=>$kategori
            ]);

            return redirect('admin/master/jenishukuman')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('admin/master/jenishukuman/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

    public function ubahjenishukuman($id)
    {  
        if(Session::get('admin') != null)
        {
           
			$jenishukuman = DB::table('jenis_hukuman')
			->select('*')
			->where('jenis_hukuman.idjenishukuman', $id)
			->get();

            return view('master_jenishukuman.ubahjenishukuman_admin', compact('jenishukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahjenishukuman_proses(Request $request)
    {  
    	try
    	{
			$this->validate($request,[
			'namahukuman' =>'required|max:50',
			'kategori' =>'required'
			]);


			$update_jenishukuman = DB::table('jenis_hukuman') 
			->where('idjenishukuman',$request->get('idjenishukuman'))
			->update([
			    'namahukuman' => $request->get('namahukuman'),
			    'kategori' => $request->get('kategori') 
			]);

            return redirect('admin/master/jenishukuman')->with(['Success' => 'Berhasil Mengubah Data Jenis Hukuman (ID) '.$request->get('idjenishukuman')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("admin/master/jenishukuman/ubah/$id")->with(['Error' => 'Gagal Mengubah Data Jenis Hukuman (ID) '.$request->get('idjenishukuman')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }


    public function hapusjenishukuman ($id)
    {
        try
        {
			$hapus_jenishukuman = DB::table('jenis_hukuman')
			->where('idjenishukuman',$id)
			->delete();

            return redirect('admin/master/jenishukuman')->with(['Success' => 'Berhasil Menghapus Data Jenis Hukuman (ID) '.$id]);
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/jenishukuman")->with(['Error' => 'Gagal Menghapus Data '.$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }
}

