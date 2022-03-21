<?php
namespace App\Http\Controllers\Hole_admin;
use App\Hole_booking;
use App\Http\Controllers\Controller;
use App\Income;
use App\Reservation;
use Illuminate\Http\Request;

class PaymentsController extends Controller{

    // get all contact us messages
    public function index(){
        $id = auth()->guard('hole')->user()->id;
        $booking_ids = Hole_booking::where('hole_id',$id)->select('id')->get()->toArray();
        $reservation_ids = Reservation::whereIn('booking_id',$booking_ids)->select('id')->get()->toArray();
        $data = Income::whereIn('reservation_id',$reservation_ids)->where('type','hall')->orderBy('created_at','desc')->get();

        $bookings = Hole_booking::where('hole_id',$id)->get();
        return view('hole_admin.payments.index' ,compact('data','bookings'));
    }

    public function fetch_by_booking(Request $request) {
        $id = auth()->guard('hole')->user()->id;
        $reservation_ids = Reservation::where('booking_id',$request->booking_id)->select('id')->get()->toArray();
        $data = Income::whereIn('reservation_id',$reservation_ids)->where('type','hall')->orderBy('created_at','desc')->get();
        $bookings = Hole_booking::where('hole_id',$id)->get();
        return view('hole_admin.payments.index' ,compact('data','bookings'));
    }
}
