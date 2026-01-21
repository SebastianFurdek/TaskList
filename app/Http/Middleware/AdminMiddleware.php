<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (isset($user->role) && $user->role !== 'admin') {
            abort(Response::HTTP_FORBIDDEN, 'Nemáte oprávnenie na prístup k tejto stránke.');
        }

        $isDeleteMethod = $request->isMethod('delete');
        $routeName = optional($request->route())->getName();

        if ($isDeleteMethod || $routeName === 'users.destroy') {
            $routeParams = $request->route() ? $request->route()->parameters() : [];

            $target = null;
            if (array_key_exists('user', $routeParams)) {
                $target = $routeParams['user'];
            } elseif (array_key_exists('id', $routeParams)) {
                $target = $routeParams['id'];
            }

            if (is_object($target) && method_exists($target, 'getKey')) {
                $targetId = $target->getKey();
            } else {
                $targetId = $target;
            }

            if ($targetId && intval($targetId) === intval($user->getKey())) {
                abort(Response::HTTP_FORBIDDEN, 'Administrátor si nemôže zmazať sám seba.');
            }
        }

        return $next($request);
    }
}

