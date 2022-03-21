<?php

namespace App\Http\Controllers\Admin\Coach;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Coach_time_work;

class Hall_time_worksController extends AdminController
{

    public function index($id)
    {
        $data = Coach_time_work::where( 'coach_id' , $id )->get();
        return view('coach.coach_users.coach_times.index',compact('data','id'));
    }
    public function create($id)
    {
        return view('coach.coach_users.coach_times.create',compact('id'));
    }

    public function store(Request $request)
    {

        $data = $this->validate(\request(),
            [
                'time_from' => 'required',
                'time_to' => 'required',
                'coach_id' => 'required'
            ]);
        Coach_time_work::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }
    public function edit($id)
    {
        $data = Coach_time_work::where('id',$id)->first();
        return view('coach.coach_users.coach_times.edit',compact('data'));
    }
    public function update(Request $request,$id)
    {
        $data = $this->validate(\request(),
            [
                'time_from' => 'required',
                'time_to' => 'required'
            ]);
        Coach_time_work::where('id',$id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }

    public function destroy($id)
    {
        Coach_time_work::where('id',$id)->delete();
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }
}
