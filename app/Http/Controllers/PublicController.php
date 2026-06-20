<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\ContactFormRequest;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Display the homepage
     */
    public function homepage()
    {
        $page = Page::findBySlug('homepage');
        
        if (!$page || !$page->isEnabled()) {
            abort(404);
        }

        return view('public.pages.homepage', [
            'page' => $page,
            'themeVars' => $this->getEnhancedThemeVars(),
        ]);
    }

    /**
     * Display a page by slug
     */
    public function showPage(string $slug, Request $request)
    {
        $page = Page::findBySlug($slug);
        
        // Check for preview mode (only accessible by authenticated users with manage pages permission)
        $isPreview = $request->has('preview') && 
                     auth()->check() && 
                     (auth()->user()->hasPermissionTo('page.manage') || auth()->user()->hasRole('super_admin'));
        
        if (!$page) {
            abort(404, 'Page not found');
        }
        
        // If not preview mode, check normal visibility rules
        if (!$isPreview) {
            if (!$page->isEnabled() || !$page->isPublic() || !$page->isPublished()) {
                abort(404, 'Page not found or not available');
            }
        }

        // Determine which view to use based on slug
        $view = match ($slug) {
            'contact' => 'public.pages.contact',
            'about' => 'public.pages.about',
            'student-portal' => 'public.pages.student-portal',
            default => 'public.pages.default'
        };

        return view($view, [
            'page' => $page,
            'themeVars' => $this->getEnhancedThemeVars(),
            'isPreview' => $isPreview,
        ]);
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        $page = Page::findBySlug('contact');
        
        if (!$page || !$page->isEnabled()) {
            abort(404);
        }

        return view('public.pages.contact', [
            'page' => $page,
            'themeVars' => $this->getEnhancedThemeVars(),
        ]);
    }

    /**
     * Handle contact form submission
     */
    public function handleContactForm(ContactFormRequest $request)
    {
        try {
            // Store contact submission in database
            $submission = DB::table('contact_submissions')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'phone' => $request->phone,
                'submitted_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Log the submission
            Log::info('Contact form submitted', [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
            ]);

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your message! We will get back to you soon.',
                ]);
            }

            return back()->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there was an error sending your message. Please try again later.',
                ], 500);
            }

            return back()
                ->with('error', 'Sorry, there was an error sending your message. Please try again later.')
                ->withInput();
        }
    }

    /**
     * Get navigation data for views
     */
    protected function getNavigationData(): array
    {
        return [
            'navigationPages' => Page::getNavigationPages(),
            'siteName' => SiteSetting::siteName(),
            'logoUrl' => $this->themeService->getLogoUrl(),
        ];
    }

    /**
     * Get enhanced theme vars with school settings
     */
    protected function getEnhancedThemeVars(): array
    {
        $themeVars = $this->themeService->getThemeVars();
        
        // Merge school settings into themeVars
        return array_merge($themeVars, [
            // Organization settings
            'site_name' => setting('school.name', $themeVars['site_name'] ?? 'School'),
            'school_name' => setting('school.name', 'My School'),
            'school_address' => setting('school.address', ''),
            'school_phone' => setting('school.phone', ''),
            'school_email' => setting('school.email', ''),
            'school_website' => setting('school.website', ''),
            'school_location' => setting('school.location', ''),

            // Contact settings (maintain backward compatibility)
            'contact_address' => setting('school.address', ''),
            'contact_email' => setting('school.email', ''),
            'contact_phone' => setting('school.phone', ''),
            'contact_location' => setting('school.location', ''),
            'contact_latitude' => setting('school.latitude', ''),
            'contact_longitude' => setting('school.longitude', ''),
            
            // System settings
            'timezone' => setting('system.timezone', 'UTC'),
            'date_format' => setting('system.date_format', 'Y-m-d'),
            'language' => setting('system.language', 'en'),
            'currency' => setting('system.currency', 'USD'),
            
            // Academic settings (available for public pages if needed)
            'academic_year_start' => setting('school.academic_year_start', '09-01'),
            'academic_year_end' => setting('school.academic_year_end', '06-30'),
            'grading_system' => setting('academic.grading_system', 'percentage'),
            
            // Application settings
            'app_name' => setting('app.name', config('app.name')),
            'registration_enabled' => setting('app.registration_enabled', true),
            'notifications_enabled' => setting('app.notifications_enabled', true),
        ]);
    }

    /**
     * Get theme CSS
     */
    public function themeCSS()
    {
        $css = $this->themeService->generateCSS();
        
        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Search functionality (for future implementation)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('homepage');
        }

        $pages = Page::enabled()
            ->public()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->ordered()
            ->get();

        return view('public.pages.search', [
            'query' => $query,
            'pages' => $pages,
            'themeVars' => $this->getEnhancedThemeVars(),
        ]);
    }

    /**
     * Get site information for API or AJAX requests
     */
    public function siteInfo()
    {
        return response()->json([
            'site_name' => setting('school.name', SiteSetting::siteName()),
            'site_description' => SiteSetting::siteDescription(),
            'contact_email' => setting('school.email', SiteSetting::contactEmail()),
            'contact_phone' => setting('school.phone', SiteSetting::contactPhone()),
            'contact_address' => setting('school.address', SiteSetting::contactAddress()),
            'logo_url' => $this->themeService->getLogoUrl(),
            'theme_colors' => [
                'primary' => SiteSetting::primaryColor(),
                'secondary' => SiteSetting::secondaryColor(),
            ],
            'school_website' => setting('school.website', ''),
            'timezone' => setting('system.timezone', 'UTC'),
            'language' => setting('system.language', 'en'),
            'currency' => setting('system.currency', 'USD'),
        ]);
    }
}
