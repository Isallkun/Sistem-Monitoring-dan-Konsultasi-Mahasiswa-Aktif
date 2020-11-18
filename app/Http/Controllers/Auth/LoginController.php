<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use DB;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $hasil = DB::table('users')
                ->where('username',$username)
                ->get();


        if (count($hasil) != 0)
        {
            $decrypted = Crypt::decryptString($hasil[0]->password);   
            
            if ($password == $decrypted)
            {
                if($hasil[0]->role_idrole == "1")
                {
                    Session::put('admin',$hasil[0]->username);
                    Session::put('profil','administrator.jpg');

                    return redirect('admin/');
                }

                else if($hasil[0]->role_idrole == "2")
                {
                    $hasil_profil = DB::table('dosen')
                                    -> where('users_username', $hasil[0]->username)
                                    -> get();

                    Session::put('dosen',$hasil[0]->username);
                    Session::put('profil',$hasil_profil[0]->profil);


                    return redirect('dosen/');    
                }   
                // else if($hasil[0]->role_idrole == "3")
                // {
                //     Session::put('mahasiswa',$hasil[0]->username);
                //     return view('...', compact('username'));    
                // }  
                else
                {
                    return redirect()->back()->with('Info', 'ID Role tidak dapat ditemukan adalam Database');
                } 
            }
            else
            { 
                return redirect()->back()->with('Info', 'Harap Periksa Password yang anda masukan');
            }
        }
        else
        {
            return redirect()->back()->with('Info', 'Periksa kembali username yang anda masukan');
        }   
    }

    public function logout()
    {
        if(Session::get('admin') !=null)
        {
            Session::forget('admin');
            Session::forget('profil');
        }
        
        if(Session::get('dosen') !=null)
        {
            Session::forget('dosen');
            Session::forget('profil');
        }
        
        if(Session::get('mahasiswa') !=null)
        {
            Session::forget('mahasiswa');
            Session::forget('profil');
        }

        return redirect("/");
        
    }
}
