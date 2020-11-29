<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use Carbon\Carbon;

use App\User;
use App\Mahasiswa;
use App\Dosen;
use App\Gamifikasi;
use App\Konsultasi_dosenwali;
use App\Topik_konsultasi;


class DataKonsultasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarkonsultasi()
    {
    	if(Session::get('dosen') != null)
        {
        	$dosen = DB::table('users')
        	->join('dosen','dosen.users_username','=','users.username')
        	->where('users.username',Session::get('dosen'))
        	->get();

        	$data_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('dosen.npkdosen',$dosen[0]->npkdosen)
            ->orderBy('konsultasi_dosenwali.idkonsultasi','DESC')
            ->get();

            $konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
            ->select('konsultasiselanjutnya','namamahasiswa','mahasiswa_nrpmahasiswa')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
            ->orderBy('konsultasiselanjutnya','ASC')
            ->get();

        	return view('data_konsultasi.daftarkonsultasi_dosen', compact('data_konsultasi'))->with('notification', $konsultasi_berikutnya);
        }
        else
        {
        	return redirect("/");
        }
    }

    public function tambahkonsultasi()
    {
        if(Session::get('dosen') != null)
        {
            $dosen = DB::table('users')
            ->join('dosen','dosen.users_username','=','users.username')
            ->where('users.username',Session::get('dosen'))
            ->get();

            $mahasiswa = DB::table('mahasiswa')
                    ->select('*')
                    ->join("dosen","dosen.npkdosen","=","mahasiswa.dosen_npkdosen")
                    ->where("dosen.npkdosen",$dosen[0]->npkdosen)
                    ->get();

            $semester = DB::table('semester')
                        -> select('*')
                        ->get();

            $tahun_akademik = DB::table('tahun_akademik')
                            -> select('*')
                            ->get();

            
            return view('data_konsultasi.tambahkonsultasi_dosen', compact('mahasiswa','semester','tahun_akademik'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahkonsultasi_proses(Request $request)
    {
        try
        {
            $dosen = DB::table('users')
            ->join('dosen','dosen.users_username','=','users.username')
            ->where('users.username',Session::get('dosen'))
            ->get();

            $tanggalkonsultasi= Carbon::now()->format('Y/m/d');
            $topik_konsultasi = $request->get('topik_konsultasi');
            $permasalahan = $request->get('permasalahan');
            $solusi = $request->get('solusi');
            $konsultasi_selanjutnya = $request->get('konsultasi_selanjutnya');
            $mahasiswa =$request->get('mahasiswa');
            $semester=$request->get('semester');
            $tahun_akademik=$request->get('tahun_akademik');

            // dd($dosen[0]->npkdosen);

            $this->validate($request,[
                'mahasiswa' =>'required',
                'semester'=>'required',
                'tahun_akademik'=>'required',
                'topik_konsultasi' =>'required',
                'permasalahan' =>'required',
                'solusi' =>'required',
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
                'dosen_npkdosen'=>$dosen[0]->npkdosen,
                'topik_idtopikkonsultasi'=>$select_topik[0]->idtopikkonsultasi,
                'mahasiswa_nrpmahasiswa'=>$mahasiswa,
                'semester_idsemester'=>$semester,
                'thnakademik_idthnakademik'=>$tahun_akademik
            ]);

            return redirect('dosen/data/konsultasi/rangkumankondisi/'.$mahasiswa)->with(['Success' => 'Berhasil Menambahkan Data Konsultasi Mahasiswa ('. $mahasiswa.')']);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('dosen/data/mahasiswa/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

    public function kondisi($id)
    {
        if(Session::get('dosen') != null)
        {   
            $konsultasi_mhs = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa_nrpmahasiswa',$id)
            ->orderby('idkonsultasi','DESC')
            ->get();
            // dd($konsultasi_mhs);
            return view('data_konsultasi.rangkumankondisi_dosen', compact('konsultasi_mhs'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahrating_proses(Request $request, $id)
    {
        if(Session::get('dosen') != null)
        {
            try
            {
                $nilai_rating = $request->get('star');

                $mahasiswa_gamifikasi = DB::table("mahasiswa")
                ->select(DB::raw('AVG(gamifikasi.poin) as jumlahpoin'),'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','gamifikasi.idgamifikasi','gamifikasi.level')
                ->join('gamifikasi','idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
                ->where('mahasiswa.nrpmahasiswa',$id)
                ->get();

                $total_konsultasi_mahasiswa = DB::table("konsultasi_dosenwali")
                ->select(DB::raw('count(*) as jumlahkonsultasi'),'mahasiswa.nrpmahasiswa','mahasiswa.namamahasiswa')
                ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
                ->where('mahasiswa.nrpmahasiswa',$id)
                ->get();

                //perhitungan untuk penambahan poin baru
                $poin_terbaru = ($mahasiswa_gamifikasi[0]->jumlahpoin + $nilai_rating)/$total_konsultasi_mahasiswa[0]->jumlahkonsultasi;  
                $level_user ="";
                if($poin_terbaru <= 2)
                {   
                    $level_user="Bronze";
                }
                elseif($poin_terbaru > 2 && $poin_terbaru <= 4 )
                {
                    $level_user="Silver";
                }
                elseif($poin_terbaru > 4)
                {
                    $level_user="Gold";
                }
                
                $gamifikasi = DB::table('gamifikasi')
                ->where('idgamifikasi',$mahasiswa_gamifikasi[0]->idgamifikasi)
                ->update([
                    'poin' => $poin_terbaru,
                    'level' =>$level_user
                ]);

                return redirect("dosen/data/konsultasi");
            }
            catch (QueryException $e)
            {
                return redirect("dosen/data/konsultasi/rangkumankondisi/$id")->with(['Error' => 'Mohon maaf, sistem gagal menambahkan rating mahasiswa']);
            }
            

            
            
        }
        else
        {
            return redirect("/");
        }
    }


    public function ubahkonsultasi($id)
    {
        if(Session::get('dosen') != null)
        {
            $semester = DB::table('semester')
            ->select('*')
            ->get();

            $tahun_akademik = DB::table('tahun_akademik')
            -> select('*')
            ->get();

            $datakonsultasi= DB::table("konsultasi_dosenwali")
            ->join("topik_konsultasi","topik_konsultasi.idtopikkonsultasi","=","konsultasi_dosenwali.topik_idtopikkonsultasi")
            ->join("dosen","dosen.npkdosen","=","konsultasi_dosenwali.dosen_npkdosen")
            ->join("mahasiswa","mahasiswa.nrpmahasiswa","=","konsultasi_dosenwali.mahasiswa_nrpmahasiswa")
            ->join("semester","semester.idsemester","=","konsultasi_dosenwali.semester_idsemester")
            ->join("tahun_akademik","tahun_akademik.idtahunakademik","=","konsultasi_dosenwali.thnakademik_idthnakademik")
            ->where("topik_konsultasi.idtopikkonsultasi",$id)
            ->get();

            return view('data_konsultasi.ubahkonsultasi_dosen',compact('semester','tahun_akademik','datakonsultasi'));
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
                'topik_idtopikkonsultasi'=>$request->get('idtopik'),
                'semester_idsemester'=>$request->get('semester'),
                'thnakademik_idthnakademik'=>$request->get('tahun_akademik')
            ]);
          
            return redirect('dosen/data/konsultasi')->with(['Success' => 'Berhasil Mengubah Data Konsultasi (ID) '.$request->get('idkonsultasi')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/konsultasi/ubah/{$request->get('idkonsultasi')}")->with(['Error' => 'Gagal Mengubah Data Konsultasi (ID) '.$request->get('idkonsultasi')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }
}
