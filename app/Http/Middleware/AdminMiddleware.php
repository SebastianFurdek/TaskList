<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * - Only allow users with role === 'admin'.
     * - Prevent an admin from deleting themself.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Not authenticated -> redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Not an admin -> forbid
        if (isset($user->role) && $user->role !== 'admin') {
            abort(Response::HTTP_FORBIDDEN, 'Nemáte oprávnenie na prístup k tejto stránke.');
        }

        // Prevent admin from deleting themselves.
        // Check if this is a DELETE request OR a route named users.destroy
        $isDeleteMethod = $request->isMethod('delete');
        $routeName = optional($request->route())->getName();

        if ($isDeleteMethod || $routeName === 'users.destroy') {
            // Try to obtain the target user id/model from route parameters
            $routeParams = $request->route() ? $request->route()->parameters() : [];

            // Common param names: 'user', 'id'
            $target = null;
            if (array_key_exists('user', $routeParams)) {
                $target = $routeParams['user'];
            } elseif (array_key_exists('id', $routeParams)) {
                $target = $routeParams['id'];
            }

            // If target is a model, try to get its id
            if (is_object($target) && method_exists($target, 'getKey')) {
                $targetId = $target->getKey();
            } else {
                $targetId = $target;
            }

            if ($targetId && intval($targetId) === intval($user->getKey())) {
                // Block self-deletion
                abort(Response::HTTP_FORBIDDEN, 'Administrátor si nemôže zmazať sám seba.');
            }
        }

        return $next($request);
    }
}

