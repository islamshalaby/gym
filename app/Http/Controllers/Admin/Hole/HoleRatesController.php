<?php
namespace App\Http\Controllers\Admin\Hole;
use App\Hole;
use App\Hole_branch;
use App\Hole_time_work;
use App\Http\Controllers\Admin\AdminController;
use App\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JD\Cloudder\Facades\Cloudder;


class HoleRatesController extends AdminController{

    public function show($id){
        $data = Rate::where('order_id',$id)->where('type','hall')->orderBy('admin_approval','desc')->orderBy('created_at','desc')->get();
        $hall =  Hole::where('id',$id)->first();
        $hall_rate = $hall->rate ;
        return view('hole.hole_users.rates.index' ,compact('data','hall_rate'));
    }
    public function all_rates(){
        $data = Rate::where('type','hall')->orderBy('admin_approval','desc')->orderBy('created_at','desc')->get();
        return view('hole.hole_users.rates.index' ,compact('data'));
    }

    public function change_status($type,$id){
        if($type == 'accept'){
            $data['admin_approval'] = 1 ;
        }else  if($type == 'reject'){
            $data['admin_approval'] = 0 ;
        }
        $rate_updated = Rate::where('id',$id)->update($data);
        if($rate_updated > 0){
            $rate = Rate::findOrFail($id);
            $total_rates = Rate::where('order_id',$rate->order_id)->where( 'admin_approval' , 1 )->where( 'type' , 'hall' )->get();
            $sum_rates = $total_rates->sum('rate');
            $count_rates = count($total_rates);
            if($count_rates == 0){
                $new_rate = 0 ;
            }else{
                $new_rate = $sum_rates / $count_rates ;
            }
            //update hall table of rate
            $hall = Hole::findOrFail($rate->order_id);
            $floatVal = floatval($new_rate);
            // If the parsing succeeded and the value is not equivalent to an int
            if($floatVal && intval($floatVal) != $floatVal){
                $hall->rate =  number_format((float)$new_rate, 1, '.', '');
            }else{
                $hall->rate = $new_rate ;
            }
            $hall->save();
        }
        session()->flash('success', trans('messages.status_changed'));
        return back();
    }


}
