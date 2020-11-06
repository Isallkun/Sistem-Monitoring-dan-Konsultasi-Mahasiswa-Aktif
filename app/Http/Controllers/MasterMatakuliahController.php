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
                        ->paginate(10);
            
            return view('master_matakuliah.daftarmatakuliah_admin', compact('matakuliah'));
        }
        else
        {
            return redirect("/");
        }
    }

    //START TAMBAH MATAKULIAH
        // public function tambahmatakuliah()
        // {
        //     if(Session::get('admin') != null)
        //     {
        //         return view('master_matakuliah.tambahmatakuliah_admin');
        //     }
        //     else
        //     {
        //         return redirect("/");
        //     }
        // }
    //END START TAMBAH MATAKULIAH

    //START TAMBAH MATAKULIAH (PROSES)
        // public function tambahmatakuliah_proses (Request $request)
        // {
        //     try
        //     {
        //         $kode_matakuliah = $request->get('kode_matakuliah');
        //         $nama_matakuliah = $request->get('nama_matakuliah');
        //         $jenis = $request->get('jenis');
        //         $totalsks = $request->get('totalsks');
                
        //         if($request->get('keterangan') == "")
        //         {
        //             $keterangan = "-";
        //         }
        //         else
        //         {
        //             $keterangan = $request->get('keterangan');   
        //         }
                

        //         $this->validate($request,[
        //             'kode_matakuliah' =>'required|min:8',
        //             'nama_matakuliah' =>'required',
        //             'jenis' => 'required',
        //             'totalsks' =>'required|numeric|max:10',
        //         ]);

        //         $tambahdata_matakuliah= Matakuliah::insert([
        //             'kodematakuliah'=> $kode_matakuliah,
        //             'namamatakuliah'=> $nama_matakuliah,
        //             'jenis'=> $jenis,
        //             'totalsks'=>$totalsks,
        //             'keterangan'=> $keterangan
        //         ]);

        //         return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menambahkan Data']);

        //     }
        //     catch (QueryException $e)
        //     {
        //         return redirect('admin/master/matakuliah/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database']);
        //     }
        // }
    //END START TAMBAH MATAKULIAH (PROSES)

    //START UBAH MATAKULIAH
        // public function ubahmatakuliah ($id)
        // {
        //     if(Session::get('admin') != null)
        //     {
        //         $datamatakuliah = DB::table('matakuliah')
        //                     ->select('*')
        //                     ->where('kodematakuliah', $id)
        //                     ->get();

        //         return view('master_matakuliah.ubahmatakuliah_admin', compact('datamatakuliah'));
        //     }
        //     else
        //     {
        //         return redirect("/");
        //     }
        // }
    //END START UBAH MATAKULIAH

    //START UBAH MATAKULIAH (PROSES)
        // public function ubahmatakuliah_proses(Request $request)
        // {
        //     try
        //     {
        //         $nama_matakuliah = $request->get('nama_matakuliah');
        //         $jenis = $request->get('jenis');
        //         $totalsks = $request->get('totalsks');
                
        //         if($request->get('keterangan') == "")
        //         {
        //             $keterangan = "-";
        //         }
        //         else
        //         {
        //             $keterangan = $request->get('keterangan');   
        //         }

        //         $this->validate($request,[
        //             'nama_matakuliah' =>'required',
        //             'jenis' => 'required',
        //             'totalsks' =>'required|numeric|max:10',
        //         ]);

        //         $matakuliah = DB::table('matakuliah')
        //                 ->where('kodematakuliah',$request->get('kode_matakuliah'))
        //                 ->update([
        //                     'namamatakuliah' => $nama_matakuliah,
        //                     'jenis' => $jenis,
        //                     'totalsks'=> $totalsks,
        //                     'keterangan'=> $keterangan
        //                 ]);

        //         return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Mengubah Data '.$request->get('nama_matakuliah')." - ".$request->get('kode_matakuliah')]);
        //     }
        //     catch(QueryException $e)
        //     {
        //         return redirect("admin/master/matakuliah/ubah/{$request->get('kode_matakuliah')}")->with(['Error' => 'Gagal Mengubah Data '.$request->get('nama_matakuliah')." - ".$request->get('kode_matakuliah')]);
        //     }
        // }
    //END START UBAH MATAKULIAH (PROSES)

    //START HAPUS MATAKULIAH
        // public function hapusmatakuliah (Request $request,$id)
        // {
        //     try
        //     {
        //         $matakuliah = DB::table('matakuliah')
        //             ->where('kodematakuliah',$id)
        //             ->delete();

        //         return redirect('admin/master/matakuliah')->with(['Success' => 'Berhasil Menghapus Data '." ".$request->get('nama_matakuliah')." - ".$id]);
        //     }

        //     catch(QueryException $e)
        //     {
        //         return redirect("admin/master/matakuliah")->with(['Error' => 'Gagal Menghapus Data '." ".$request->get('nama_matakuliah')." - ".$id]);
        //     }
        // }
    //END START HAPUS MATAKULIAH


    public function carimatakuliah (Request $request)
    {
        $jenis_pencarian = $request->get('pencarian');
        $keyword = $request->get('keyword');

         $this->validate($request,[
            'pencarian' => 'required',
            'keyword' =>'required'
        ]);

        if($jenis_pencarian == "kodematakuliah")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('kodematakuliah',$keyword)
                ->paginate(10);
        }
        else if($jenis_pencarian == "namamatakuliah")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('namamatakuliah',$keyword)
                ->paginate(10);
        }
        else if($jenis_pencarian == "totalsks")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('sks',$keyword)
                ->paginate(10);
        }
        else if($jenis_pencarian == "totalpertemuan")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('totalpertemuan',$keyword)
                ->paginate(10);
        }
        else if($jenis_pencarian == "nisbi")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('nisbimin',$keyword)
                ->paginate(10);
        }
        else if($jenis_pencarian == "tahunakademik")
        {
            $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('tahun_akademik.tahun',$keyword)
                ->paginate(10);

            // dd($explode_keyword[0]);
        }
        else
        {
            return redirect("admin/master/matakuliah")->with(['Error' => 'Gagal melakukan proses pencarian']);
        }

        return view('master_matakuliah.daftarmatakuliah_admin', compact('matakuliah'));
    }

    function fetch(Request $request)
    {
        $query = $request->get('query');
        $pencarian =$request->get('jenis');

        if($request->get('query') && $request->get('jenis'))
        {           
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';    

            if($pencarian == 'kodematakuliah')
            {
                $matakuliah = DB::table('matakuliah')
                ->select('*')
                ->where('kodematakuliah','LIKE', "%{$query}%")
                ->get();
                
                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->kodematakuliah.'</a></li>';
                }
            }      
            else if( $pencarian== 'namamatakuliah')
            {
                $matakuliah = DB::table('matakuliah')
                ->select('*')
                ->where('namamatakuliah','LIKE', "%{$query}%")
                ->get();
                
                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->namamatakuliah.'</a></li>';
                }
            }    
            else if( $pencarian== 'totalsks')
            {
                $matakuliah = DB::table('matakuliah')
                ->where('sks', 'LIKE', "%{$query}%")
                ->get();
        
                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->sks.'</a></li>';
                }
            }   
            else if( $pencarian== 'totalpertemuan')
            {
                $matakuliah = DB::table('matakuliah')
                ->where('totalpertemuan', 'LIKE', "%{$query}%")
                ->get();
        
                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->totalpertemuan.'</a></li>';
                }
            }   
            else if( $pencarian== 'nisbi')
            {
                $matakuliah = DB::table('matakuliah')
                ->where('nisbimin', 'LIKE', "%{$query}%")
                ->get();
        
                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->nisbimin.'</a></li>';
                }
            }   
            else if( $pencarian== 'tahunakademik')
            {
                $matakuliah = DB::table('matakuliah')
                ->select('matakuliah.*', 'semester.semester', 'tahun_akademik.tahun')
                ->join('semester', 'semester.idsemester','=', 'matakuliah.semester_idsemester')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('semester.semester','LIKE',"%{$query}%" )
                ->orwhere('tahun_akademik.tahun','LIKE',"%{$query}%" )
                ->get();

                foreach($matakuliah as $row)
                {
                    $output .= '<li><a href="#">'.$row->tahun.'</a></li>';
                }
            }   
            
           
            $output .= '</ul>';
            echo $output;
        }
    }


}
