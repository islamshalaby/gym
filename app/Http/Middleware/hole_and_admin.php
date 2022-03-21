<?php

namespace App\Http\Middleware;

use Closure;

class hole_and_admin
{
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('hole')->check()) {
            $request->id = \Auth::guard('hole')->user()->id;
            return $next($request);
        }else if (\Auth::guard('admin')->check()) {
            $request->id = \Auth::guard('admin')->user()->id;
            return $next($request);
        } else {
            session()->flash('error', trans('messages.unautherize'));
            return redirect()->back();
        }
    }
}
