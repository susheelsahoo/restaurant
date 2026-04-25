<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        addJavascriptFile('assets/js/custom/authentication/sign-in/general.js');

        return view('pages/auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $guard = $this->guardFor($request);

        $request->authenticate($guard);
        Auth::guard($this->oppositeGuard($guard))->logout();

        $request->session()->regenerate();

        $request->user($guard)->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        return redirect()->intended($this->homePathFor($guard));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $guard = $this->logoutGuardFor($request);

        Auth::guard($guard)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($this->loginPathFor($guard));
    }

    private function guardFor(Request $request): string
    {
        return $request->is('mobile/login') ? 'mobile' : 'web';
    }

    private function oppositeGuard(string $guard): string
    {
        return $guard === 'mobile' ? 'web' : 'mobile';
    }

    private function homePathFor(string $guard): string
    {
        return $guard === 'mobile' ? '/mobile/dashboard' : RouteServiceProvider::HOME;
    }

    private function loginPathFor(string $guard): string
    {
        return $guard === 'mobile' ? '/mobile/login' : route('admin.login');
    }

    private function logoutGuardFor(Request $request): string
    {
        $previousPath = parse_url($request->headers->get('referer', ''), PHP_URL_PATH) ?: '';

        if (str_starts_with($previousPath, '/mobile')) {
            return 'mobile';
        }

        if (Auth::guard('mobile')->check() && ! Auth::guard('web')->check()) {
            return 'mobile';
        }

        return 'web';
    }
}
