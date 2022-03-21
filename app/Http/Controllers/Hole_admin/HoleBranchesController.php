<?php
namespace App\Http\Controllers\Hole_admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hole_branch;


class HoleBranchesController extends Controller{

    // get all contact us messages
    public function index(){
        $id = auth()->guard('hole')->user()->id;
        $data = Hole_branch::where('hole_id',$id)->get();
        return view('hole_admin.branches.index' ,compact('data','id'));
    }
    public function create(){
        return view('hole_admin.branches.create');
    }
    public function store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'title_ar' => 'required',
                'title_en' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
        $data['hole_id'] = auth()->guard('hole')->user()->id;
        Hole_branch::create($data);
        session()->flash('success', trans('messages.added_s'));
        return redirect( route('branches.index'));
    }
    public function edit($id){
        $data = Hole_branch::where('id',$id)->first();
        return view('hole_admin.branches.edit',compact('data'));
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
        return redirect( route('branches.index'));
    }
    public function destroy($id){
        Hole_branch::where('id',$id)->delete();
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }
}
