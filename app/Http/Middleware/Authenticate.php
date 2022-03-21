<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $uri =  $request->getRequestUri();
            if (strpos($uri, 'admin-panel') !== false) {
                return route('adminlogin');
            }elseif (strpos($uri, 'hole-panel') !== false){
                return route('hole.home');
            }else{
                return route('invalid' , [1,2]);
            }
        }
    }
}
