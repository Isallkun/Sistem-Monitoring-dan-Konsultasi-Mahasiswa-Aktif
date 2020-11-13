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
    public function index()
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

            // dd($total_konsultasi_sekarang);

            return view('home_admin',compact('dosen_aktif', 'mahasiswa_aktif', 'matakuliah','konsultasi','total_konsultasi','total_konsultasi_sekarang'));

            //Untuk Multi login user (dengan hak akses berbeda)
            // if(Session::get('dosen') != null)
            // {
            //     return redirect('homem');  
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
}
  