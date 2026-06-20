<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard or redirect based on user role.
     *
     * @return Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect users to their appropriate dashboard based on role
        if (auth()->check() && auth()->user()->hasAnyRole(['enseignant', 'teacher'])) {
            return redirect()->route('enseignant.dashboard');
        }
        
        // Admins see the main dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Fallback (should not happen with proper middleware)
        return redirect()->route('accueil');
    }
}
