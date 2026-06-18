<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MobileHeader extends Component
{
    /**
     * Create component instance.
     */
    public function __construct()
    {
        // no props for now
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.admin.mobile-header');
    }
}
