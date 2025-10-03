<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Meta extends Component
{
    public $title;
    public $description;
    public $image;
    public $url;
    public $type;

    public function __construct($title = null, $description = null, $image = null, $url = null, $type = 'website')
    {
        $this->title = $title ?? config('app.name');
        $this->description = $description ?? "Toko online " . config('app.name') . ". Temukan produk berkualitas dengan harga terbaik.";
        $this->image = $image ?? asset('assets/images/logo.webp');
        $this->url = $url ?? url()->current();
        $this->type = $type;
    }

    public function render()
    {
        return view('components.meta');
    }
}
