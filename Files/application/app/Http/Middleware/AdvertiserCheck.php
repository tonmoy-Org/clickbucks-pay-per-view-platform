<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('advertiser')->check()) {
            $advertiser = authAdvertiser();
            if ($advertiser->status  && $advertiser->ev  && $advertiser->sv  && $advertiser->tv) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first.';
                    return response()->json([
                        'remark' => 'unverified',
                        'status' => 'error',
                        'message' => ['error' => $notify],
                        'data' => [
                            'is_ban' => $advertiser->status,
                            'email_verified' => $advertiser->ev,
                            'mobile_verified' => $advertiser->sv,
                            'twofa_verified' => $advertiser->tv,
                        ],
                    ]);
                } else {
                    return to_route('advertiser.authorization');
                }
            }
        }
        abort(403);
    }
}
