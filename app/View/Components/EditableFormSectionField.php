<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class EditableFormSectionField extends Component
{
    private $m_inputClassList = 'form-control text-13';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name = '',
        public $value = '',
        public $placeholder = '',
        public $allowSpaces = 'true',
        public $invalidFeedback = '',
        public $inputClassList = '',
        public $locked = false
    )
    {
        $defaultName = 'input-'.Str::random(10);

        if (empty($this->name))
            $this->name = $defaultName;

        if ($this->allowSpaces === 'false')
            $this->m_inputClassList .= ' no-spaces ';

        if (!empty($inputClassList))
            $this->m_inputClassList .= $inputClassList;

        // Automatically make default placeholder value using the element name.
        // We wont add an automatic placeholder when there is no element name.
        if (empty($this->placeholder) && $this->name != $defaultName)
            $this->placeholder = ucwords($this->name);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.editable-form-section-field', [
            'inputClasses' => $this->m_inputClassList
        ]);
    }
}
