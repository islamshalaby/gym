<?php

Route::get('/',function(){
    return redirect()->route('hole_login');
});
// halls Routes
Route::group(['middleware'=>'language','prefix' => "admin-panel",'namespace' => "Admin\Hole"] , function($router){
    Route::get('home' , 'HomeController@home')->name('hole.home_panel');
    Route::group([ 'prefix' => 'halls',] , function($router){
        Route::get('show' , 'HoleController@index')->name('halls.show');
        Route::get('details/{id}' , 'HoleController@show')->name('halls.details');
        Route::get('create' , 'HoleController@create')->name('halls.create');
        Route::get('edit/{id}' , 'HoleController@edit')->name('halls.edit');
        Route::post('update/{id}' , 'HoleController@update')->name('halls.update');
        Route::post('delete/{id}' , 'HoleController@destroy')->name('halls.delete');
        Route::post('store' , 'HoleController@store')->name('halls.store');
        Route::get('change_status/{status}/{id}' , 'HoleController@change_status');
        Route::get('make_famous/{id}' , 'HoleController@make_famous')->name('halls.make_famous');
        Route::post('sort' , 'HoleController@sort')->name('halls.sort');
//        Route::get('edit/{id}' , 'HoleController@edit');
    });
    Route::resource('admin_hall_rates' , 'HoleRatesController');
    Route::get('all_rates' , 'HoleRatesController@all_rates')->name('halls.all_rates');
    Route::get('admin_hall_rate/{type}/{id}' , 'HoleRatesController@change_status')->name('admin_hall_rate.change_status');


    Route::get('famous_holes' , 'HoleController@famous_holes')->name('famous_holes');
    Route::resource('admin_branches' , 'HoleBranchesController');
    Route::get('branches/show_branches/{id}' , 'HoleBranchesController@show')->name('branches.show_new');
    Route::get('branches/create_new/{id}' , 'HoleBranchesController@create')->name('branches.create_new');
    Route::post('admin_branches/update/{id}' , 'HoleBranchesController@update')->name('admin_branches.update');
    Route::get('admin_branches/delete/{id}' , 'HoleBranchesController@destroy');

    Route::get('reserv_data/types' , 'ReservDataController@types')->name("reserv_data.types");
    Route::post('reserv_data/types/store' , 'ReservDataController@types_store')->name("reserv.types.store");
    Route::post('reserv_data/types/update' , 'ReservDataController@types_update')->name("reserv.types.update");
    Route::get('reserv_data/types/delete/{id}' , 'ReservDataController@types_delete')->name("reserv.types.delete");

    Route::get('reserv_data/goals/{id}' , 'ReservDataController@goals')->name("reserv_data.goals");
    Route::post('reserv_data/goals/store' , 'ReservDataController@goals_store')->name("reserv.goals.store");
    Route::post('reserv_data/goals/update' , 'ReservDataController@goals_update')->name("reserv.goals.update");
    Route::get('reserv_data/goals/delete/{id}' , 'ReservDataController@goals_delete')->name("reserv.goals.delete");
});

//Route::get('/login' ,  [ 'as' => 'adminlogin', 'uses' => 'Hole_admin\LoginController@getlogin']);
Route::get('/login' , 'Hole_admin\LoginController@getlogin')->name('hole_login');
Route::post('/login' , 'Hole_admin\LoginController@postlogin')->name('post.hole.login');


//Hole admin panel
Route::group(['middleware'=> ['language','hole'],'prefix' => "hall-panel",'namespace' => "Hole_admin"] , function($router){
    Route::get('/' , 'HomeController@home')->name('hall.home');

    Route::get('/data' , 'HomeController@hall_data')->name('hall.data');
    Route::post('/data/update/{id}' , 'HomeController@update_hall_data')->name('hall.update');

    Route::get('/time_works' , 'HomeController@hall_time_works')->name('hall.time_works');
    Route::post('/data/update/time_works/{id}' , 'HomeController@update_hall_time_works')->name('hall.update.time_works');

    Route::resource('branches' , 'HoleBranchesController');
    Route::get('branches/update_new/{id}' , 'HoleBranchesController@update')->name('branches.update_new');
    Route::get('branches/delete/{id}' , 'HoleBranchesController@destroy')->name('branches.delete');

    Route::resource('hall_rates' , 'RatesController');
    Route::resource('media' , 'MediaController');
    Route::post('media/store_video' , 'MediaController@store_video')->name('media.store.video');
    Route::post('media/delete' , 'MediaController@destroy')->name('media.delete');

    Route::get('subscribers/{type}' , 'SubscribersController@index')->name('subscribers');
    Route::get('subscribers/{id}/end' , 'SubscribersController@end')->name('subscription.end');
    Route::get('subscribers/user_data/{id}' , 'SubscribersController@user_data')->name('subscription.user_data');
    Route::post('subscribers/re_new' , 'SubscribersController@re_new')->name('subscription.re_new');

    Route::resource('booking' , 'BookingsController');
    Route::get('booking/common/{id}' , 'BookingsController@make_common')->name('booking.common');
    Route::post('booking/new_update/{id}' , 'BookingsController@update')->name('booking.new_update');
    Route::get('booking/delete/{id}' , 'BookingsController@destroy')->name('booking.destroy');
    Route::get('booking_detail/delete/{id}' , 'BookingsController@destroy_detail')->name('booking.destroy_detail');
    Route::post('booking_detail/store' , 'BookingsController@store_detail')->name('booking_detail.store');

    Route::resource('hall_payments' , 'PaymentsController');
    Route::post('/hall_payments/fetch_by_booking' , 'PaymentsController@fetch_by_booking')->name('hall_payments.fetch_by_booking');


    Route::get('/logout' , 'LoginController@logout');
    Route::get('/profile' , 'HomeController@profile');
    Route::post('/profile' , 'HomeController@updateprofile');

});



