<?php

namespace App\Http\Controllers\Admin\Coach;

use App\Coach;
use App\Coach_time_work;
use App\CoachNotification;
use App\Day;
use App\Helpers\APIHelpers;
use App\Hole;
use App\Http\Controllers\Admin\AdminController;
use App\Notification;
use App\Rate;
use App\User;
use App\User_caoch_ask;
use App\UserNotification;
use App\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Cloudinary;
use JD\Cloudder\Facades\Cloudder;

class NotificationController extends AdminController
{
    // get all notifications
    public function show(){
        $data['notifications'] = Notification::where('type','coach')->orderBy('id' , 'desc')->get();
        return view('coach.notifications.index' , ['data' => $data]);
    }

    // get notification details
    public function details(Request $request){
        $data['notification'] = Notification::find($request->id);
        return view('coach.notifications.show' , ['data' => $data]);
    }

    // delete notification
    public function delete(Request $request){
        $notification = Notification::find($request->id);
        if($notification){
            $notification->delete();
        }
        return redirect('admin-panel/coach_notifications/show');
    }

    // type : get - get send notification page
    public function getsend(){
        return view('coach.notifications.create');
    }

    // send notification and insert it in database
    public function send(Request $request){
        $notification = new Notification();
        if($request->file('image')){
            $logo = $request->file('image')->getRealPath();
            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_logo = $image_id . '.' . $image_format;
            $notification->image = $image_new_logo;
//            $image_name = $request->file('image')->getRealPath();
//            Cloudder::upload($image_name, null);
//            $imagereturned = Cloudder::getResult();
//            $image_id = $imagereturned['public_id'];
//            $image_format = $imagereturned['format'];
//            $image_new_name = $image_id.'.'.$image_format;
//            $notification->image = $image_new_name;
        }else{
            $notification->image = null;
        }
        $notification->title = $request->title;
        $notification->body = $request->body;
        $notification->type = 'coach';
        $notification->save();
        $users = Coach::where('deleted','0')->where('fcm_token' ,'!=' , null)->get();
        for($i =0; $i < count($users); $i++){
            $fcm_tokens[$i] = $users[$i]['fcm_token'];
            $user_notification = new CoachNotification();
            $user_notification->coach_id = $users[$i]['id'];
            $user_notification->notification_id = $notification->id;
            $user_notification->save();
        }
        $the_image = "https://res.cloudinary.com/dsibvtsiv/image/upload/w_100,q_100/v1581928924/".$notification->image;
        APIHelpers::send_notification($notification->title , $notification->body , $the_image , null , $fcm_tokens);
        return redirect('admin-panel/coach_notifications/show');
    }

    // resend notifications
    public function resend(Request $request){
        $notification_id = $request->id;
        $notification = Notification::find($notification_id);
        $users_tokens= Coach::select('fcm_token')->where('deleted','0')->where('fcm_token' ,'!=' , null)->get();
        $array_values = array_values((array)$users_tokens);
        $array_values = $array_values[0];
        APIHelpers::send_notification($notification->title , $notification->body , $notification->image , null , $array_values);
        session()->flash('success', trans('messages.sent_s'));
        return redirect()->back();
    }
}
