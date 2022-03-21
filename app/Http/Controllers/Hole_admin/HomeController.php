<?php
namespace App\Http\Controllers\Hole_admin;

//use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;




use Cloudinary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Hole_time_work;
use App\Hole_booking;
use App\Hole_branch;
use App\Reservation;
use App\Hole;
use App\Rate;


class HomeController extends Controller{

    // get all contact us messages
    public function home(){
        $id = auth()->guard('hole')->user()->id ;
        $count_branchs = Hole_branch::where('hole_id',$id)->get()->count();
        $count_bookings = Hole_booking::where('hole_id',$id)->where('deleted','0')->get()->count();

        $booking_ids = Hole_booking::where('hole_id',$id)->select('id')->get()->toArray();
        $count_Reservations = Reservation::whereIn('booking_id',$booking_ids)->where('deleted','0')->where('type','hall')->get()->count();
        $count_rates = Rate::where('order_id',$id)->where('type','hall')->where('admin_approval',1)->get()->count();
        return view('hole_admin.home',compact('count_branchs','count_bookings','count_Reservations','count_rates'));
    }
    public function hall_data(){
        $id = auth()->guard('hole')->user()->id;
        $data = Hole::where('id',$id)->first();
        return view('hole_admin.hall_data.index',compact('data'));
    }


    public function hall_time_works(){
        $id = auth()->guard('hole')->user()->id;
        $data = Hole::where('id',$id)->first();
        $time_male = Hole_time_work::where('hole_id',$id)->where('type','male')->first();
        $time_female = Hole_time_work::where('hole_id',$id)->where('type','female')->first();
        $time_mix = Hole_time_work::where('hole_id',$id)->where('type','mix')->first();
        $branches = Hole_branch::where('hole_id',$id)->get();
        return view('hole_admin.hall_data.time_works',compact('data','time_male','time_female','time_mix','branches'));
    }

	public function upload($request)
	{
		 $resizedVideo = cloudinary()->uploadVideo($request->getRealPath(), [
				'folder' => 'uploads',
				'transformation' => [
						  'width' => 350,
						  'height' => 200
				 ]
		]);

		return $resizedVideo;
	}

    public function update_hall_data(Request $request , $id)
    {
        $data = $this->validate(\request(),
            [
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required|numeric',
                'about_hole' => '',
                'story' => '',
            ]);

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
        if($request->story != null){
            $story = $request->file('story')->getRealPath();
			if ($request->file('story')->getSize()) {
				$uploadedFileUrl = $this->upload($request->file('story'));
                $image_id2 = $uploadedFileUrl->getPublicId();
                $image_format2 = $uploadedFileUrl->getExtension();
				$image_new_story = $image_id2.'.'.$image_format2;
				$data['story'] = $image_new_story ;
			}
        }
        Hole::where('id',$id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return back();
    }

    public function update_hall_time_works(Request $request , $id)
    {
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
        return back();
    }

    // get profile
    public function profile(){
        $admin = Auth::guard('hole')->user();
        $data['name'] = $admin->name;
        $data['email'] = $admin->email;
        return view('hole_admin.profile' , ['data' => $data]);
    }

    // update profile
    public function updateprofile(Request $request){
        $current_admin_id =  Auth::guard('hole')->user()->id;
        $check_manager_email = Hole::where('email' , $request->email)->where('id' , '!=' , $current_admin_id)->first();
        if($check_manager_email){
            return redirect()->back()->with('status' , 'Email Exists Before');
        }

        $current_manager = Hole::find($current_admin_id);
        $current_manager->name = $request->name;
        $current_manager->email = $request->email;
        if($request->password){
            $current_manager->password = Hash::make($request->password);
        }
        $current_manager->save();
        session()->flash('success', trans('messages.updated_s'));
        return redirect()->back();
    }

}
