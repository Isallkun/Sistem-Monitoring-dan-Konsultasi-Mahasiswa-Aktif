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

        	return view('data_konsultasi.daftarkonsultasi_dosen', compact('data_konsultasi','konsultasi_berikutnya'));
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
            
            $durasi_konsultasi = $request->get('temp_value');
            Session::put('durasi', $durasi_konsultasi);

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
            
            return redirect('dosen/data/konsultasi/rangkumankondisi/'.$mahasiswa)->with(['Success' => 'Berhasil Menambahkan Data Konsultasi Terjadwal ('. $mahasiswa.')']);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('dosen/data/konsultasi/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

    public function kondisi($id)
    {
        if(Session::get('dosen') != null)
        {   
            $durasi = Session::get('durasi');

            $konsultasi_mhs = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa_nrpmahasiswa',$id)
            ->orderby('idkonsultasi','DESC')
            ->limit(5)
            ->get();

            return view('data_konsultasi.rangkumankondisi_dosen', compact('konsultasi_mhs','durasi'));
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
                $durasi = Session::get('durasi');
                $explode_durasi = (explode('::',$durasi));
        
                $nilai_durasikonsultasi =0;
                $nilai_manfaatkonsultasi = $request->get('star_manfaatkonsultasi');
                $nilai_sifatkonsultasi = $request->get('star_sifatkonsultasi');
                $nilai_interaksi = $request->get('star_interaksi');
                $nilai_pencapaian = $request->get('star_pencapaian');


                if($explode_durasi[0] <= "01 Menit")
                {
                    //dibawah 1 menit = *
                    $nilai_durasikonsultasi=1;
                }
                elseif($explode_durasi[0] > "01 Menit" ||  $explode_durasi[0] <= "03 Menit")
                {
                    //antara 1menit - 3menit = **  
                    $nilai_durasikonsultasi=2;  
                }
                elseif($explode_durasi[0] > "03 Menit" ||  $explode_durasi[0] <= "05 Menit")
                {
                    //antara 3menit - 5menit = ***    
                    $nilai_durasikonsultasi=3;
                }
                elseif($explode_durasi[0] > "05 Menit" ||  $explode_durasi[0] <= "08 Menit")    
                {
                    //antara 5menit - 8menit = ****
                    $nilai_durasikonsultasi=4;    
                }   
                else
                {
                    // 9menit keatas = *****
                    $nilai_durasikonsultasi=5;
                }         
                

                // Mengambil data poin setiap aspek yang dimiliki o/ mahasiswa
                $mahasiswa_gamifikasi = DB::table("mahasiswa")
                ->select('mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa', 'gamifikasi.idgamifikasi', 
                         'gamifikasi.aspek_durasi_konsultasi as poin_durasi', 'gamifikasi.aspek_manfaat_konsultasi as poin_manfaat', 
                         'gamifikasi.aspek_sifat_konsultasi as poin_sifat', 'gamifikasi.aspek_interaksi as poin_interaksi',
                         'gamifikasi.aspek_pencapaian as poin_pencapaian','gamifikasi.total','gamifikasi.level')
                ->join('gamifikasi','idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
                ->where('mahasiswa.nrpmahasiswa',$id)
                ->get();

                // Menghitung total konsultasi mahasiswa
                 $total_konsultasi_mahasiswa = DB::table("konsultasi_dosenwali")
                ->select(DB::raw('count(*) as jumlahkonsultasi'),'mahasiswa.nrpmahasiswa','mahasiswa.namamahasiswa')
                ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
                ->where('mahasiswa.nrpmahasiswa',$id)
                ->get();

                //Proses penghitungan poin (per aspek)
                //1. Aspek Durasi Konsultasi
                $durasi = $mahasiswa_gamifikasi[0]->poin_durasi + $nilai_durasikonsultasi;
                //2. Aspek Manfaat Konsultasi
                $manfaat = $mahasiswa_gamifikasi[0]->poin_manfaat + $nilai_manfaatkonsultasi;
                //3. Aspek Sifat Konsultasi
                $sifat = $mahasiswa_gamifikasi[0]->poin_sifat + $nilai_sifatkonsultasi;
                //4. Aspek Interaksi
                $interaksi = $mahasiswa_gamifikasi[0]->poin_interaksi + $nilai_interaksi;
                //5. Aspek Pencapaian
                $pencapaian = $mahasiswa_gamifikasi[0]->poin_pencapaian + $nilai_pencapaian;

                //Perhitungan rata-rata poin 
                $avg_total_poin = (($durasi+$manfaat+$sifat+$interaksi+$pencapaian)/5)/$total_konsultasi_mahasiswa[0]->jumlahkonsultasi;

              
                $level_user ="";
                if($avg_total_poin <= 2)
                {   
                    $level_user="Bronze";
                }
                elseif($avg_total_poin > 2 && $avg_total_poin <= 4 )
                {
                    $level_user="Silver";
                }
                elseif($avg_total_poin > 4)
                {
                    $level_user="Gold";
                }

                $gamifikasi = DB::table('gamifikasi')
                ->where('idgamifikasi',$mahasiswa_gamifikasi[0]->idgamifikasi)
                ->update([
                    'aspek_durasi_konsultasi' => $durasi,
                    'aspek_manfaat_konsultasi' => $manfaat,
                    'aspek_sifat_konsultasi' => $sifat,
                    'aspek_interaksi'=>$interaksi,
                    'aspek_pencapaian' => $pencapaian,
                    'total' => $avg_total_poin,
                    'level' =>$level_user
                ]);


                //Menghapus session durasi konsultasi
                Session::forget('durasi');

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
            ->where("konsultasi_dosenwali.idkonsultasi",$id)
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
          
            return redirect('dosen/data/konsultasi')->with(['Success' => 'Berhasil Mengubah Data Konsultasi Terjadwal (ID) '.$request->get('idkonsultasi')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/konsultasi/ubah/{$request->get('idkonsultasi')}")->with(['Error' => 'Gagal Mengubah Data Konsultasi (ID) '.$request->get('idkonsultasi')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }


    public function daftarkonsultasi_mahasiswa()
    {
        if(Session::get('mahasiswa') != null)
        {
            $mahasiswa = DB::table('users')
            ->join('mahasiswa','mahasiswa.users_username','=','users.username')
            ->where('users.username',Session::get('mahasiswa'))
            ->get();

            $data_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->where('konsultasi_dosenwali.konfirmasi',0)
            ->orderBy('konsultasi_dosenwali.idkonsultasi','DESC')
            ->get();

            $semua_konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->orderBy('konsultasi_dosenwali.idkonsultasi','ASC')
            ->get();

            $konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
            ->select('konsultasiselanjutnya','namamahasiswa','mahasiswa_nrpmahasiswa')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.mahasiswa_nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa )
            ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
            ->orderBy('konsultasiselanjutnya','ASC')
            ->get();

            $tanggal_sekarang = Carbon::now();

            return view('data_konsultasi.daftarkonsultasi_mahasiswa',compact('data_konsultasi','semua_konsultasi','konsultasi_berikutnya','tanggal_sekarang'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function konfirmasikonsultasi_proses($id)
    {
        if(Session::get('mahasiswa') != null)
        {
            $status_konfirmasi = DB::table('konsultasi_dosenwali')
            ->select('konfirmasi')
            ->where('idkonsultasi', $id)
            ->get();

            if($status_konfirmasi[0]->konfirmasi == 0)
            {
                $hasil_konfirmasi = DB::table('konsultasi_dosenwali') 
                ->where('idkonsultasi',$id)
                ->update([
                    'konfirmasi' => '1'
                ]);   
            }

            return redirect('mahasiswa/data/konsultasimahasiswa');
        }
        else
        {
            return redirect("/");
        }
    }
}
