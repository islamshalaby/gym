<?php
namespace App\Http\Controllers\Shop_admin;

use App\Ad;
use App\Category;
use App\ContactUs;
use App\Http\Controllers\Controller;
use App\MainOrder;
use App\OrderItem;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Shop;

class HomeController extends Controller{

    // get all contact us messages
    public function home(){
        return view('shop_admin.home');
    }

    public function logout(){
        $user = Auth::guard('shop')->user();
        Auth::guard('shop')->logout();
        return redirect('/login');
    }

    // get profile
    public function profile(){
        $admin = Auth::guard('shop')->user();
        $data['name_ar'] = $admin->name_ar;
        $data['name_en'] = $admin->name_en;
        $data['email'] = $admin->email;
        return view('shop_admin.profile' , ['data' => $data]);
    }

    // update profile
    public function updateprofile(Request $request){
        $current_admin_id =  Auth::guard('shop')->user()->id;
        $check_manager_email = Shop::where('email' , $request->email)->where('id' , '!=' , $current_admin_id)->first();
        if($check_manager_email){
            return redirect()->back()->with('status' , 'Email Exists Before');
        }

        $current_manager = Shop::find($current_admin_id);
        $current_manager->name_ar = $request->name_ar;
        $current_manager->name_en = $request->name_en;
        $current_manager->email = $request->email;
        if($request->password){
            $current_manager->password = Hash::make($request->password);
        }
        $current_manager->save();
        session()->flash('success', trans('messages.updated_s'));
        return redirect()->back();
    }
}
