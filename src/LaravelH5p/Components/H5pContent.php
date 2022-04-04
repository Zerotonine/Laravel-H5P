<?php

namespace EscolaSoft\LaravelH5p\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\App;

class H5pContent extends Component
{
    public $contentId = null;
    public $width = null;
    public $height = null;
    // public $settings = null;
    // public $embed_code = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($contentId, $width = "1200", $height = "auto")
    {
        $this->contentId = $contentId;
        $this->width = $width;
        $this->height = $height;
        // $this->initialize($contentId);
    }

    protected function initialize($contentId){
        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $this->settings = $h5p::get_editor();
        $content = $h5p->get_content($contentId);
        $embed = $h5p->get_embed($content, $this->settings);
        $this->embed_code = $embed['embed'];
        $this->settings = $embed['settings'];
        // $user = Auth::user();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('lh5p::h5p-content');
    }
}
