<?php

namespace EscolaSoft\LaravelH5p\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\App;

class H5pBundleLayout extends Component {
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-bundle-layout');
    }
}