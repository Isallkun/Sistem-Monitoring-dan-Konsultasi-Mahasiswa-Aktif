<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use Session;
use Carbon\Carbon;
use DB;
use PDF;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_admin()
    { 
        if(Session::get('admin') != null)
        {
            $dosen = DB::table('dosen')
                    ->select('*')
                    ->count();
            $dosen_aktif = DB::table('dosen')
                        ->select('*')
                        ->where('status','aktif')
                        ->count();
            
            $mahasiswa = DB::table('mahasiswa')
                        ->select('*')
                        ->count();
            $mahasiswa_aktif = DB::table('mahasiswa')
                        ->select('*')
                        ->where('status','aktif')
                        ->count();
            

            $matakuliah = DB::table('matakuliah')
                        ->select('*')
                        ->count();

            $konsultasi = DB::table('konsultasi_dosenwali')
                        ->select('*')
                        ->count();


            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $start=Carbon::now()->month-3;
            $end=Carbon::now()->month;
            $total_konsultasi_sekarang = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->whereBetween(DB::raw('MONTH(tanggalkonsultasi)'),[$start,$end])
            ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $aktifitaskonsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $tahun_sekarang=Carbon::now()->year;


            $semester = DB::table('semester')
            ->get();
            $semester_aktif = DB::table('semester')
            ->where('status',1)
            ->get();

            $tahun = DB::table('tahun_akademik')
            ->get();
            $tahun_aktif = DB::table('tahun_akademik')
            ->where('status',1)
            ->get();

            return view('home_admin',compact('dosen','dosen_aktif', 'mahasiswa','mahasiswa_aktif', 'matakuliah','konsultasi','total_konsultasi','total_konsultasi_sekarang','aktifitaskonsultasi','tahun_sekarang','semester','semester_aktif','tahun','tahun_aktif'));

            //Untuk Multi login user (dengan hak akses berbeda)
            // if(Session::get('dosen') != null)
            // {
            //     return view('home_dosen');
            // }
            // else if(Session::get('mahasiswa') != null)
            // {
            //     return view('auth.login');  
            // }
            
        }
        else
        {
            return redirect('/');  
        }
    }

    public function ubahtahun_proses(Request $request)
    {
        try
        {
            $this->validate($request,[
                'semester' =>'required',
                'tahun'=>'required'
            ]);

            //Mengubah Status Semester dan Tahun Akademik yg aktif menjadi tidak aktif
            $semester = DB::table('semester') 
            ->where('idsemester',$request->get('semester_aktif'))
            ->update([
                'status' =>0
            ]);
            $tahunakademik = DB::table('tahun_akademik') 
            ->where('idtahunakademik',$request->get('tahun_aktif'))
            ->update([
                'status' =>0
            ]);


            //Mengubah status semester dan tahun akademik yg dipilih user menjadi aktif
            $semester = DB::table('semester') 
            ->where('idsemester',$request->get('semester'))
            ->update([
                'status' =>1
            ]);
            $tahunakademik = DB::table('tahun_akademik') 
            ->where('idtahunakademik',$request->get('tahun'))
            ->update([
                'status' =>1
            ]);

            return redirect('admin')->with(['Success' => 'Berhasil Mengubah Tahun Akademik']);
        }
        catch(QueryException $e)
        {
            return redirect('admin')->with(['Error' => 'Gagal Mengubah Tahun Akademik <br> Pesan Kesalahan: '.$message[0]]);
        }
    }


    public function index_dosen()
    {
        if(Session::get('dosen') != null)
        {
            $dosen = DB::table('dosen')
            ->join('users','users.username','=', 'dosen.users_username')
            ->where('users.username', Session::get('dosen'))
            ->get();

            $mahasiswa = DB::table('mahasiswa')
            ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
            ->where('mahasiswa.dosen_npkdosen',$dosen[0]->npkdosen )
            ->where('mahasiswa.status','aktif')
            ->count();

            $hukuman = DB::table('hukuman')
            ->join('dosen','dosen.npkdosen','=', 'hukuman.dosen_npkdosen')
            ->where('hukuman.dosen_npkdosen',$dosen[0]->npkdosen )
            ->count();
 
            $konsultasi = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->count();
         
            $nonkonsultasi = DB::table('non_konsultasi')
            ->join('dosen','dosen.npkdosen','=', 'non_konsultasi.dosen_npkdosen')
            ->where('non_konsultasi.dosen_npkdosen',$dosen[0]->npkdosen )
            ->count();

            // Grafik IP mahasiswa berdasarkan range
            // IPS Mahasiswa
            $ips1_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ips),0) as ips, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ips',[1,2]);
            $ips2_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ips),0) as ips, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ips',[3,4]);
            $results_ips = $ips2_mahasiswa->union($ips1_mahasiswa)->orderBy("ips","ASC")->get();
            
            //IPK Mahasiswa
            $ipk1_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ipk),0) as ipk, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ipk',[1,2]);
            $ipk2_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ipk),0) as ipk, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ipk',[3,4]);
            $results_ipk = $ipk2_mahasiswa->union($ipk1_mahasiswa)->orderBy("ipk","ASC")->get();

            //IPKM Mahasiswa
            $ipkm1_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ipkm),0) as ipkm, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ipkm',[1,2]);
            $ipkm2_mahasiswa = DB::table('kartu_studi')
            ->select(DB::raw('COUNT(*) as total, round((ipkm),0) as ipkm, nrpmahasiswa'))
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->whereBetween('kartu_studi.ipkm',[3,4]);
            $results_ipkm = $ipkm2_mahasiswa->union($ipkm1_mahasiswa)->orderBy("ipkm","ASC")->get();
          
            //Grafik NISBI Mata Kuliah
            $matakuliah = DB::table('matakuliah')
            ->select('kodematakuliah','namamatakuliah')
            ->get();
            $data_nisbi = DB::table('kartu_studi')
            ->select(DB::raw('count(nisbi) as total, " " as namamatakuliah, "Total Mata kuliah" as label'),"idkartustudi","nisbi",'nrpmahasiswa')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('detail_kartu_studi','detail_kartu_studi.kartustudi_idkartustudi','=','kartu_studi.idkartustudi')
            ->where('dosen_npkdosen',$dosen[0]->npkdosen)
            ->where('nisbi','!=','')
            ->groupBy('nisbi')
            ->get();       

            //Grafik Kondisi Mahasiswa
            $kondisi_mahasiswa = DB::table('mahasiswa')
            ->select(DB::raw('count(*) as total'), 'flag')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->where('dosen.npkdosen',$dosen[0]->npkdosen)
            ->groupBy('flag')
            ->get();
            
            // Grafik Total Konsultasi Dosen
            // Total konsultasi seluruh
            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan, MONTH(tanggalkonsultasi) as bln'))
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();
            //Total konsultasi sekarang
            $start=Carbon::now()->month-3;
            $end=Carbon::now()->month;
            $total_konsultasi_sekarang = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan, MONTH(tanggalkonsultasi) as bln'))
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->whereBetween(DB::raw('MONTH(tanggalkonsultasi)'),[$start,$end])
            ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();
            


            $notif_konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
            ->count();
        
            $notif_nonkonsultasi_berikutnya = DB::table('non_konsultasi')
            ->join('dosen','dosen.npkdosen','=', 'non_konsultasi.dosen_npkdosen')
            ->where('non_konsultasi.dosen_npkdosen',$dosen[0]->npkdosen )
            ->wheredate('non_konsultasi.tanggalpertemuan','>=',Carbon::now())
            ->count();

            $notif_hukumanaktif_terbaru = DB::table('hukuman')
            ->join('dosen','dosen.npkdosen','=', 'hukuman.dosen_npkdosen')
            ->where('hukuman.dosen_npkdosen',$dosen[0]->npkdosen )
            ->where('hukuman.status',1)
            ->wheredate('hukuman.tanggalkonfirmasi','>=',Carbon::now()->subDays(7))
            ->count();
      
            return view('home_dosen', compact('mahasiswa','hukuman','nonkonsultasi','konsultasi','results_ips','results_ipk','results_ipkm','matakuliah','data_nisbi','kondisi_mahasiswa','total_konsultasi','total_konsultasi_sekarang','notif_konsultasi_berikutnya','notif_nonkonsultasi_berikutnya','notif_hukumanaktif_terbaru'));
            
        }
        else
        {
            return redirect('/');
        }
    }

    public function tampilkan_matakuliah(Request $request)
    {
        $dosen = DB::table('dosen')
        ->join('users','users.username','=', 'dosen.users_username')
        ->where('users.username', Session::get('dosen'))
        ->get();

        $mahasiswa = DB::table('mahasiswa')
        ->join('dosen','dosen.npkdosen','=', 'mahasiswa.dosen_npkdosen')
        ->where('mahasiswa.dosen_npkdosen',$dosen[0]->npkdosen )
        ->where('mahasiswa.status','aktif')
        ->count();

        $hukuman = DB::table('hukuman')
        ->join('dosen','dosen.npkdosen','=', 'hukuman.dosen_npkdosen')
        ->where('hukuman.dosen_npkdosen',$dosen[0]->npkdosen )
        ->count();

        $konsultasi = DB::table('konsultasi_dosenwali')
        ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
        ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
        ->count();

        $nonkonsultasi = DB::table('non_konsultasi')
        ->join('dosen','dosen.npkdosen','=', 'non_konsultasi.dosen_npkdosen')
        ->where('non_konsultasi.dosen_npkdosen',$dosen[0]->npkdosen )
        ->count();


        $ips1_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ips),0) as ips, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ips',[1,2]);
        $ips2_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ips),0) as ips, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ips',[3,4]);
        $results_ips = $ips2_mahasiswa->union($ips1_mahasiswa)->orderBy("ips","ASC")->get();
        
        //IPK Mahasiswa
        $ipk1_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ipk),0) as ipk, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ipk',[1,2]);
        $ipk2_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ipk),0) as ipk, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ipk',[3,4]);
        $results_ipk = $ipk2_mahasiswa->union($ipk1_mahasiswa)->orderBy("ipk","ASC")->get();
      
        //IPKM Mahasiswa
        $ipkm1_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ipkm),0) as ipkm, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ipkm',[1,2]);
        $ipkm2_mahasiswa = DB::table('kartu_studi')
        ->select(DB::raw('COUNT(*) as total, round((ipkm),0) as ipkm, nrpmahasiswa'))
        ->join('mahasiswa','mahasiswa.nrpmahasiswa', '=', 'kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','kartu_studi.thnakademik_idthnakademik')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->whereBetween('kartu_studi.ipkm',[3,4]);
        $results_ipkm = $ipkm2_mahasiswa->union($ipkm1_mahasiswa)->orderBy("ipkm","ASC")->get();
      
        //Grafik Pencarian NISBI Matakuliah
        $matakuliah = DB::table('matakuliah')
        ->select('kodematakuliah','namamatakuliah')
        ->get();
           
        $kode_matakuliah=$request->get('matakuliah');
        $data_nisbi = DB::table('matakuliah')
        ->select(DB::raw('count(*) as total,"Total Mahasiswa" as label'), 'nisbi', 'matakuliah.kodematakuliah','matakuliah.namamatakuliah','dosen.*')
        ->join('detail_kartu_studi','detail_kartu_studi.matakuliah_kodematakuliah','=','matakuliah.kodematakuliah')
        ->join('kartu_studi','kartu_studi.idkartustudi','=','detail_kartu_studi.kartustudi_idkartustudi')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','kartu_studi.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->groupBy('nisbi')
        ->where('nisbi','!=','')
        ->where('dosen_npkdosen',$dosen[0]->npkdosen)
        ->where('matakuliah.kodematakuliah','=',$kode_matakuliah)
        ->get();    

        //Grafik Kondisi Mahasiswa
        $kondisi_mahasiswa = DB::table('mahasiswa')
        ->select(DB::raw('count(*) as total'), 'flag')
        ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
        ->where('dosen.npkdosen',$dosen[0]->npkdosen)
        ->groupBy('flag')
        ->get();
            
        // Grafik Total Konsultasi Dosen
        // Total konsultasi seluruh
        $total_konsultasi = DB::table('konsultasi_dosenwali')
        ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan, MONTH(tanggalkonsultasi) as bln'))
        ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
        ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
        ->groupBy('bln')
        ->orderBy('bln','DESC')
        ->get();
        //Total konsultasi sekarang
        $start=Carbon::now()->month-3;
        $end=Carbon::now()->month;
        $total_konsultasi_sekarang = DB::table('konsultasi_dosenwali')
        ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan, MONTH(tanggalkonsultasi) as bln'))
        ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
        ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
        ->whereBetween(DB::raw('MONTH(tanggalkonsultasi)'),[$start,$end])
        ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
        ->groupBy('bln')
        ->orderBy('bln','DESC')
        ->get();

        $notif_konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
        ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
        ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
        ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
        ->count();

        $notif_nonkonsultasi_berikutnya = DB::table('non_konsultasi')
        ->join('dosen','dosen.npkdosen','=', 'non_konsultasi.dosen_npkdosen')
        ->where('non_konsultasi.dosen_npkdosen',$dosen[0]->npkdosen )
        ->wheredate('non_konsultasi.tanggalpertemuan','>=',Carbon::now())
        ->count();

        $notif_hukumanaktif_terbaru = DB::table('hukuman')
        ->join('dosen','dosen.npkdosen','=', 'hukuman.dosen_npkdosen')
        ->where('hukuman.dosen_npkdosen',$dosen[0]->npkdosen )
        ->where('hukuman.status',1)
        ->wheredate('hukuman.tanggalkonfirmasi','>=',Carbon::now()->subDays(7))
        ->count();

        return view('home_dosen', compact('mahasiswa','hukuman','konsultasi','nonkonsultasi','results_ips','results_ipk','results_ipkm','matakuliah','data_nisbi','kondisi_mahasiswa','total_konsultasi','total_konsultasi_sekarang','notif_konsultasi_berikutnya','notif_nonkonsultasi_berikutnya','notif_hukumanaktif_terbaru'));
    }

    public function index_mahasiswa()
    {
        if(Session::get('mahasiswa') != null)
        {
            $mahasiswa = DB::table('mahasiswa')
            ->join('users','users.username','=', 'mahasiswa.users_username')
            ->where('users.username', Session::get('mahasiswa'))
            ->get();

            $konsultasi_mahasiswa = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->count();

            $hukuman_mahasiswa = DB::table('hukuman')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->count();

            $konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
            ->count();

            $nonkonsultasi_berikutnya = DB::table('non_konsultasi')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=', 'non_konsultasi.mahasiswa_nrpmahasiswa')
            ->where('non_konsultasi.mahasiswa_nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa )
            ->wheredate('non_konsultasi.tanggalpertemuan','>=',Carbon::now())
            ->count();

            $menunggu_konfirmasi = DB::table('konsultasi_dosenwali')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->where('konsultasi_dosenwali.konfirmasi', 0)
            ->count();

            $data_konsultasi_mhs = DB::table('konsultasi_dosenwali')
            ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
            ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
            ->where('mahasiswa.nrpmahasiswa',$mahasiswa[0]->nrpmahasiswa)
            ->where('konsultasi_dosenwali.konfirmasi', 1)
            ->orderBy('idkonsultasi','ASC')
            ->get();
            
            return view('home_mahasiswa', compact('mahasiswa','konsultasi_mahasiswa','hukuman_mahasiswa','konsultasi_berikutnya', 'menunggu_konfirmasi','data_konsultasi_mhs','nonkonsultasi_berikutnya'));
        }
        else
        {
            return redirect('/');  
        }
    }

    public function cetak_konsultasi ($id)
    {
        $mahasiswa = DB::table('mahasiswa')
        ->where('nrpmahasiswa', $id)
        ->get();

        $data_konsultasi_mhs = DB::table('konsultasi_dosenwali')
        ->join('topik_konsultasi','topik_konsultasi.idtopikkonsultasi','=','konsultasi_dosenwali.topik_idtopikkonsultasi')
        ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','konsultasi_dosenwali.mahasiswa_nrpmahasiswa')
        ->join('dosen','dosen.npkdosen','=','konsultasi_dosenwali.dosen_npkdosen')
        ->join('semester','semester.idsemester','=','konsultasi_dosenwali.semester_idsemester')
        ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','konsultasi_dosenwali.thnakademik_idthnakademik')
        ->where('mahasiswa.nrpmahasiswa',$id)
        ->where('konsultasi_dosenwali.konfirmasi', 1)
        ->orderBy('idkonsultasi','ASC')
        ->get();

        $pdf = PDF::loadview('data_konsultasi/konsultasi_pdf',['konsultasi'=>$data_konsultasi_mhs,'mhs'=>$mahasiswa]);
        return $pdf->download('laporan-konsultasi-dosenwali-'.$id);
    }


    public function index_ketuajurusan()
    { 
        if(Session::get('ketuajurusan') != null)
        {
            $mahasiswa = DB::table('mahasiswa')
            ->select('*')
            ->count();
            $mahasiswa_aktif = DB::table('mahasiswa')
            ->select('*')
            ->where('status','aktif')
            ->count();

            $konsultasi = DB::table('konsultasi_dosenwali')
            ->select('*')
            ->count();

            $non_konsultasi = DB::table('non_konsultasi')
            ->select('*')
            ->count();

            $hukuman = DB::table('hukuman')
            ->select('*')
            ->count();

            $total_konsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $start=Carbon::now()->month-3;
            $end=Carbon::now()->month;
            $total_konsultasi_sekarang = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->whereBetween(DB::raw('MONTH(tanggalkonsultasi)'),[$start,$end])
            ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $aktifitaskonsultasi = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan,MONTH(tanggalkonsultasi) as bln'))
            ->whereYear('tanggalkonsultasi','=',Carbon::now()->year)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $tahun_sekarang=Carbon::now()->year;

            $total_hukuman = DB::table('hukuman')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalinput) as bulan,MONTH(tanggalinput) as bln'))
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $hukuman_berlangsung = DB::table('hukuman')
            ->select('*',DB::raw('COUNT(*) as total, MONTHNAME(tanggalinput) as bulan,MONTH(tanggalinput) as bln'))
            ->whereDate('masaberlaku','>=',Carbon::now())
            ->where('status',1)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            $hukuman_selesai = DB::table('hukuman')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalinput) as bulan,MONTH(tanggalinput) as bln'))
            ->whereDate('masaberlaku','<=',Carbon::now())
            ->where('status',2)
            ->groupBy('bln')
            ->orderBy('bln','DESC')
            ->get();

            return view('home_ketuajurusan', compact('mahasiswa','mahasiswa_aktif','konsultasi','non_konsultasi','hukuman','total_konsultasi','total_konsultasi_sekarang','aktifitaskonsultasi','tahun_sekarang','total_hukuman','hukuman_berlangsung','hukuman_selesai'));
        }
        else
        {
            return redirect('/');  
        }
    }

}
  