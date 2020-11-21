<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use Carbon\Carbon;
use App\Dosen;
use App\Mahasiswa;
use App\Topik_konsultasi;
use App\Konsultasi_dosenwali;

class MasterKonsultasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarkonsultasi()
    {
        if(Session::get('admin') != null)
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->paginate(10);
            
            return view('master_konsultasi.daftarkonsultasi_admin', compact('konsultasi'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahkonsultasi()
    {
        if(Session::get('admin') != null)
        {
            $dosen = DB::table('dosen')
                    ->select('*')
                    ->get();

            $mahasiswa = DB::table('mahasiswa')
                    ->select('*')
                    ->get();

            $semester = DB::table('semester')
                        -> select('*')
                        ->get();

            $tahun_akademik = DB::table('tahun_akademik')
                            -> select('*')
                            ->get();


            
            return view('master_konsultasi.tambahkonsultasi_admin', compact('dosen','mahasiswa','semester','tahun_akademik'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahkonsultasi_proses (Request $request)
    {
        try
        {
            $tanggalkonsultasi= Carbon::now()->format('Y/m/d');
            $topik_konsultasi = $request->get('topik_konsultasi');
            $permasalahan = $request->get('permasalahan');
            $solusi = $request->get('solusi');
            $konsultasi_selanjutnya = $request->get('konsultasi_selanjutnya');
            $dosen =$request->get('dosen');
            $mahasiswa =$request->get('mahasiswa');
            $semester=$request->get('semester');
            $tahun_akademik=$request->get('tahun_akademik');

            $this->validate($request,[
                'topik_konsultasi' =>'required',
                'permasalahan' =>'required',
                'solusi' =>'required',
                'dosen' =>'required',
                'mahasiswa' =>'required',
                'semester'=>'required',
                'tahun_akademik'=>'required'
            ]);


            $tambahdata_topik = Topik_konsultasi::insert([
                'namatopik' =>$topik_konsultasi
            ]);

            $select_topik = DB::table('topik_konsultasi')
                            ->select('idtopikkonsultasi')
                            ->orderby('idtopikkonsultasi','desc')
                            ->limit(1)
                            ->get();

            $tambahdata_konsultasi= Konsultasi_dosenwali::insert([
                'tanggalkonsultasi'=> $tanggalkonsultasi,
                'permasalahan'=>$permasalahan,
                'solusi'=>$solusi,
                'konsultasiselanjutnya'=>$konsultasi_selanjutnya,
                'konfirmasi'=>0,
                'dosen_npkdosen'=>$dosen,
                'topik_idtopikkonsultasi'=>$select_topik[0]->idtopikkonsultasi,
                'mahasiswa_nrpmahasiswa'=>$mahasiswa,
                'semester_idsemester'=>$semester,
                'thnakademik_idthnakademik'=>$tahun_akademik
            ]);

            return redirect('admin/master/konsultasi')->with(['Success' => 'Berhasil Menambahkan Data']);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('admin/master/konsultasi/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

     public function ubahkonsultasi($id)
    {  
        if(Session::get('admin') != null)
        {
            $dosen = DB::table('dosen')
                    ->select('*')
                    ->get();

            $mahasiswa = DB::table('mahasiswa')
                    ->select('*')
                    ->get();

            $semester = DB::table('semester')
                        -> select('*')
                        ->get();

            $tahun_akademik = DB::table('tahun_akademik')
                            -> select('*')
                            ->get();

            $datakonsultasi = DB::table('konsultasi_dosenwali')
                    ->select('konsultasi_dosenwali.*','dosen.*','topik_konsultasi.*','mahasiswa.*','semester.*','tahun_akademik.*')
                    ->join('dosen', 'dosen.npkdosen', '=', 'konsultasi_dosenwali.dosen_npkdosen')
                    ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi', '=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
                    ->join('mahasiswa', 'mahasiswa.nrpmahasiswa', '=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
                    ->join('semester', 'semester.idsemester', '=', 'konsultasi_dosenwali.semester_idsemester')
                    ->join('tahun_akademik', 'tahun_akademik.idtahunakademik', '=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
                    ->where('konsultasi_dosenwali.idkonsultasi', $id)
                    ->get();

            // dd($datakonsultasi);

            return view('master_konsultasi.ubahkonsultasi_admin', compact('dosen','mahasiswa','semester','tahun_akademik','datakonsultasi'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahkonsultasi_proses(Request $request)
    {
        try
        {
            // Form Validasi Input User
            $this->validate($request,[
                'topik_konsultasi' =>'required',
                'permasalahan' =>'required',
                'solusi' =>'required',
                'dosen' =>'required',
                'mahasiswa' =>'required',
                'semester'=>'required',
                'tahun_akademik'=>'required'
            ]);

          
            $topik = DB::table('topik_konsultasi')
                    ->where('idtopikkonsultasi',$request->get('idtopik'))
                    ->update([
                        'namatopik' => $request->get('topik_konsultasi')
                    ]);

          
            $mahasiswa = DB::table('konsultasi_dosenwali') 
                ->where('idkonsultasi',$request->get('idkonsultasi'))
                ->update([
                    'permasalahan' => $request->get('permasalahan'),
                    'solusi' => $request->get('solusi'),
                    'konsultasiselanjutnya'=> $request->get('konsultasi_selanjutnya'),
                    'dosen_npkdosen' =>$request->get('dosen'),
                    'topik_idtopikkonsultasi'=>$request->get('idtopik'),
                    'mahasiswa_nrpmahasiswa'=>$request->get('mahasiswa'),
                    'semester_idsemester'=>$request->get('semester'),
                    'thnakademik_idthnakademik'=>$request->get('tahun_akademik')
                    
                ]);
          

            return redirect('admin/master/konsultasi')->with(['Success' => 'Berhasil Mengubah Data Konsultasi (ID) '.$request->get('idkonsultasi')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("admin/master/konsultasi/ubah/{$request->get('idkonsultasi')}")->with(['Error' => 'Gagal Mengubah Data Konsultasi (ID) '.$request->get('idkonsultasi')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }

    public function hapuskonsultasi (Request $request,$id)
    {
        try
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
                ->where('idkonsultasi',$id)
                ->delete();

            $topik = DB::table('topik_konsultasi')
                ->where('idtopikkonsultasi',$request->get('idtopik'))
                ->delete();

            return redirect('admin/master/konsultasi')->with(['Success' => 'Berhasil Menghapus Data Konsultasi (ID) '.$id]);
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/konsultasi")->with(['Error' => 'Gagal Menghapus Data '.$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }
   
    public function carikonsultasi (Request $request)
    {
        $jenis_pencarian = $request->get('pencarian');
        $keyword = $request->get('keyword');

         $this->validate($request,[
            'pencarian' => 'required',
            'keyword' =>'required'
        ]);

        if($jenis_pencarian == "tanggal")
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('tanggalkonsultasi',$keyword)
            ->paginate(10);
        }
        else if($jenis_pencarian == "topik")
        {
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('topik_konsultasi.namatopik',$keyword)
            ->paginate(10);
        }
        else if($jenis_pencarian == "namadosen")
        {
            $exp_keyword = explode(' ',$keyword);

            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('dosen.npkdosen',$exp_keyword[0])
            ->paginate(10);
        }
        else if($jenis_pencarian == "namamahasiswa")
        {
            $exp_keyword = explode(' ',$keyword);

            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$exp_keyword[0])
            ->paginate(10);
        }
       
       
        else if($jenis_pencarian == "tahunakademik")
        {
           $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('konsultasi_dosenwali.*','dosen.namadosen','dosen.npkdosen','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','topik_konsultasi.namatopik','semester.semester','tahun_akademik.tahun')
            ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester', 'semester.idsemester','=', 'konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('tahun_akademik.tahun',$keyword)
            ->paginate(10);
        }
        else
        {
            return redirect("admin/master/konsultasi")->with(['Error' => 'Gagal melakukan proses pencarian']);
        }

        return view('master_konsultasi.daftarkonsultasi_admin', compact('konsultasi'));
    }

    function fetch(Request $request)
    {
        $query = $request->get('query');
        $pencarian =$request->get('jenis');

        if($request->get('query') && $request->get('jenis'))
        {           
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';    

            if($pencarian == 'tanggal')
            {
                $konsultasi = DB::table('konsultasi_dosenwali')
                ->select('tanggalkonsultasi')
                ->where('tanggalkonsultasi','LIKE', "%{$query}%")
                ->groupBy('tanggalkonsultasi')
                ->get();
                
                foreach($konsultasi as $row)
                {
                    $output .= '<li><a href="#">'.$row->tanggalkonsultasi.'</a></li>';
                }
            }      
            else if( $pencarian== 'topik')
            {
                $konsultasi = DB::table('konsultasi_dosenwali')
                ->select('topik_konsultasi.namatopik')
                ->join('topik_konsultasi', 'topik_konsultasi.idtopikkonsultasi','=', 'konsultasi_dosenwali.topik_idtopikkonsultasi')
                ->where('topik_konsultasi.namatopik','LIKE', "%{$query}%")
                ->groupBy('topik_konsultasi.namatopik')
                ->get();

                foreach($konsultasi as $row)
                {
                    $output .= '<li><a href="#">'.$row->namatopik.'</a></li>';
                }
            }    
            else if( $pencarian== 'namadosen')
            {
                $konsultasi = DB::table('konsultasi_dosenwali')
                ->select('dosen.npkdosen','dosen.namadosen')
                ->join('dosen', 'dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
                ->where('dosen.namadosen', 'LIKE', "%{$query}%")
                ->groupBy('dosen.namadosen')
                ->groupBy('dosen.npkdosen')
                ->get();
     
                foreach($konsultasi as $row)
                {
                    $output .= '<li><a href="#">'.$row->npkdosen." ".$row->namadosen.'</a></li>';
                }
            } 
            else if( $pencarian== 'namamahasiswa')
            {
                $konsultasi = DB::table('konsultasi_dosenwali')
                ->select('mahasiswa.nrpmahasiswa','mahasiswa.namamahasiswa')
                ->join('mahasiswa', 'mahasiswa.nrpmahasiswa','=', 'konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
                ->where('mahasiswa.namamahasiswa', 'LIKE', "%{$query}%")
                ->groupBy('mahasiswa.namamahasiswa')
                ->groupBy('mahasiswa.nrpmahasiswa')
                ->get();
     
                foreach($konsultasi as $row)
                {
                    $output .= '<li><a href="#">'.$row->nrpmahasiswa." ".$row->namamahasiswa.'</a></li>';
                }
            }     
            
            else if( $pencarian== 'tahunakademik')
            {
                $matakuliah = DB::table('matakuliah')
                ->select('tahun_akademik.tahun')
                ->join('tahun_akademik', 'tahun_akademik.idtahunakademik','=', 'matakuliah.thnakademik_idthnakademik')
                ->where('tahun_akademik.tahun','LIKE',"%{$query}%" )
                ->groupBy('tahun_akademik.tahun')
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
