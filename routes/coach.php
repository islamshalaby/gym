<?php

// Holes Routes
Route::group(['middleware'=>'language','prefix' => "admin-panel",'namespace' => "Admin\Coach"] , function($router){

    // Notifications Route
    Route::group(["prefix" => "coach_notifications"], function ($router) {
        Route::get('show', 'NotificationController@show');
        Route::get('details/{id}', 'NotificationController@details');
        Route::get('delete/{id}', 'NotificationController@delete');
        Route::get('send', 'NotificationController@getsend');
        Route::post('send', 'NotificationController@send');
        Route::get('resend/{id}', 'NotificationController@resend');
    });
    Route::group([ 'prefix' => 'coaches',] , function($router){
        Route::get('show' , 'CoachController@index')->name('coaches.show');
        Route::get('rejected' , 'CoachController@rejected')->name('coaches.rejected');
        Route::get('confirm/{id}/{type}' , 'CoachController@confirm')->name('coach.confirm');
        Route::get('new_join' , 'CoachController@new_join')->name('coaches.new_join');
        Route::get('details/{id}' , 'CoachController@show')->name('coaches.details');
        Route::get('create' , 'CoachController@create')->name('coaches.create');
        Route::post('sort' , 'CoachController@sort')->name('coaches.sort');
        Route::get('edit/{id}' , 'CoachController@edit')->name('coaches.edit');
        Route::post('update/{id}' , 'CoachController@update')->name('coaches.update');
        Route::post('store' , 'CoachController@store')->name('coaches.store');
        Route::get('change_status/{status}/{id}' , 'CoachController@change_status');
        Route::get('make_famous/{id}' , 'CoachController@make_famous')->name('coaches.make_famous');
        Route::get('rates/{id}' , 'CoachController@rates')->name('coaches.rates');
        Route::get('/rate/change_status/{type}/{id}' , 'CoachController@change_rate_status')->name('coaches.change_status');

        // coach times
        Route::get('times/{id}' , 'Hall_time_worksController@index')->name('coaches.times');
        Route::get('times/create/{id}' , 'Hall_time_worksController@create')->name('coach_times.create');
        Route::get('times/edit/{id}' , 'Hall_time_worksController@edit')->name('coach_times.edit');
        Route::post('times/store' , 'Hall_time_worksController@store')->name('coach_times.store');
        Route::post('times/update/{id}' , 'Hall_time_worksController@update')->name('coach_times.update');
        Route::get('times/delete/{id}' , 'Hall_time_worksController@destroy')->name('coach_times.delete');
    });
    Route::get('famous_coaches' , 'CoachController@famous_coaches')->name('famous_coaches');
});
