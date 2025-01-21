<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditableFormSectionHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $label = 'Form Section',
        public $caption = '',
        public $hidden = false
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.editable-form-section-header');
    }
}
