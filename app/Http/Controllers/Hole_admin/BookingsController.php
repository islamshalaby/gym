<?php
namespace App\Http\Controllers\Hole_admin;
use App\Hole;
use App\Hole_booking;
use App\Hole_booking_detail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hole_branch;


class BookingsController extends Controller{

    // get all contact us messages
    public function index(){
        $id = auth()->guard('hole')->user()->id;
        $data = Hole_booking::where('deleted','0')->where('hole_id',$id)->get();
        return view('hole_admin.booking.index' ,compact('data','id'));
    }
    public function create(){
        return view('hole_admin.booking.create');
    }
    public function store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'name_ar' => 'required',
                'name_en' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'price' => 'required',
                'months_num' => 'required|numeric'
            ]);
        if($request->cb_discount == 'discount'){
            $this->validate(\request(),
                [
                    'discount' => 'required',
                    'discount_price' => 'required'
                ]);
        }
        $data['hole_id'] = auth()->guard('hole')->user()->id;
        if($request->cb_discount == 'discount'){
            $data['is_discount'] = 1;
            $data['discount'] = $request->discount;
            $data['discount_price'] = $request->discount_price;
        }
        $booking = Hole_booking::create($data);
        if($request->rows != null){
            foreach ($request->rows as $row) {
                if ($row['name_ar'] != null || $row['name_en'] != null) {
                    $details_data['name_ar'] = $row['name_ar'];
                    $details_data['name_en'] = $row['name_en'];
                    $details_data['booking_id'] = $booking->id;
                    Hole_booking_detail::create($details_data);
                }
            }
        }
        // update started price to hall
        $hole_booking =  Hole_booking::where('deleted','0')->where( 'hole_id' , auth()->guard('hole')->user()->id )->orderBy('price','asc')->first();
        $hole_discount_booking =  Hole_booking::where('deleted','0')->where( 'hole_id' , auth()->guard('hole')->user()->id )->orderBy('discount_price','asc')->first();

        if($hole_booking->price > $hole_discount_booking->discount_price){
            $new_price = $hole_discount_booking->discount_price;
        }else if($hole_booking->price < $hole_discount_booking->discount_price){
            $new_price = $hole_booking->price;
        }else{
            $new_price = $hole_booking->price;
        }
        if($new_price == 0){
            $new_price = $hole_booking->price;
        }

        $hole_data = Hole::where('id',auth()->guard('hole')->user()->id)->first();
        $hole_data->started_price = $new_price;
        $hole_data->save();
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('booking.index'));
    }
    public function store_detail(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'booking_id' => 'required',
                'name_ar' => 'required',
                'name_en' => 'required'
            ]);
        Hole_booking_detail::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }
    public function edit($id){
        $data = Hole_booking::where('id',$id)->first();
        return view('hole_admin.booking.edit',compact('data'));
    }
    public function update(Request $request,$id)
    {
        $data = $this->validate(\request(),
            [
                'name_ar' => 'required',
                'name_en' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'price' => 'required',
                'months_num' => 'required|numeric'
            ]);
        if($request->cb_discount == 'discount'){
            $this->validate(\request(),
                [
                    'discount' => 'required',
                    'discount_price' => 'required'
                ]);
        }
        $data['hole_id'] = auth()->guard('hole')->user()->id;
        if($request->cb_discount == 'discount'){
            $data['is_discount'] = 1;
            $data['discount'] = $request->discount;
            $data['discount_price'] = $request->discount_price;
        }else{
            $data['is_discount'] = 0;
        }
        Hole_booking::where('id',$id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('booking.index'));
    }
    public function make_common($id){
        $data['common'] = 0;
        Hole_booking::where('hole_id',auth()->guard('hole')->user()->id)->update($data);

        $booking = Hole_booking::where('id',$id)->first();
        $booking->common = 1;
        $booking->save();
        session()->flash('success', trans('messages.common_s'));
        return redirect()->back();
    }

    public function destroy($id){
        $data['deleted'] = '1';
        Hole_booking::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }

    public function destroy_detail($id){
        Hole_booking_detail::where('id',$id)->delete();
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }
}
