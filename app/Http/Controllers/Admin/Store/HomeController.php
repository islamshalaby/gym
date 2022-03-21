<?php
namespace App\Http\Controllers\Admin\Store;
use App\Http\Controllers\Admin\AdminController;

class HomeController extends AdminController{

    // get all contact us messages
    public function home(){

        return view('store.home');
    }

}
