<?php
namespace App\Http\Controllers\Hole_admin;
use App\Hole;
use App\Hole_booking;
use App\Hole_booking_detail;
use App\Http\Controllers\Controller;
use App\Income;
use App\Reservation;
use App\Reservation_option;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Hole_branch;


class SubscribersController extends Controller{

    // get all contact us messages
    public function index($type){
        $id = auth()->guard('hole')->user()->id;
        $bookings = Hole_booking::where('hole_id',$id)->where('deleted','0')->get();
        $booking_ids = Hole_booking::where('hole_id',$id)->select('id')->get()->toArray();
        $data = Reservation::whereIn('booking_id',$booking_ids)->where('type','hall')->where('status',$type)->orderBy('created_at','asc')->get();

        return view('hole_admin.subscribers.index' ,compact('data','bookings'));
    }

    public function end($id){
        $data['status'] = 'ended';
        Reservation::where('id',$id)->update($data);
        session()->flash('success', trans('messages.status_changed'));
        return back();
    }

    public function resubscribe($id){
        $id = auth()->guard('hole')->user()->id;
        $data['status'] = 'ended';
        Reservation::where('id',$id)->update($data);
        session()->flash('success', trans('messages.status_changed'));
        return back();
    }

    public function user_data($id){
        $data['user'] = User::find($id);
        return view('hole_admin.subscribers.user_data' ,compact('data'));
    }

    public function re_new(Request $request){
        $reservation = Reservation::find($request->reserv_id);

        //to get booking monthes to generate expire date ...
        $booking = Hole_booking::find($request->booking_id);
        $mytime = Carbon::now();
        $today =  Carbon::parse($mytime->toDateTimeString())->format('Y-m-d H:i');
        $final_date = Carbon::createFromFormat('Y-m-d H:i', $today);
        $final_expire_date = $final_date->addMonths($booking->months_num);


        $data['user_id'] = $reservation->user_id ;
        $data['expire_date'] = $final_expire_date ;
        $data['booking_id'] = $request->booking_id ;
        $data['payment'] = 'cash' ;
        if($booking->is_discount == '1'){
            $data['price'] = $booking->discount_price ;
        }else{
            $data['price'] = $booking->price ;
        }
        $data['status'] = 'start';
        $create_reserv = Reservation::create($data);

        if($create_reserv  != null ){
            $reserv_options = Reservation_option::where('reservation_id',$request->reserv_id)->get();
            foreach ($reserv_options as $row){
                $res_option_data['reservation_id'] = $create_reserv->id;
                $res_option_data['goal_id'] = $row->goal_id;
                $res_option_data['type_id'] = $row->type_id;
                Reservation_option::create($res_option_data);
            }
            if($request->add_money == 'add_money'){
                if($booking->is_discount == '1'){
                    $income_Data['price'] = $booking->discount_price ;
                }else{
                    $income_Data['price'] = $booking->price ;
                }
                $income_Data['type'] = 'hall' ;
                $income_Data['user_id'] = $reservation->user_id ;
                $income_Data['reservation_id'] = $request->reserv_id ;
                $income_Data['booking_id'] = $request->booking_id ;
                Income::create($income_Data);
                session()->flash('success', trans('messages.reservation_re_new_money'));
                return back();
            }
        }
        session()->flash('success', trans('messages.reservation_re_new'));
        return back();
    }


}
