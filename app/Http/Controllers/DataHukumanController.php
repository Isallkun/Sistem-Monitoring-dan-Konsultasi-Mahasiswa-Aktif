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
                if(Carbon::now() >= $d->masaberlaku)
                {
                    $hukuman = DB::table('hukuman') 
                    ->where('idhukuman',$d->idhukuman)
                    ->update([
                        'status' => 2
                    ]);   
                }
            }

        	return view('data_hukuman.daftarhukuman_dosen', compact('data_hukuman'));
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
}
