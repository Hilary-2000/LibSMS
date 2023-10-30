<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class notification extends Component
{
    // define variables
    
    /**
     * Create a new component instance.
     */
    public $notices = [];
    public function __construct($notifications)
    {
        $this->notices = $notifications;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notifications');
    }
}
