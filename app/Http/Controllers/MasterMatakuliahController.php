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

            $this->validate($request,[
                'kode_matakuliah' =>'required|min:8',
                'nama_matakuliah' =>'required'
            ]);

            $tambahdata_matakuliah= Matakuliah::insert([
                'kodematakuliah'=> $kode_matakuliah,
                'namamatakuliah'=> $nama_matakuliah
            ]);

            return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
            return redirect('admin/master/matakuliah/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database']);
        }
    }
}
