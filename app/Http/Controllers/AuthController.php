<?php

namespace App\Http\Controllers;
use Auth;
use Hash;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function register()
    {
        return view('auth.register');
    }

    public function postregister(Request $request)
    {
        $validation = \Validator::make($request->all(),[
                'name'=> 'required',
                'email'=> 'required|email',
                'password' => 'required|min:6|confirmed',
                'nik' => 'required|max:16',
                'alamat' => 'required',
                'rekening' => 'required',
                'notelepon' => 'required|max:12',
                'role_id' => 'required',
        ])->validate();
        $user = new \App\User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = \Hash::make($request->get('password'));
        $user->remember_token = str_random(60);
        $user->role_id = $request->input('role_id');
        $user->save();
        //insert ke tabel biouser
        $request->request->add(['id_user'=>$user->id]);
        $biouser = \App\Biouser::create($request->all());
        // dd($biouser);
        return redirect()->route('login')->with('success','Data berhasil ditambahkan');
    }

    public function login(){
        return view('auth.login');
    }

    public function postlogin(Request $request){
        $user = $request->only('email','password');
       if(Auth::attempt($user)){
        // dd($user);
           return redirect('dashboard');
       }
       return redirect('/login')->with('message','Password atau Username anda keliru!!');
    }

    public function securitypassword(){
        return view('auth.securitypassword');
    }

    public function updatepassword(Request $request)
    {   
        // $validation = \Validator::make($request->all(),[
        //     'password' => 'required|min:6|confirmed',
        // ])->validate();
        
        $old_password = auth()->user()->password;
        $current_user = auth()->user()->id;
        if(Hash::check($request->input('old_password'),$old_password))
        {
           $user = User::find($current_user);
           $validatedData = $request->validate([
            'password' => 'min:6|confirmed',
        ]);
            $user->password = Hash::make($request->input('password'));
            if($user->save()){
                return redirect()->back()->with('success','Password Behasil di Ubah');
            }
            else{
                return redirect()->back()->with('failed','Password Lama Invalid');
            }
        }
        else{
            return redirect()->back()->with('failed','Password Lama Invalid');
        }
    }

    public function logout(){
        Auth::logout();
        return redirect('login');
    }
}
