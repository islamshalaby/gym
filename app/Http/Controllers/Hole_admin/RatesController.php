<?php
namespace App\Http\Controllers\Hole_admin;
use App\Http\Controllers\Controller;
use App\Rate;

class RatesController extends Controller{

    // get all contact us messages
    public function index(){
        $id = auth()->guard('hole')->user()->id;
        $data = Rate::where('order_id',$id)->where('type','hall')->orderBy('created_at','desc')->get();
        return view('hole_admin.rates.index' ,compact('data'));
    }
}
