<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use H5PCore;
use Livewire\Component;

class H5pFlash extends Component
{
    public $title;
    public $message;
    public $type = 'positive'; //positive | negative

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-flash');
    }
}