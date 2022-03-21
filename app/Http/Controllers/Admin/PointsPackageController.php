<?php
namespace App\Http\Controllers\Admin;
use App\Plan_details;
use App\Points_package;
use App\Setting;
use Illuminate\Http\Request;
use App\Plan;
class PointsPackageController extends AdminController{
    // show
    public function index() {
        $data = Points_package::where('deleted','0')->OrderBy('id','asc')->get();
        return view('admin.points_packages.index',compact('data'));
    }

    public function store(Request $request) {
        $data = $this->validate(\request(),
            [
                'points' => 'required|numeric',
                'price' => 'required|numeric'
            ]);
        Points_package::create($data);
        session()->flash('success', trans('messages.added_s'));
        return back();
    }

    public function update(Request $request) {
        $data = $this->validate(\request(),
            [
                'id' => 'required',
                'points' => 'required|numeric',
                'price' => 'required|numeric'
            ]);
        unset($data['id']);
        Points_package::where('id',$request->id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }
    public function update_points(Request $request) {
        $data = $this->validate(\request(),
            [
                'points' => 'required|numeric',
            ]);
        Setting::where('id',1)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }

    public function show($id) {

    }

    public function destroy($id) {
        $data['deleted'] = '1';
        Points_package::where('id',$id)->update($data);
        session()->flash('success', trans('messages.deleted_s'));
        return back();
    }

}
