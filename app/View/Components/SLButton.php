<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class SLButton extends Component
{
    private $classList = 'btn btn-sm sl-button';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $id    = '',
        public $type  = 'button',
        public $text  = 'Button',
        public $style = 'primary'
    )
    {
        if (empty($this->id))
            $this->id = 'input-'.Str::random(10);

        switch ($style)
        {
            default:
            case 'primary':
                $this->classList .= " btn-primary sign-lingua-purple-button";
                break;

            case 'danger':
                $this->classList .= " btn-danger sign-lingua-red-button";
                break;

            case 'secondary':
                $this->classList .= " btn-secondary sign-lingua-gray-button";
                break;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.sl-button')->with('classList', $this->classList);
    }
}
