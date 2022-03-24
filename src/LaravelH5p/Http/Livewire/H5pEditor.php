<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use H5PCore;
use Livewire\Component;

class H5pEditor extends Component
{
    public $library = 0;
    public $parameters = '{}';
    public $nonce;
    public $display_options;
    public $settings;
    public $content;
    public $mode = 'create'; //possible values are 'create' && 'edit'
    public $contentId;

    protected $h5p;
    protected $core;


    public function mount(){
        if($this->mode == 'create'){
            $this->initializeCreate();
        } elseif ($this->mode == 'edit'){
            $this->initializeEdit($this->contentId);
        } //TODO: else show error message?
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-editor');
    }

    private function initializeCreate(){
        $this->library = 0;
        $this->parameters = '{}';
        // $this->nonce = bin2hex(random_bytes(4));
        $this->h5p = App::make('LaravelH5p');
        $this->core = $this->h5p::$core;
        $this->display_options =$this->core->getDisplayoptionsForEdit(null);
        $this->settings = $this->h5p::get_editor();
        $this->settings['editor']['ajaxPath'] .= $this->nonce . '/';
        $this->settings['editor']['filesPath'] = asset('storage/h5p/editor');
        event(new H5pEvent('content', 'new'));
    }

    private function initializeEdit($contentId){
        // $this->nonce = bin2hex(random_bytes(4));
        $this->h5p = App::make('LaravelH5p');
        $this->core = $this->h5p::$core;
        $editor = $this->h5p::$h5peditor;
        $this->settings = $this->h5p::get_core();
        $this->content = $this->h5p->get_content($contentId);

        $embed = $this->h5p->get_embed($this->content, $this->settings);
        $embed_code = $embed['embed'];
        $this->settings = $embed['settings'];

        $this->library = $this->content['library'] ? H5PCore::libraryToString($this->content['library']) : 0;

        $this->parameters = json_encode([
            'params' => json_decode($this->content['params']),
            'metadata' => ['title' => $this->content['title']]
        ]);

        $this->display_options = $this->core->getDisplayOptionsForEdit($this->content['disable']);

        $this->settings = $this->h5p::get_editor($this->content);
        $this->settings['editor']['filesPath'] = asset('storage/h5p/content/' . $this->content['id']);
        event(new H5pEvent('content', 'edit', $this->content['id'], $this->content['title'], $this->content['library']['name'], $this->content['library']['majorVersion'] . '.' . $this->content['library']['minorVersion']));
    }
}