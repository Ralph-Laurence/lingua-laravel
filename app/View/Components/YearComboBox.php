<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class YearComboBox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $from = 0,
        public $to = 0,
        public $value = 0,
        public $as = ''
    )
    {
        $currentYear = date('Y');

        // Set default values if empty
        $this->from = $from ?: $currentYear;
        $this->to   = $to ?: $currentYear;
        $this->as   = $as ?: 'year-combobox-'.Str::random(10);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('partials.year-combo-box');
    }
}
