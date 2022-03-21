<?php
namespace App\Http\Controllers\Admin\Hole;
use App\Hole;
use App\Hole_branch;
use App\Hole_time_work;
use Cloudinary;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JD\Cloudder\Facades\Cloudder;

class HoleController extends AdminController{

    // get all contact us messages
    public function index(){
        $data = Hole::where('deleted','0')->orderBy('sort' , 'asc')->get();
        return view('hole.hole_users.index',compact('data'));
    }

    public function famous_holes(){
        $data = Hole::where('famous','1')->where('deleted','0')->orderBy('sort' , 'asc')->get();
        return view('hole.hole_users.index',compact('data'));
    }

    public function create(){
        return view('hole.hole_users.create');
    }
    public function edit($id)
    {
        $data = Hole::where('id',$id)->first();
        $time_male = Hole_time_work::where('hole_id',$id)->where('type','male')->first();
        $time_female = Hole_time_work::where('hole_id',$id)->where('type','female')->first();
        $time_mix = Hole_time_work::where('hole_id',$id)->where('type','mix')->first();
        $branches = Hole_branch::where('hole_id',$id)->get();
        return view('hole.hole_users.edit',compact('data','time_male','time_female','time_mix','branches'));
    }

    public function store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'name' => 'required',
                'name_en' => 'required',
                'email' => 'required|unique:holes|unique:users|unique:admins',
                'phone' => 'required|numeric',
                'about_hole' => '',
                'about_hole_en' => '',
                'password' => 'required|numeric',
                'logo' => 'required',
                'cover' => 'required',
            ]);
        if ($request->male == 'male') {
            $this->validate(\request(),
                [
                    'male_hole_from' => 'required',
                    'male_hole_to' => 'required'
                ]);
        }
        if ($request->female == 'female') {
            $this->validate(\request(),
                [
                    'female_hole_from' => 'required',
                    'female_hole_to' => 'required'
                ]);
        }
        if ($request->mix == 'mix') {
            $this->validate(\request(),
                [
                    'mix_hole_from' => 'required',
                    'mix_hole_to' => 'required'
                ]);
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->logo != null) {
            $logo = $request->file('logo')->getRealPath();

            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_logo = $image_id . '.' . $image_format;
            $data['logo'] = $image_new_logo;
        }
        if ($request->cover != null) {
            $logo = $request->file('cover')->getRealPath();

            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_cover = $image_id . '.' . $image_format;
            $data['cover'] = $image_new_cover;
        }
        $hole = Hole::create($data);
