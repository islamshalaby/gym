<?php

namespace App\Http\Middleware;

use Closure;

class hole
{
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('hole')->check()) {
            $request->id = \Auth::guard('hole')->user()->id;
            return $next($request);
        } else {
            session()->flash('error', trans('messages.unautherize'));
            return redirect()->back();
        }
    }
}
