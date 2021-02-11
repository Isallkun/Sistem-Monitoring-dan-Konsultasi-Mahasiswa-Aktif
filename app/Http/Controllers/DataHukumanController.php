<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use DB;
use Session;
use Carbon\Carbon;
use File;
use ZipArchive;

use App\User;
use App\Mahasiswa;
use App\Dosen;
use App\Hukuman;
use App\Berkas_hukuman;

class DataHukumanController extends Controller
{
    public function __construct()
    {
        $this->middleware('revalidate');
    }

    public function daftarhukuman()
    {
    	if(Session::get('dosen') != null)
        {
        	$dosen = DB::table('users')
        	->join('dosen','dosen.users_username','=','users.username')
        	->where('users.username',Session::get('dosen'))
        	->get();

        	$data_hukuman = DB::table('hukuman')
        	->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
        	->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
        	->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
        	->where('npkdosen', $dosen[0]->npkdosen)
            ->orderby('tanggalinput','DESC')
        	->groupBy('idhukuman')
        	->get();

            foreach ($data_hukuman as $d)
            {
                if($d->masaberlaku <= Carbon::now())
                {
                    $hukuman = DB::table('hukuman') 
                    ->where('idhukuman',$d->idhukuman)
                    ->update([
                        'status' => 2
                    ]);   
                    
                }
                if($d->masaberlaku == null)
                {
                    $hukuman = DB::table('hukuman') 
                    ->where('idhukuman',$d->idhukuman)
                    ->update([
                        'status' => 0
                    ]);  
                }
            }

            $notifikasi_hukuman = DB::table('hukuman')
            ->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('npkdosen', $dosen[0]->npkdosen)
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->whereNotNull('masaberlaku')
            ->get();

        	return view('data_hukuman.daftarhukuman_dosen', compact('data_hukuman','notifikasi_hukuman'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function detailhukuman($id)
    {
    	if(Session::get('dosen') != null)
    	{
            $data_hukuman = DB::table('hukuman')
            ->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('idhukuman', $id)
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->get();

    		$data_detail_hukuman = DB::table('hukuman')
        	->select("berkas_hukuman.*","hukuman.*",'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
        	->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
        	->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
        	->join('berkas_hukuman','berkas_hukuman.hukuman_idhukuman','=','hukuman.idhukuman')
        	->where('idhukuman',$id)
        	->get();

        	return view('data_hukuman.detailhukuman_dosen', compact('data_hukuman','data_detail_hukuman'));
    	}
    	else
    	{
    		return redirect("/");
    	}
    }


    public function ubahnilai($id)
    {
    	if(Session::get('dosen') != null)
    	{
    		$nilai_hukuman = DB::table('hukuman')
            ->select('penilaian')
            ->where('idhukuman', $id)
            ->get();

            if($nilai_hukuman[0]->penilaian == null)
            {
            	$hukuman = DB::table('hukuman') 
                ->where('idhukuman',$id)
                ->update([
                	'penilaian' => 'kurang'
                ]);   
            }
            else if($nilai_hukuman[0]->penilaian == 'kurang')
            {
            	$hukuman = DB::table('hukuman') 
                ->where('idhukuman',$id)
                ->update([
                	'penilaian' => 'cukup'
                ]);
            }
            else if($nilai_hukuman[0]->penilaian == 'cukup')
            {
            	$hukuman = DB::table('hukuman') 
                ->where('idhukuman',$id)
                ->update([
                	'penilaian' => 'baik'
                ]);
            	
            }
            else if($nilai_hukuman[0]->penilaian == 'baik')
            {
            	$hukuman = DB::table('hukuman') 
                ->where('idhukuman',$id)
                ->update([
                	'penilaian' => 'kurang'
                ]);
            }

            return redirect('dosen/data/hukuman');
    	}
    	else
    	{
    		return redirect("/");
    	}
    }

    public function tambahhukuman()
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

            return view('data_hukuman.tambahhukuman_dosen', compact('mahasiswa'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function tambahhukuman_proses(Request $request)
    {
        try
        {
            $dosen = DB::table('users')
            ->join('dosen','dosen.users_username','=','users.username')
            ->where('users.username',Session::get('dosen'))
            ->get();

            $tanggal_sekarang=Carbon::now()->format('Y/m/d');
            $mahasiswa= $request->get('mahasiswa');
            $keterangan = $request->get('keterangan');


            $this->validate($request,[
                'mahasiswa' =>'required',
                'keterangan'=>'required'               
            ]);


            $tambahdata_hukuman = Hukuman::insert([
                'tanggalinput' =>$tanggal_sekarang,
                'keterangan' =>$keterangan,
                'status'=>0,
                'dosen_npkdosen'=>$dosen[0]->npkdosen,
                'mahasiswa_nrpmahasiswa'=>$mahasiswa
            ]);

             return redirect('dosen/data/hukuman')->with(['Success' => 'Berhasil Menambahkan Data Hukuman Mahasiswa ('. $mahasiswa.')']);
        }

        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('dosen/data/hukuman/tambah')->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
        }
    }

    public function ubahhukuman($id)
    {
        if(Session::get('dosen') != null)
        {
            $hukuman = DB::table("hukuman")
            ->select('mahasiswa.nrpmahasiswa','mahasiswa.namamahasiswa', 'hukuman.*')
            ->join('mahasiswa', 'mahasiswa.nrpmahasiswa', '=', 'hukuman.mahasiswa_nrpmahasiswa')
            ->where('hukuman.idhukuman',$id)
            ->get();

            return view('data_hukuman.ubahhukuman_dosen',compact('hukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function ubahhukuman_proses(Request $request)
    {
        try
        {
            $this->validate($request,[
                'keterangan'=>'required'               
            ]);

             $hukuman = DB::table('hukuman')
            ->where('idhukuman',$request->get('idhukuman'))
            ->update([
                'keterangan' => $request->get('keterangan')
            ]);

            return redirect('dosen/data/hukuman')->with(['Success' => 'Berhasil Mengubah Data Hukuman (ID) '.$request->get('idhukuman')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/hukuman/ubah/{$request->get('idhukuman')}")->with(['Error' => 'Gagal Mengubah Data Hukuman (ID) '.$request->get('idhukuman')."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }

    public function hapushukuman(Request $request,$id)
    {
        try
        {
            $total_hukuman = DB::table('hukuman')
            ->join('berkas_hukuman','berkas_hukuman.hukuman_idhukuman','=','hukuman.idhukuman')
            ->where('idhukuman',$id)
            ->count();

            $berkas_hukuman = DB::table('hukuman')
            ->select('berkas_hukuman.berkas')
            ->join('berkas_hukuman','berkas_hukuman.hukuman_idhukuman','=','hukuman.idhukuman')
            ->where('idhukuman',$id)
            ->get();

            if($total_hukuman != 0)
            {
                $hapus_berkas = DB::table('berkas_hukuman')
                ->where('hukuman_idhukuman',$id)
                ->delete();

                foreach ($berkas_hukuman as $b) 
                {
                    File::delete('data_hukuman/'.$b->berkas);
                }
            }
           
            $hapus_hukuman = DB::table('hukuman')
            ->where('idhukuman',$id)
            ->delete();
            
            return redirect('dosen/data/hukuman')->with(['Success' => 'Berhasil Menghapus Data Hukuman (ID) '.$id]);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/hukuman/")->with(['Error' => 'Gagal Menghapus Data Hukuman (ID) '.$id."<br> Pesan Kesalahan: ".$message[0]]);
        }
    }


    public function daftarhukuman_mahasiswa()
    {
        if(Session::get('mahasiswa') != null)
        {
            $mahasiswa = DB::table('users')
            ->join('mahasiswa','mahasiswa.users_username','=','users.username')
            ->where('users.username',Session::get('mahasiswa'))
            ->get();

            $data_hukuman = DB::table('hukuman')
            ->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa', 'dosen.namadosen')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('nrpmahasiswa', $mahasiswa[0]->nrpmahasiswa)
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->get();

            foreach ($data_hukuman as $d)
            {
                if($d->masaberlaku <= Carbon::now())
                {
                    $hukuman = DB::table('hukuman') 
                    ->where('idhukuman',$d->idhukuman)
                    ->update([
                        'status' => 2
                    ]);   
                    
                }
                if($d->masaberlaku == null)
                {
                    $hukuman = DB::table('hukuman') 
                    ->where('idhukuman',$d->idhukuman)
                    ->update([
                        'status' => 0
                    ]);  
                }
            }

            $notifikasi_hukuman = DB::table('hukuman')
            ->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('npkdosen', $mahasiswa[0]->nrpmahasiswa)
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->whereNotNull('masaberlaku')
            ->get();


            return view('data_hukuman.daftarhukuman_mahasiswa', compact('data_hukuman','notifikasi_hukuman'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function unggahberkas_proses(Request $request, $id)
    {
        if(Session::get('mahasiswa') != null)
        {
            try
            {
                $this->validate($request,[
                'berkas.*' =>'required|file|max:2000'
                ]);

                $nrpmahasiswa = $request->get('nrpmahasiswa');
                $total = count($_FILES['berkas']['name']);

                if($total <="2")
                {
                    foreach ($_FILES['berkas']['name'] as $key => $value) 
                    {
                        $name_file = time()."_".$nrpmahasiswa."_".$value;

                        $tambahdata_berkashukuman= Berkas_hukuman::insert([
                           'berkas'=>$name_file, 
                           'hukuman_idhukuman'=>$id
                        ]);
                    }

                    for($i=0; $i < $total; $i++)
                    {
                        $filename =time()."_".$nrpmahasiswa."_".$_FILES['berkas']['name'][$i];
                        move_uploaded_file($_FILES['berkas']['tmp_name'][$i],'data_hukuman/'.$filename);
                    }

                    //Lakukan update data hukuman
                    $pengguna = DB::table('hukuman')
                        ->where('idhukuman',$id)
                        ->update([
                            'status' => 1,
                            'tanggalkonfirmasi' => Carbon::now(),
                            'masaberlaku' =>Carbon::now()->addMonths(6)
                    ]);

                    return redirect("mahasiswa/data/hukumanmahasiswa")->with(['Success' => 'Berhasil mengunggah berkas hukuman']);
                }
                else
                {
                    return redirect("mahasiswa/data/hukumanmahasiswa")->with(['Error' => 'Berkas yang diunggah tidak boleh lebih dari 2 (dua) berkas.']);
                }
            }
            catch (QueryException $e)
            {
                return redirect("mahasiswa/data/hukumanmahasiswa")->with(['Error' => 'Mohon maaf, sistem gagal mengunggah berkas hukuman']);
            }
        }
        else
        {
            return redirect("/");
        }
    }

    public function unduhberkas_proses(Request $request, $id)
    {
        if(Session::get('mahasiswa') != null)
        {
            $nrpmahasiswa = $request->get('nrpmahasiswa');

            $berkas_hukuman = DB::table('hukuman')
            ->select('berkas_hukuman.berkas')
            ->join('berkas_hukuman','berkas_hukuman.hukuman_idhukuman','=','hukuman.idhukuman')
            ->where('idhukuman',$id)
            ->where('mahasiswa_nrpmahasiswa',$nrpmahasiswa)
            ->get();


            //Load ZIP Library  
            $zip = new ZipArchive;
            $zipname = time()."_".$nrpmahasiswa.".zip";

            //Membuat File ZIP
            if ($zip->open(public_path($zipname), ZipArchive::CREATE) === TRUE)
            {
                $files = File::files(public_path('data_hukuman'));
       
                foreach ($berkas_hukuman as $key => $value) 
                {
                    $zip->addFile("data_hukuman/".$value->berkas);
                }
                 
                $zip->close();
            } 
           


            //Mengunduh File ZIP yang telah dibentuk
            if(file_exists($zipname))
            {
                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename="'.$zipname.'"');
                header('Content-Length: ' . filesize($zipname));
                readfile($zipname);
                unlink($zipname);
            } 
            else
            {
                $informasi = "Proses mengkompresi file gagal";
            } 
             
             // return redirect("mahasiswa/data/hukumanmahasiswa");   
        }
        else
        {
            return redirect("/");
        }
    }
}
