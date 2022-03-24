<?php

namespace EscolaSoft\LaravelH5p\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\App;

class H5pEditorModal extends Component
{

    public $contentId;
    public $settings;
    public $nonce;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($nonce = null, $contentId = null)
    {
        $this->contentId = $contentId;
        $this->nonce = $nonce;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('lh5p::h5p-editor-modal');
    }
}
