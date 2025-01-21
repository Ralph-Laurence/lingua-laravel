<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditableFormSectionControlButton extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $unlock = false
    )
    {

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.editable-form-section-control-button');
    }
}
