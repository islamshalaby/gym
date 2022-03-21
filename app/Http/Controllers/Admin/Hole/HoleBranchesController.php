<?php
namespace App\Http\Controllers\Admin\Hole;
use App\Hole;
use App\Hole_branch;
use App\Hole_time_work;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JD\Cloudder\Facades\Cloudder;


class HoleBranchesController extends AdminController{

    public function show($id){

        $data = Hole_branch::where('hole_id',$id)->get();
        return view('hole.hole_users.branches.index' ,compact('data','id'));
    }
    public function create($id){
        return view('hole.hole_users.branches.create',compact('id'));
    }
    public function store(Request $request)
    {

        $data = $this->validate(\request(),
            [
                'hole_id' => 'required',
                'title_ar' => 'required',
                'title_en' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
        Hole_branch::create($data);
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('admin_branches.show',$request->hole_id));
    }
    public function edit($id){
        $data = Hole_branch::where('id',$id)->first();
        return view('hole.hole_users.branches.edit',compact('data'));
    }
    public function update(Request $request,$id)
    {
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);
        Hole_branch::where('id',$id)->update($data);
        $branch = Hole_branch::find($id);
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('admin_branches.show',$branch->hole_id));
    }
    public function destroy($id){
        Hole_branch::where('id',$id)->delete();
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }
}
