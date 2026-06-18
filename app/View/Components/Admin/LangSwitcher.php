<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LangSwitcher extends Component
{
    /**
     * Available locales array [code => label]
     * @var array
     */
    public $locales;

    /**
     * Create component instance.
     *
     * @param array|null $locales
     */
    public function __construct($locales = null)
    {
        $raw = $locales ?? config('locales', []);

        // Normalize to structured format: code => ['label'=>..., 'flag'=>...]
        $normalized = [];
        foreach ($raw as $code => $value) {
            if (is_array($value)) {
                $normalized[$code] = [
                    'label' => $value['label'] ?? (string) ($value[0] ?? strtoupper($code)),
                    'flag' => $value['flag'] ?? $code,
                ];
            } else {
                $normalized[$code] = [
                    'label' => (string) $value,
                    'flag' => $code,
                ];
            }
        }

        $this->locales = $normalized;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return view('components.admin.lang-switcher');
    }
}
