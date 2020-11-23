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
                        ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                        ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                        ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                        ->get();
            
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
            $semester = DB::table('semester')
                        ->get();
            
            $tahun_akademik = DB::table('tahun_akademik')
                              ->get();

            return view('master_matakuliah.tambahmatakuliah_admin', compact('semester','tahun_akademik'));
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
            $kodematakuliah = $request->get('kodematakuliah');
            $namamatakuliah = $request->get('namamatakuliah');
            $totalsks = $request->get('totalsks');
            $totalpertemuan = $request->get('totalpertemuan');
            $nisbi =$request->get('nisbi');
            $semester=$request->get('semester');
            $tahunakademik=$request->get('tahunakademik');

            $this->validate($request,[
                'kodematakuliah' =>'required|max:8',
                'namamatakuliah' =>'required',
                'totalsks' =>'required|numeric|min:1|max:10',
                'totalpertemuan' =>'required|numeric|min:1|max:10',
                'nisbi' =>'required',
                'semester'=>'required',
                'tahunakademik'=>'required'
            ]);


            $tambahdata_matakuliah= Matakuliah::insert([
                'kodematakuliah'=> $kodematakuliah,
                'namamatakuliah'=> $namamatakuliah,
                'sks'=>$totalsks,
                'totalpertemuan'=>$totalpertemuan,
                'nisbimin'=>$nisbi,
                'thnakademik_idthnakademik'=>$tahunakademik,
                'semester_idsemester'=>$semester
            ]);

            return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('admin/master/matakuliah/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }


}
