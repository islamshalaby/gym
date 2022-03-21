<?php

namespace App\Http\Controllers\Admin\Hole;

use App\Http\Controllers\Admin\AdminController;
use App\Reservation_goal;
use App\Reservation_type;
use Illuminate\Http\Request;

class ReservDataController extends AdminController
{

    public function types()
    {
        $data = Reservation_type::where('deleted','0')->get();
        return view('hole.reserv_data.index',compact('data'));
    }
    public function goals($id)
    {
        $data = Reservation_goal::where('type_id',$id)->where('deleted','0')->get();
        return view('hole.reserv_data.goals',compact('data','id'));
    }
    public function types_store(Request $request)
    {

        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required',
                'type' => 'required'
            ]);
        Reservation_type::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }
    public function types_update(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required',
                'type' => 'required'
            ]);
        Reservation_type::where('id',$request->id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }
    public function types_delete($id)
    {
        $data['deleted']='1';
        Reservation_type::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }


    public function goals_store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'type_id' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required'
            ]);
        Reservation_goal::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }
    public function goals_update(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required'
            ]);
        Reservation_goal::where('id',$request->id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }
    public function goals_delete($id)
    {
        $data['deleted']='1';
        Reservation_goal::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }
}
