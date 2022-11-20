<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

  
    
    
    public function register()
    {
        return view('auth.register');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
            $code = rand(10000,99999);
            $email = $request['email'];
            $password = $request['password'];
            $moreUsers = "faezeh.pouya@gmail.com";
            Mail::to($request['email'])->send(new SendMail($code));
            $request->session()->put(['email'=>$email,'password'=>$password]);
            $request->session()->put('code',$code);
            
            return view('auth.code');
    }

    public function save(Request $request)
    {
        $code = \session()->get('code');
        if ($request['code'] == $code){
            $user=User::create([
                'email'=> \session()->get('email'),
                'password'=> \session()->get('password'),
            ]);
            $user->save();
            return 'Welcome.';
        }else{
            return 'The entered code is not correct.';
        }
        $request->session()->forget(['code']);
    }
}
