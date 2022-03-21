<?php

namespace App\Http\Controllers\Admin\Coach;

use App\Coach;
use App\Coach_time_work;
use App\Day;
use App\Hole;
use App\Http\Controllers\Admin\AdminController;
use App\Rate;
use App\User;
use App\User_caoch_ask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Cloudinary;

class CoachController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Coach::where('is_confirm', 'accepted')->where('deleted', '0')->get();
        return view('coach.coach_users.index', compact('data'));
    }

    public function rejected()
    {
        $data = Coach::where('is_confirm', 'rejected')->where('deleted', '0')->get();
        return view('coach.coach_users.index', compact('data'));
    }

    public function new_join()
    {
        $data = Coach::where('is_confirm', 'new')->where('deleted', '0')->get();
        return view('coach.coach_users.index', compact('data'));
    }

    public function confirm($id, $type)
    {
        $data['is_confirm'] = $type;
        Coach::where('id', $id)->update($data);
        session()->flash('success', trans('messages.status_changed'));
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $days = Day::orderBy('created_at', 'asc')->get();
        return view('coach.coach_users.create', compact('days'));
    }

    public function famous_coaches()
    {
        $data = Coach::where('famous', '1')->where('deleted', '0')->get();
        return view('coach.coach_users.index', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate(\request(),
            [
                'name' => 'required',
                'name_en' => 'required',
                'phone' => 'required',
                'age' => 'required',
                'exp' => 'required',
                'email' => 'required',
                'about_coach' => '',
                'about_coach_en' => '',
                'password' => 'required|numeric',
                'image' => 'required'
            ]);
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
            $data['is_confirm'] = 'accepted';
        } else {
            unset($data['password']);
        }
        $data['verified'] = 1;
        if ($request->image != null) {
            $logo = $request->file('image')->getRealPath();
            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_name = $image_id . '.' . $image_format;
            $data['image'] = $image_new_name;
        }
        unset($data['time_from']);
        unset($data['time_to']);
        $data['is_confirm'] = 'accepted';

        $coach = Coach::create($data);
        //add time works
        $data_coach_time_work['coach_id'] = $coach->id;
        $data_coach_time_work['time_from'] = $request->time_from ;
        $data_coach_time_work['time_to'] =$request->time_to ;
        Coach_time_work::create($data_coach_time_work);
        // add free coach ask to every user
        $users = User::all();
        foreach ($users as $row) {
            $data['user_id'] = $row->id;
            $data['caoch_id'] = $coach->id;
            User_caoch_ask::create($data);
        }
        session()->flash('success', trans('messages.added_s'));
        return redirect(route('coaches.show'));
    }

// change status
    public function change_status(Request $request)
    {
        $user = Coach::find($request->id);
        $user->status = $request->status;
        $user->save();
        return redirect()->back();
    }

    public function make_famous(Request $request, $id)
    {
        $coach = Coach::find($id);
        if ($coach->famous == '1') {
            $data['famous'] = '0';
            Coach::where('id', $id)->update($data);
            session()->flash('success', trans('messages.famous_done_coaches'));
        } else {
            $data['famous'] = '1';
            Coach::where('id', $id)->update($data);
            session()->flash('success', trans('messages.famous_done'));
        }
        return back();
    }

    public function show($id)
    {
        $data = Coach::where('id', $id)->first();
        $time_works = Coach_time_work::where('coach_id', $id)->get();
        return view('coach.coach_users.details', compact('data', 'time_works'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Coach::where('id', $id)->first();
        return view('coach.coach_users.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        unset($data['_token']);
        unset($data['MAX_FILE_SIZE']);
//        $data = $this->validate(\request(),
//            [
//                'name' => 'required',
//                'name_en' => 'required',
//                'phone' => 'required',
//                'age' => 'required',
//                'exp' => 'required',
//                'email' => 'required',
//                'about_coach' => '',
//                'image' => '',
//                'about_coach_en' => ''
//            ]);
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->image != null) {
            $logo = $request->file('image')->getRealPath();
            $imagereturned = Cloudinary::upload($logo);
            $image_id = $imagereturned->getPublicId();
            $image_format = $imagereturned->getExtension();
            $image_new_name = $image_id . '.' . $image_format;
            $data['image'] = $image_new_name;
        }
        Coach::where('id', $id)->update($data);
        session()->flash('success', trans('messages.updated_s'));
        return redirect(route('coaches.show'));
    }

    // sorting
    public function sort(Request $request)
    {
        $post = $request->all();
        $count = 0;
        for ($i = 0; $i < count($post['id']); $i++) :
            $index = $post['id'][$i];
            $home_section = Coach::findOrFail($index);
            $count++;
            $newPosition = $count;
            $data['sort'] = $newPosition;
            if ($home_section->update($data)) {
                echo "success";
            } else {
                echo "failed";
            }
        endfor;
        exit('success');
    }

    public function destroy($id)
    {
        //
    }

    public function rates($id)
    {
        $data = Rate::where('order_id', $id)->where('type', 'coach')->orderBy('admin_approval', 'desc')->orderBy('created_at', 'desc')->get();
        $coach = Coach::where('id', $id)->first();
        $coach_rate = $coach->rate;
        return view('coach.coach_users.rates.index', compact('data', 'coach_rate'));
    }

    public function change_rate_status($type, $id)
    {
        if ($type == 'accept') {
            $data['admin_approval'] = 1;
        } else if ($type == 'reject') {
            $data['admin_approval'] = 0;
        }
        $rate_updated = Rate::where('id', $id)->update($data);
        if ($rate_updated > 0) {
            $rate = Rate::findOrFail($id);
            $total_rates = Rate::where('order_id', $rate->order_id)->where('admin_approval', 1)->where('type', 'coach')->get();
            $sum_rates = $total_rates->sum('rate');
            $count_rates = count($total_rates);
            if ($count_rates == 0) {
                $new_rate = 0;
            } else {
                $new_rate = $sum_rates / $count_rates;
            }
            //update hall table of rate
            $coach = Coach::findOrFail($rate->order_id);
            $floatVal = floatval($new_rate);
            // If the parsing succeeded and the value is not equivalent to an int
            if ($floatVal && intval($floatVal) != $floatVal) {
                $coach->rate = number_format((float)$new_rate, 1, '.', '');
            } else {
                $coach->rate = $new_rate;
            }
            $coach->save();
        }
        session()->flash('success', trans('messages.status_changed'));
        return back();
    }
}
