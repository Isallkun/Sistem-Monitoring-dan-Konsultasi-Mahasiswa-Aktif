<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use App\Matakuliah;

class MasterMatakuliahController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarmatakuliah()
    {
        if(Session::get('admin') != null)
        {
            $matakuliah = DB::table('matakuliah')
                        ->select('*')
                        ->paginate(10);
            
            return view('master_matakuliah.daftarmatakuliah_admin', compact('matakuliah'));
        }
        else
        {
            return redirect("/");
        }
    }
    
    public function tambahmatakuliah()
    {
        if(Session::get('admin') != null)
        {
            return view('master_matakuliah.tambahmatakuliah_admin');
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahmatakuliah_proses (Request $request)
    {
        try
        {
            $kode_matakuliah = $request->get('kode_matakuliah');
            $nama_matakuliah = $request->get('nama_matakuliah');
            $jenis = $request->get('jenis');
            $totalsks = $request->get('totalsks');
            
            if($request->get('keterangan') == "")
            {
                $keterangan = "-";
            }
            else
            {
                $keterangan = $request->get('keterangan');   
            }
            

            $this->validate($request,[
                'kode_matakuliah' =>'required|min:8',
                'nama_matakuliah' =>'required',
                'jenis' => 'required',
                'totalsks' =>'required|numeric|max:10',
            ]);

            $tambahdata_matakuliah= Matakuliah::insert([
                'kodematakuliah'=> $kode_matakuliah,
                'namamatakuliah'=> $nama_matakuliah,
                'jenis'=> $jenis,
                'totalsks'=>$totalsks,
                'keterangan'=> $keterangan
            ]);

            return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
            return redirect('admin/master/matakuliah/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database']);
        }
    }

    public function ubahmatakuliah ($id)
    {
        if(Session::get('admin') != null)
        {
            $datamatakuliah = DB::table('matakuliah')
                        ->select('*')
                        ->where('kodematakuliah', $id)
                        ->get();

            return view('master_matakuliah.ubahmatakuliah_admin', compact('datamatakuliah'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahmatakuliah_proses(Request $request)
    {
        try
        {
            $nama_matakuliah = $request->get('nama_matakuliah');
            $jenis = $request->get('jenis');
            $totalsks = $request->get('totalsks');
            
            if($request->get('keterangan') == "")
            {
                $keterangan = "-";
            }
            else
            {
                $keterangan = $request->get('keterangan');   
            }

            $this->validate($request,[
                'nama_matakuliah' =>'required',
                'jenis' => 'required',
                'totalsks' =>'required|numeric|max:10',
            ]);

            $matakuliah = DB::table('matakuliah')
                    ->where('kodematakuliah',$request->get('kode_matakuliah'))
                    ->update([
                        'namamatakuliah' => $nama_matakuliah,
                        'jenis' => $jenis,
                        'totalsks'=> $totalsks,
                        'keterangan'=> $keterangan
                    ]);

            return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_matakuliah')." - ".$request->get('kode_matakuliah')]);
        }
        catch(QueryException $e)
        {
            return redirect("admin/master/matakuliah/ubah/{$request->get('kode_matakuliah')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_matakuliah')." - ".$request->get('kode_matakuliah')]);
        }
    }

    public function hapusmatakuliah (Request $request,$id)
    {
        try
        {
            $matakuliah = DB::table('matakuliah')
                ->where('kodematakuliah',$id)
                ->delete();

            return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menghapus Data '." ".$request->get('nama_matakuliah')." - ".$id]);
        }

        catch(QueryException $e)
        {
            return redirect("admin/master/matakuliah")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('nama_matakuliah')." - ".$id]);
        }
    }


}
