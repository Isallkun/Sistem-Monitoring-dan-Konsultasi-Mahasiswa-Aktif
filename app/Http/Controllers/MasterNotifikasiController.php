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




    public function contact_sent()
	{
		if(Session::get('admin')!=null)
        {  

			$data_email =array();

			$dosen = DB::table('dosen')
	        ->select('dosen.*','jurusan.namajurusan')
	        ->join('jurusan', 'jurusan.idjurusan', '=', 'dosen.jurusan_idjurusan')
	        ->where('status','aktif')
	        ->get();
	        
	        foreach($dosen as $d)
	        {
	        	$data_email[]=$d->email;
	        }

	        Mail::to($data_email)->send(new KonsultasiDosenWaliMail());
	        return "Email telah dikirim";	
	    } 
	    else
        {
            
        }   
	}
}
