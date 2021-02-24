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
use App\Non_konsultasi;

class DataNonKonsultasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarnonkonsultasi()
    {
    	if(Session::get('dosen') != null)
        {
        	$dosen = DB::table('users')
        	->join('dosen','dosen.users_username','=','users.username')
        	->where('users.username',Session::get('dosen'))
        	->get();

        	$data_non_konsultasi = DB::table('non_konsultasi')
        	->select('non_konsultasi.*','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
        	->join('dosen','dosen.npkdosen','=','non_konsultasi.dosen_npkdosen')
			->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
			->where('dosen.npkdosen', $dosen[0]->npkdosen)
			->get();
			
			$non_konsultasi_berikutnya = DB::table('non_konsultasi')
            ->select('non_konsultasi.tanggalpertemuan','mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','non_konsultasi.mahasiswa_nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=', 'non_konsultasi.dosen_npkdosen')
            ->where('non_konsultasi.dosen_npkdosen',$dosen[0]->npkdosen )
            ->where('non_konsultasi.status',0)
            ->wheredate('non_konsultasi.tanggalpertemuan','>=',Carbon::now())
            ->orderBy('non_konsultasi.tanggalpertemuan','ASC')
            ->get();


            $this->ubah_status();
        	return view('data_nonkonsultasi.daftarnonkonsultasi_dosen', compact('data_non_konsultasi','non_konsultasi_berikutnya'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function hapusnonkonsultasi ($id)
    {
        try
        {
            $hapus_non_konsultasi = DB::table('non_konsultasi')
                ->where('idnonkonsultasi', $id)
                ->delete();

            return redirect('dosen/data/nonkonsultasi')->with(['Success' => 'Berhasil Menghapus Data Non-Konsultasi (ID) '.$id]);
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("dosen/data/nonkonsultasi")->with(['Error' => 'Gagal Menghapus Data (ID) '.$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }




    public function ubah_status()
    {
        if(Session::get('dosen')!=null)
        {
            $dosen = DB::table('users')
        	->join('dosen','dosen.users_username','=','users.username')
        	->where('users.username',Session::get('dosen'))
        	->get();

        	$data_non_konsultasi = DB::table('non_konsultasi')
        	->select('idnonkonsultasi','tanggalpertemuan','non_konsultasi.status')
			->join('dosen','dosen.npkdosen','=','non_konsultasi.dosen_npkdosen')
			->where('non_konsultasi.status',0)
			->where('dosen.npkdosen', $dosen[0]->npkdosen)
			->whereDate('tanggalpertemuan','<=', now())
			->get();

			foreach ($data_non_konsultasi as $d ) 
			{
        		$topik = DB::table('non_konsultasi')
                ->where('idnonkonsultasi', $d->idnonkonsultasi)
                ->update([
                    'status' => 1
                ]);	
			}
        }
        else
        {
            return redirect("/");
        }

    }

}
 