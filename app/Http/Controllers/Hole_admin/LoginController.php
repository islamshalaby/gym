<?php
namespace App\Http\Controllers\Hole_admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller{

//    public function __construct()
//    {
//        $this->middleware('auth:hole', ['except' => ['getlogin' , 'postlogin']]);
//    }
    // get login page
    public function getlogin(){
        if (\Auth::guard('admin')->check()) {
            return redirect('/admin-panel');
        }else if (\Auth::guard('hole')->check()) {
            return redirect(route('hall.home'));
        }else if (\Auth::guard('')->check()) {
            return redirect(route('shop.home'));
        } else {
            return view('hole_admin.login');
        }
    }

    // post login
    public function postlogin(Request $request){
        $credentials = request(['email', 'password']);
        if (Auth::guard('hole')->attempt($credentials)) {
            if(Auth::guard('hole')->user()->status == 'active'){
                $user = Auth::guard('hole')->user();
                return redirect(route('hall.home'));
            }else{
                session()->flash('success', trans('messages.you_are_not_active'));
                Auth::guard('hole')->logout();
                return view('hole_admin.login');
            }
        }else if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            return redirect('/admin-panel');
        } else if (Auth::guard('shop')->attempt($credentials)) {
            $user = Auth::guard('shop')->user();
            return redirect(route('shop.home'));
        }else {
            session()->flash('success', trans('messages.email_pass_invalied'));
            return view('hole_admin.login');
        }
    }

    public function logout(){
        $user = Auth::guard('hole')->user();
        Auth::guard('hole')->logout();
        return redirect('/login');
    }

}
