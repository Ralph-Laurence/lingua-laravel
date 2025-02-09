<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class EditableFormSectionField extends Component
{
    private $m_inputClassList = 'form-control text-13';
    private $m_rootClassList  = 'input-group has-validation';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name            = '',
        public $value           = '',
        public $originalValue   = '',
        public $placeholder     = '',
        public $allowSpaces     = 'true',
        public $invalidFeedback = '',
        // backend  = allow the backend to print errors from blade view
        // frontend = use frontend to display the errors
        public $feedbackMode    = 'backend',
        public $inputClassList  = '',
        public $rootClassList   = '',
        public $locked          = false
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

        if (empty($rootClassList))
            $this->m_rootClassList .= ' mb-3';
        else
            $this->m_rootClassList .= ' '. $rootClassList;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.editable-form-section-field', [
            'inputClasses' => $this->m_inputClassList,
            'rootClasses'  => $this->m_rootClassList
        ]);
    }
}
