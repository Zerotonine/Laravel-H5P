<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Http\Controllers\H5pController;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as H5pBundles;
use Livewire\Component;
use Livewire\WithPagination;

class H5pBundleCreateModal extends Component
{

    public $title;

    protected $rules = [
        'title' => 'required|min:3'
    ];

    public function submit(){
        $this->validate();

        H5pBundles::create([
            'title' => $this->title,
            'user_id' => Auth::user()->id
        ]);
        $this->emit('closeBundleCreateModal');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-bundle-create-modal');
    }
}