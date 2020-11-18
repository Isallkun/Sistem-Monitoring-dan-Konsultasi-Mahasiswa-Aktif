<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use Carbon\Carbon;
use DB;

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
            $dosen_aktif = DB::table('dosen')
                        ->select('*')
                        ->where('status','aktif')
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
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan'))
            ->groupBy('bulan')
            ->orderBy('bulan','DESC')
            ->get();

            $start=Carbon::now()->month-3;
            $end=Carbon::now()->month;

            $total_konsultasi_sekarang = DB::table('konsultasi_dosenwali')
            ->select(DB::raw('COUNT(*) as total, MONTHNAME(tanggalkonsultasi) as bulan'))
            ->whereBetween(DB::raw('MONTH(tanggalkonsultasi)'),[$start,$end])
            ->whereYear('tanggalkonsultasi',Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan','DESC')
            ->get();

            return view('home_admin',compact('dosen_aktif', 'mahasiswa_aktif', 'matakuliah','konsultasi','total_konsultasi','total_konsultasi_sekarang'));

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

            $konsultasi_berikutnya = DB::table('konsultasi_dosenwali')
            ->join('dosen','dosen.npkdosen','=', 'konsultasi_dosenwali.dosen_npkdosen')
            ->where('konsultasi_dosenwali.dosen_npkdosen',$dosen[0]->npkdosen )
            ->wheredate('konsultasi_dosenwali.konsultasiselanjutnya','>=',Carbon::now())
            ->count();

            return view('home_dosen', compact('mahasiswa','hukuman','konsultasi','konsultasi_berikutnya'));
            
        }
        else
        {
            return redirect('/');
        }
    }
}
  