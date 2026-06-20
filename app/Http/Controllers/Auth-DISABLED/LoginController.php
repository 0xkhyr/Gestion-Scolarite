<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Providers\RouteServiceProvider;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class LoginController extends Controller
// {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

//     use AuthenticatesUsers;

//     /**
//      * Where to redirect users after login.
//      *
//      * @var string
//      */
//     protected $redirectTo = RouteServiceProvider::HOME;

//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         $this->middleware('guest')->except('logout');
//     }

//     /**
//      * Get the post-authentication redirect path.
//      */
//     protected function redirectTo()
//     {
//         $user = Auth::user();

//         if (!$user) {
//             return '/';
//         }

//         // Use the RoleRedirectService for consistent redirect logic
//         return \App\Services\RoleRedirectService::getRedirectPath($user);
//     }

//     // Removed guard() method to use default web guard

//     /**
//      * Handle a login request to the application.
//      */
//     public function login(Request $request)

//         // Check for too many login attempts
//         if (method_exists($this, 'hasTooManyLoginAttempts') &&
//             $this->hasTooManyLoginAttempts($request)) {
//             $this->fireLockoutEvent($request);
//             return $this->sendLockoutResponse($request);
//         }

//         // Attempt to log the user in
//         if ($this->attemptLogin($request)) {
//             $user = Auth::user();

//             // Enforce Login Context (Admin vs Teacher)
//             if ($request->routeIs('admin.login.submit') && !$user->isAdmin()) {
//                 Auth::logout();
//                 return redirect()->back()
//                     ->withInput($request->only($this->username(), 'remember'))
//                     ->withErrors([
//                         $this->username() => __('app.acces_refuse'),
//                     ]);
//             }

//             if ($request->routeIs('enseignant.connexion.submit') && !$user->isTeacher()) {
//                 Auth::logout();
//                 return redirect()->back()
//                     ->withInput($request->only($this->username(), 'remember'))
//                     ->withErrors([
//                         $this->username() => __('app.acces_refuse'),
//                     ]);
//             }

//             // Log successful login
//             if ($user->isTeacher() && $user->profile) {
//                 \App\Services\ActivityLogger::log('teacher', $user->profile->id_enseignant, 'login', 'enseignant', $user->profile->id_enseignant, 'Teacher login');
//             } elseif ($user->isAdmin() && $user->profile) {
//                  \App\Services\ActivityLogger::log('admin', $user->profile->id_administrateur, 'login', 'administrateur', $user->profile->id_administrateur, 'Admin login');
//             }

//             // Check if user is active
//             if (!$user->is_active) {
//                 Auth::logout();
//                 return redirect()->back()
//                     ->withInput($request->only($this->username(), 'remember'))
//                     ->withErrors([
//                         $this->username() => __('app.account_deactivated'),
//                     ]);
//             }

//             // 2FA Logic for Admins
//             if ($user->isAdmin() && $user->two_factor_enabled) {
//                 session()->forget('admin_2fa_passed');
//                 session(['admin_2fa_pending' => true]);
//                 return redirect()->route('admin.2fa.challenge');
//             }

//             return $this->sendLoginResponse($request);
//         }

//         // Increment login attempts
//         $this->incrementLoginAttempts($request);

//         // Log failed teacher login
//         \App\Services\ActivityLogger::log('teacher', null, 'failed_login', null, null, 'Failed login attempt for '.$request->email);

//         return $this->sendFailedLoginResponse($request);
//     }

//     /**
//      * Number of allowed attempts for teacher logins.
//      */
//     protected function maxAttempts()
//     {
//         return 5; // 5 attempts
//     }

//     /**
//      * Decay minutes for teacher login attempts.
//      */
//     protected function decayMinutes()
//     {
//         return 10; // 10 minutes
//     }

//     /**
//      * Show the application's login form.
//      */
//     public function showLoginForm()
//     {
//         if (request()->routeIs('admin.login')) {
//             return view('admin.auth.login');
//         }
//         return view('auth.login');
//     }

//     /**
//      * Log the user out of the application.
//      */
//     public function logout(Request $request)
//     {
//         $this->guard()->logout();

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         if ($request->routeIs('admin.logout')) {
//              return redirect()->route('admin.login')->with('success', __('app.logged_out_successfully'));
//         }

//         return redirect()->route('enseignant.connexion')->with('success', __('app.logged_out_successfully'));
//     }
// }
