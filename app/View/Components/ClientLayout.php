<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClientLayout extends Component
{
    public $seo;

    /**
     * Create a new component instance.
     */
    public function __construct($seo = null)
    {
        $this->seo = $seo;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.client.app', [
            'seo' => $this->seo,
        ]);
    }
}
