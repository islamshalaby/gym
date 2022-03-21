<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use App\Setting;
use Cloudinary;

class SettingController extends AdminController{

    // get setting
    public function GetSetting(){
        $data['setting'] = Setting::find(1);
        return view('admin.setting' , ['data' => $data]);
    }

    // post setting
    public function PostSetting(Request $request){
        $setting = Setting::find(1);
        if($request->file('logo')){
            $logo = $request->file('logo')->getRealPath();
            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_logo = $image_id . '.' . $image_format;
            $setting->logo = $image_new_logo;
        }
        if($request->file('offer_image')){
            $offer_image = $request->file('offer_image')->getRealPath();
            $imagereturned = Cloudinary::upload($offer_image);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_logo = $image_id . '.' . $image_format;
            $setting->offer_image = $image_new_logo;
        }
        $setting->app_name_en = $request->app_name_en;
        $setting->app_name_ar = $request->app_name_ar;
        $setting->email = $request->email;
        $setting->phone = $request->phone;
        $setting->address_en = $request->address_en;
        $setting->address_ar = $request->address_ar;
        $setting->app_name_ar = $request->app_name_ar;
        $setting->facebook = $request->facebook;
        $setting->youtube = $request->youtube;
        $setting->twitter = $request->twitter;
        $setting->instegram = $request->instegram;
        $setting->snap_chat = $request->snap_chat;
        $setting->watsapp = $request->watsapp;
        $setting->map_url = $request->map_url;
        $setting->latitude = $request->latitude;
        $setting->longitude = $request->longitude;
        $setting->fax = $request->fax;
        $setting->post_address = $request->post_address;
		$setting->save();
        return  back();
    }
}
