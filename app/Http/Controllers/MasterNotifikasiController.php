<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use DB;
use Session;
use Carbon\Carbon;

use App\Mail\KonsultasiDosenWaliMail;
use App\Dosen;
use App\Mahasiswa;
use App\Jadwal_konsultasi;

class MasterNotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarnotifikasi()
    {
    	if(Session::get('admin')!=null)
        {
        	$jadwalkonsultasi = DB::table('jadwal_konsultasi')
        	->select('*')
        	->get();

        	return view('master_notifikasi.daftarnotifikasi_admin', compact('jadwalkonsultasi'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function tambahnotifikasi()
    {
        if(Session::get('admin') != null)
        {
            return view('master_notifikasi.tambahnotifikasi_admin');
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahnotifikasi_proses(Request $request)
    {
    	try 
    	{
    		$judul = $request->get('judul');
    		$tanggalmulai = $request->get('tanggal_mulai');
    		$tanggalberakhir = $request->get('tanggal_berakhir');
    		$keterangan = $request->get('keterangan');

    		$this->validate($request,[
            	'judul' =>'required',
            	'tanggal_mulai' =>'required',
            	'tanggal_berakhir' =>'required',
        	]);

    		if($tanggalmulai >= now() && $tanggalberakhir >= now())
    		{
    			if($tanggalmulai < $tanggalberakhir)
    			{
    				$tambah_jadwal= Jadwal_konsultasi::insert([
		                'judul'=> $judul,
		                'tanggalinput'=> now(),
		                'status'=>0,
		                'tanggalmulai'=>$tanggalmulai,
		                'tanggalberakhir'=>$tanggalberakhir,
		                'keterangan'=>$keterangan
            		]);


                    // $data_konsultasi = DB::table('jadwal_konsultasi')
                    // ->select('jadwal_konsultasi.*')
                    // ->orderBy('idjadwalkonsultasi', 'DESC')
                    // ->limit(1)
                    // ->get();

                    // $email_pengguna =array();

                    // $pengguna_mahasiswa = DB::table('mahasiswa')
                    // ->select('mahasiswa.email as email_mahasiswa')
                    // ->where('mahasiswa.status','aktif')
                    // ->get();
                    // foreach($pengguna_mahasiswa as $pm)
                    // {
                    //     $email_pengguna[]=$pm->email_mahasiswa;
                    // }
                    // $pengguna_dosen = DB::table('dosen')
                    // ->select('dosen.email as email_dosen')
                    // ->where('dosen.status','aktif')
                    // ->get();
                    // foreach($pengguna_dosen as $pd)
                    // {
                    //     $email_pengguna[]=$pd->email_dosen;
                    // }
                    // Mail::to($email_pengguna)->send(new KonsultasiDosenWaliMail($data_konsultasi));


    				return redirect('admin/master/notifikasi')->with(['Success' => 'Berhasil Menambahkan Data']);
    			}
    			else
    			{
    				return redirect('admin/master/notifikasi/tambah')->with(['Error' => 'Harap menginputkan [tanggal mulai] lebih kecil dari [tanggal berakhir].']);
    			}            	
    		}
    		else
    		{
    			return redirect('admin/master/notifikasi/tambah')->with(['Error' => 'Harap menginputkan [tanggal mulai] dan [tanggal berakhir] mulai dari H+1.']);
    		}
    	} 
    	catch (Exception $e) 
    	{
    		$message= explode("in C:",$e);

            return redirect('admin/master/notifikasi/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
    	}	
    }

   

    public function ubahnotifikasi($id)
    {
    	if(Session::get('admin')!=null)
        {
        	$jadwal_konsultasi = DB::table('jadwal_konsultasi')
        	->select('*')
        	->where('idjadwalkonsultasi',$id)
        	->get();

        	return view('master_notifikasi.ubahnotifikasi_admin', compact('jadwal_konsultasi'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function ubahnotifikasi_proses(Request $request)
    {
    	try
    	{
    		
    		$tanggalmulai = $request->get('tanggal_mulai');
    		$tanggalberakhir = $request->get('tanggal_berakhir');
    		$keterangan = $request->get('keterangan');

    		$this->validate($request,[
            	'tanggal_mulai' =>'required',
            	'tanggal_berakhir' =>'required',
        	]);

    		if($tanggalmulai >= now() && $tanggalberakhir >= now())
    		{
    			if($tanggalmulai < $tanggalberakhir)
    			{
    				$jadwalkonsultasi = DB::table('jadwal_konsultasi')
                    ->where('idjadwalkonsultasi',$request->get('idjadwalkonsultasi'))
                    ->update([
                        'tanggalmulai'=>$tanggalmulai,
		                'tanggalberakhir'=>$tanggalberakhir,
		                'keterangan'=>$keterangan
                    ]);

    				return redirect('admin/master/notifikasi')->with(['Success' => 'Berhasil Mengubah Data (ID) '.$request->get('idjadwalkonsultasi') ]);
    			}
    			else
    			{
    				return redirect('admin/master/notifikasi/ubah/'.$request->get('idjadwalkonsultasi'))->with(['Error' => 'Harap menginputkan [tanggal mulai] lebih kecil dari [tanggal berakhir].']);
    			}            	
    		}
    		else
    		{
    			return redirect('admin/master/notifikasi/ubah/'.$request->get('idjadwalkonsultasi'))->with(['Error' => 'Harap menginputkan [tanggal mulai] dan [tanggal berakhir] mulai dari H+1.']);
    		}
    	}
    	catch(QueryException $e)
    	{
    		 $message= explode("in C:",$e);

            return redirect("admin/master/notifikasi")->with(['Error' => 'Gagal Mengubah Data '.$id."<br> Pesan Kesalahan: ".$message[0]]);
    	}
    }

    public function hapusnotifikasi ($id)
    {
        try
        {
            $hapus_jadwal = DB::table('jadwal_konsultasi')
                ->where('idjadwalkonsultasi',$id)
                ->delete();

            return redirect('admin/master/notifikasi')->with(['Success' => 'Berhasil Menghapus Data Konsultasi (ID) '.$id]);
        }

        catch(QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect("admin/master/notifikasi")->with(['Error' => 'Gagal Menghapus Data '.$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }

    // Bagian untuk pengiriman notifikasi konsultasi (otomatis)
    // note: letakan di index notifikasi
    public function contact_send()
    {
    	$data_konsultasi = DB::table('jadwal_konsultasi')
	    ->select(DB::raw("DATEDIFF(tanggalmulai,now()) as selisih"),'jadwal_konsultasi.*')
	    ->where ('status',0)
	    ->where(DB::raw("DATEDIFF(tanggalmulai,now())"),'<=',5)
	    ->limit(1)
	    ->get();

        foreach ($data_konsultasi as $d) 
        {
            if($d->selisih <= 5)
            {
                $jadwalkonsultasi = DB::table('jadwal_konsultasi')
                ->where('idjadwalkonsultasi', $d->idjadwalkonsultasi)
                ->update([
                    'status'=>1
                ]);


                $email_pengguna =array();
                $pengguna_mahasiswa = DB::table('mahasiswa')
                ->select('mahasiswa.email as email_mahasiswa')
                ->where('mahasiswa.status','aktif')
                ->get();
                foreach($pengguna_mahasiswa as $pm)
                {
                    $email_pengguna[]=$pm->email_mahasiswa;
                }

                $pengguna_dosen = DB::table('dosen')
                ->select('dosen.email as email_dosen')
                ->where('dosen.status','aktif')
                ->get();
                foreach($pengguna_dosen as $pd)
                {
                    $email_pengguna[]=$pd->email_dosen;
                }

                $when = now()->addDays($d->selisih-3);

                Mail::to($email_pengguna)->later($when,new KonsultasiDosenWaliMail($data_konsultasi));

                // Jangan lupa nyalakan: php artisan queue:listen
                return "Berhasil";
            }
            else
            {
                return "Gagal";
            }
        }	
    }
}
