<?php

namespace App\Http\Middleware;

use Closure;

class shop
{
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('shop')->check()) {
            $request->id = \Auth::guard('shop')->user()->id;
            return $next($request);
        } else {
            session()->flash('error', trans('messages.unautherize'));
            return redirect()->back();
        }
    }
}