//
//        if($request->branches != null){
//            foreach ($request->branches as $row) {
//                if ($row['title_ar'] != null && $row['title_en'] != null && $row['longitude'] != null && $row['latitude'] != null) {
//                    $row['hole_id'] = $hole->id;
//                    Hole_branch::create($row);
//                }
//            }
//        }
        if ($request->male == 'male') {
            $male_data['time_from'] = $request->male_hole_from;
            $male_data['time_to'] = $request->male_hole_to;
            $male_data['type'] = 'male';
            $male_data['hole_id'] = $hole->id;
            Hole_time_work::create($male_data);
        }
        if ($request->female == 'female') {
            $male_data['time_from'] = $request->female_hole_from;
            $male_data['time_to'] = $request->female_hole_to;
            $male_data['type'] = 'female';
            $male_data['hole_id'] = $hole->id;
            Hole_time_work::create($male_data);
        }
        if ($request->mix == 'mix') {
            $male_data['time_from'] = $request->mix_hole_from;
            $male_data['time_to'] = $request->mix_hole_to;
            $male_data['type'] = 'mix';
            $male_data['hole_id'] = $hole->id;
            Hole_time_work::create($male_data);
        }
        session()->flash('success', trans('messages.added_s'));
        return redirect(route('halls.show'));
    }
    public function update(Request $request , $id)
    {
        $data = $this->validate(\request(),
            [
                'name' => 'required',
                'name_en' => 'required',
                'email' => 'required',
                'phone' => 'required|numeric',
                'about_hole' => '',
                'about_hole_en' => '',
            ]);
        if($request->male == 'male'){
            $this->validate(\request(),
                [
                    'male_hole_from' => 'required',
                    'male_hole_to' => 'required'
                ]);
        }
        if($request->female == 'female'){
            $this->validate(\request(),
                [
                    'female_hole_from' => 'required',
                    'female_hole_to' => 'required'
                ]);
        }
        if($request->mix == 'mix'){
            $this->validate(\request(),
                [
                    'mix_hole_from' => 'required',
                    'mix_hole_to' => 'required'
                ]);
        }

        if($request->password){
            $data['password'] = Hash::make($request->password);
        }
        if($request->logo != null){
            $logo = $request->file('logo')->getRealPath();

            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_logo = $image_id.'.'.$image_format;
            $data['logo'] = $image_new_logo ;
        }
        if($request->cover != null){
            $logo = $request->file('cover')->getRealPath();
            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_cover = $image_id.'.'.$image_format;
            $data['cover'] = $image_new_cover ;
        }
        $hole = Hole::where('id',$id)->update($data);
        if($request->male == 'male'){
            $male_time = Hole_time_work::where('hole_id',$id)->where('type','male')->first();
            if($male_time == null){
                $male_data['time_to'] = $request->male_hole_to;
                $male_data['type'] = 'male';
                $male_data['time_from'] = $request->male_hole_from;
                $male_data['hole_id'] = $id ;
                Hole_time_work::create($male_data);
            }else{
                $male_update_data['time_to'] = $request->male_hole_to;
                $male_update_data['time_from'] = $request->male_hole_from;
                Hole_time_work::where('hole_id',$id)->where('type','male')->update($male_update_data);
            }
        }else{
            $male_time = Hole_time_work::where('hole_id',$id)->where('type','male')->first();
            if($male_time != null){
                $male_time->delete();
            }
        }
        if($request->female == 'female'){
            $female_time = Hole_time_work::where('hole_id',$id)->where('type','female')->first();
            if($female_time == null){
                $female_data['time_to'] = $request->female_hole_to;
                $female_data['type'] = 'female';
                $female_data['time_from'] = $request->female_hole_from;
                $female_data['hole_id'] = $id ;
                Hole_time_work::create($female_data);
            }else{
                $female_update_data['time_to'] = $request->female_hole_to;
                $female_update_data['time_from'] = $request->female_hole_from;
                Hole_time_work::where('hole_id',$id)->where('type','female')->update($female_update_data);
            }
        }else{
            $female_time = Hole_time_work::where('hole_id',$id)->where('type','female')->first();
            if($female_time != null){
                $female_time->delete();
            }
        }
        if($request->mix == 'mix'){
            $mix_time = Hole_time_work::where('hole_id',$id)->where('type','mix')->first();
            if($mix_time == null){
                $mix_data['time_to'] = $request->mix_hole_to;
                $mix_data['type'] = 'mix';
                $mix_data['time_from'] = $request->mix_hole_from;
                $mix_data['hole_id'] = $id ;
                Hole_time_work::create($mix_data);
            }else{
                $mix_update_data['time_to'] = $request->mix_hole_to;
                $mix_update_data['time_from'] = $request->mix_hole_from;
                Hole_time_work::where('hole_id',$id)->where('type','mix')->update($mix_update_data);
            }
        }else{
            $mix_time = Hole_time_work::where('hole_id',$id)->where('type','mix')->first();
            if($mix_time != null){
                $mix_time->delete();
            }
        }
        session()->flash('success', trans('messages.updated_s'));
        return redirect( route('halls.show'));
    }
    public function show($id){
        $data = Hole::find($id);
        return view('hole.hole_users.details' ,compact('data'));
    }

    // change status
    public function change_status(Request $request){
        $user = Hole::find($request->id);
        $user->status = $request->status;
        $user->save();
        return redirect()->back();
    }

    public function destroy($id){
        $hall = Hole::find($id);
        $hall->deleted = '1';
        $hall->save();
        session()->flash('success', trans('messages.deleted_s'));
        return redirect()->back();
    }

    public function make_famous(Request $request, $id)
    {
        $hole = Hole::find($id);
        if ($hole->famous == '1') {
            $data['famous'] = '0';
            Hole::where('id', $id)->update($data);
            session()->flash('success', trans('messages.famous_removed_done'));
        } else {
            $data['famous'] = '1';
            Hole::where('id', $id)->update($data);
            session()->flash('success', trans('messages.famous_done'));
        }
        return back();
    }

    // sorting
    public function sort(Request $request) {
        $post = $request->all();
        $count = 0;
        for ($i = 0; $i < count($post['id']); $i ++) :
            $index = $post['id'][$i];
            $home_section = Hole::findOrFail($index);
            $count ++;
            $newPosition = $count;
            $data['sort'] = $newPosition;
            if($home_section->update($data)) {
                echo "success";
            }else {
                echo "failed";
            }
        endfor;
        exit('success');
    }

}
