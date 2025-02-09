<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class DocProofUpsertModal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $as = '',
        public $title = '',
        public $createAction = '',
        public $updateAction = '',
        public $fetchAction = '',
        public $formId = ''
    )
    {
        $this->as = $as ?: 'docproof-upsert-modal-'.Str::random(10);
        $this->title = $title ?: 'Doc Proof';

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('x-components.doc-proof-upsert-modal');
    }
}
