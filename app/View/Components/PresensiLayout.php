<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PresensiLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;
    public $custom_style = null;
    public $custom_script = null;

    public function __construct($title = null)
    {
        $this->title = $title ?? "E Presensi";
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.presensi');
    }
}
