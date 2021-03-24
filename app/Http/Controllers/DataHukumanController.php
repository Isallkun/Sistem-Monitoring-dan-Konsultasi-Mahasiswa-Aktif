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

            $mahasiswa_wali = DB::table('mahasiswa')
            ->select('mahasiswa.*','tahun_akademik.tahun')
            ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
            ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
            ->where('dosen.npkdosen',$dosen[0]->npkdosen)
            ->orderby('mahasiswa.nrpmahasiswa','ASC')
            ->get();
        	
            $data_hukuman = DB::table('hukuman')
            ->select('hukuman.*')
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
 
        	return view('data_hukuman.daftarmahasiswahukuman_dosen', compact('mahasiswa_wali','notifikasi_hukuman'));
        }
        else
        {
        	return redirect("/");
        }
    }

    public function tampilkan_filter(Request $request)
    {
        if(Session::get('dosen') != null)
        {
            $dosen = DB::table('users')
            ->join('dosen','dosen.users_username','=','users.username')
            ->where('users.username',Session::get('dosen'))
            ->get();

            
            $user_input=$request->get('filter');
            if($user_input == 'hukuman_mahasiswa')
            {
                $info='Hukuman mahasiswa (tinggi-rendah)';

                $mahasiswa_wali = DB::table('mahasiswa')
                ->select(DB::raw('COUNT(hukuman.idhukuman) as total_hukuman'),'mahasiswa.*','tahun_akademik.tahun')
                ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
                ->leftjoin('hukuman','hukuman.mahasiswa_nrpmahasiswa','=','mahasiswa.nrpmahasiswa')
                ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
                ->where('dosen.npkdosen',$dosen[0]->npkdosen)
                ->groupBy('mahasiswa.nrpmahasiswa')
                ->orderby('total_hukuman', 'DESC')
                ->get();

            }
            elseif($user_input == 'rating_mahasiswa')
            {
                $info='Rating mahasiswa (rendah-tinggi)';

                $mahasiswa_wali = DB::table('mahasiswa')
                ->select('mahasiswa.*','tahun_akademik.tahun', 'gamifikasi.total')
                ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
                ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
                ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
                ->where('dosen.npkdosen',$dosen[0]->npkdosen)
                ->orderby('gamifikasi.total', 'ASC')
                ->get();

            }
            elseif($user_input == 'konsultasi_mahasiswa')
            {
                $info='Konsultasi mahasiswa (rendah-tinggi)';

                $mahasiswa_wali = DB::table('mahasiswa')
                ->select(DB::raw('COUNT(konsultasi_dosenwali.idkonsultasi) as total_konsultasi'),'mahasiswa.*','tahun_akademik.tahun')
                ->join('dosen','dosen.npkdosen','=','mahasiswa.dosen_npkdosen')
                ->leftjoin('konsultasi_dosenwali','konsultasi_dosenwali.mahasiswa_nrpmahasiswa','=','mahasiswa.nrpmahasiswa')
                ->join('tahun_akademik','tahun_akademik.idtahunakademik','=','mahasiswa.thnakademik_idthnakademik')
                ->where('dosen.npkdosen',$dosen[0]->npkdosen)
                ->groupBy('mahasiswa.nrpmahasiswa')
                ->orderby('total_konsultasi', 'ASC')
                ->get();

            }
            
            
            $data_hukuman = DB::table('hukuman')
            ->select('hukuman.*')
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

            return view('data_hukuman.daftarmahasiswahukuman_dosen', compact('mahasiswa_wali','notifikasi_hukuman','info'));
        }
        else
        {
            return redirect("/");
        }
    }

    public function detailhukuman ($id)
    {
        if(Session::get('dosen') != null)
        {
            //Data Mahasiswa
            $mahasiswa = DB::table('mahasiswa')
            ->select('mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','gamifikasi.total AS total_rate')
            ->join('gamifikasi','gamifikasi.idgamifikasi','=','mahasiswa.gamifikasi_idgamifikasi')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->get();


            //Mendapatkan total hukuman
            $hukuman_mahasiswa = DB::table('hukuman')
            ->where('hukuman.mahasiswa_nrpmahasiswa',$id)
            ->count();
            //Mendapatkan total konsultasi 
            $konsultasi_mahasiswa = DB::table('konsultasi_dosenwali')
            ->where('konsultasi_dosenwali.mahasiswa_nrpmahasiswa',$id)
            ->count();


            $data_hukuman = DB::table('hukuman')
            ->select('hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa','dosen.npkdosen','dosen.namadosen')
            ->join('dosen','dosen.npkdosen','=','hukuman.dosen_npkdosen')
            ->join('mahasiswa','mahasiswa.nrpmahasiswa','=','hukuman.mahasiswa_nrpmahasiswa')
            ->where('mahasiswa.nrpmahasiswa',$id)
            ->orderby('tanggalinput','DESC')
            ->groupBy('idhukuman')
            ->get();

            return view('data_hukuman.detailmahasiswahukuman_dosen', compact('mahasiswa','hukuman_mahasiswa','konsultasi_mahasiswa','data_hukuman'));
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

            return redirect()->back();
    	}
    	else
    	{
    		return redirect("/");
    	}
    }

    public function tambahhukuman($id)
    {
        if(Session::get('dosen') != null)
        {
            $mahasiswa = DB::table('mahasiswa')
            ->select('*')
            ->where("mahasiswa.nrpmahasiswa",$id)
            ->get();

            return view('data_hukuman.tambahhukuman_dosen', compact('mahasiswa'));
        }
        else
        {
            return redirect("/");
        }
    }

    function fetch(Request $request)
    {
        $query = $request->get('query');
        $pencarian =$request->get('jenis');

        if($request->get('query') && $request->get('jenis'))
        {           
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';    

            if($pencarian == 'ringan')
            {
                $jenishukuman = DB::table('jenis_hukuman')
                ->select('*')
                ->where('kategori', $pencarian)
                ->where('namahukuman', 'LIKE', "%{$query}%")
                ->get();
            
                foreach($jenishukuman as $row)
                {
                    $output .= '<li><a href="#">'.$row->namahukuman.'</a></li>';
                }
            } 
            elseif($pencarian == 'sedang')
            {
                $jenishukuman = DB::table('jenis_hukuman')
                ->select('*')
                ->where('kategori', $pencarian)
                ->where('namahukuman', 'LIKE', "%{$query}%")
                ->get();
            
                foreach($jenishukuman as $row)
                {
                    $output .= '<li><a href="#">'.$row->namahukuman.'</a></li>';
                }
            } 
            elseif($pencarian == 'berat')
            {
                $jenishukuman = DB::table('jenis_hukuman')
                ->select('*')
                ->where('kategori', $pencarian)
                ->where('namahukuman', 'LIKE', "%{$query}%")
                ->get();
            
                foreach($jenishukuman as $row)
                {
                    $output .= '<li><a href="#">'.$row->namahukuman.'</a></li>';
                }
            } 
         
            $output .= '</ul>';
            echo $output;
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
            $kategori = $request->get('kategori');
            $hukuman = $request->get('hukuman');
            $mahasiswa= $request->get('mahasiswa');
            $keterangan = $request->get('keterangan');


            $this->validate($request,[
                'kategori' => 'required',
                'hukuman' => 'required|max:50',
                'keterangan'=>'max:100'               
            ]);


            $tambahdata_hukuman = Hukuman::insert([
                'tanggalinput' =>$tanggal_sekarang,
                'kategori' => $kategori,
                'namahukuman' => $hukuman,
                'keterangan' =>$keterangan,
                'status'=>0,
                'dosen_npkdosen'=>$dosen[0]->npkdosen,
                'mahasiswa_nrpmahasiswa'=>$mahasiswa
            ]);



             return redirect('dosen/data/hukuman/detail/'.$mahasiswa)->with(['Success' => 'Berhasil Menambahkan Data Hukuman Mahasiswa ('. $mahasiswa.')']);
        }

        catch (QueryException $e)
        {
            $message= explode("in C:",$e);

            return redirect('dosen/data/hukuman/detail/tambah/'.$mahasiswa)->with(['Error' => 'Gagal Menambahkan Data Kedalam Database <br> Pesan Kesalahan: '.$message[0]]);
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
                'kategori' => 'required',
                'hukuman' => 'required|max:50',
                'keterangan'=>'max:100'               
            ]);


            $hukuman = DB::table('hukuman')
            ->where('idhukuman',$request->get('idhukuman'))
            ->update([
                'kategori'=> $request->get('kategori'),
                'namahukuman' =>$request->get('hukuman'),
                'keterangan' => $request->get('keterangan')
            ]);

            return redirect('dosen/data/hukuman/detail/'.$request->get('mahasiswa'))->with(['Success' => 'Berhasil Mengubah Data Hukuman (ID) '.$request->get('idhukuman')]);
        }
        catch(QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/hukuman/detail/ubah/{$request->get('idhukuman')}")->with(['Error' => 'Gagal Mengubah Data Hukuman (ID) '.$request->get('idhukuman')."<br> Pesan Kesalahan: ".$message[0]]);
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

            return redirect('dosen/data/hukuman/detail/'.$request->get('mahasiswa'))->with(['Success' => 'Berhasil Menghapus Data Hukuman (ID) '.$id]);

        }
        catch (QueryException $e)
        {
            $message= explode("in C:",$e);
            
            return redirect("dosen/data/hukuman/detail/".$request->get('mahasiswa'))->with(['Error' => 'Gagal Menghapus Data Hukuman (ID) '.$id."<br> Pesan Kesalahan: ".$message[0]]);
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
            ->select(DB::raw("DATEDIFF(masaberlaku,now())AS total"),'hukuman.*', 'mahasiswa.namamahasiswa','mahasiswa.nrpmahasiswa', 'dosen.namadosen','dosen.npkdosen')
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
            ->where('nrpmahasiswa', $mahasiswa[0]->nrpmahasiswa)
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
                    $hukuman = DB::table('hukuman')
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
        if(Session::get('mahasiswa') != null || Session::get('dosen') != null)
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
        }
        
        else
        {
            return redirect("/");
        }
    }
}
