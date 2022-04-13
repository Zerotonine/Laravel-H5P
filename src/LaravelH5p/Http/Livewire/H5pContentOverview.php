<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Http\Controllers\H5pController;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Client\Request;

class H5pContentOverview extends Component
{
    use WithPagination;

    public $pagination = 10;
    public $showModal = false;
    public $showEditor = false;
    public $showContent = false;
    public $bundleMode = false;
    public $search = '';

    public $contentId = null;
    public $title = null;
    public $nonce;

    protected $h5p;
    protected $core;
    protected Request $request;
    public $settings;


    public function getEntriesProperty(){
        if($this->search){
            return H5pContent::where('title', 'LIKE', '%'.$this->search.'%')->orderBy('h5p_contents.id', 'desc')->paginate($this->pagination);
        }
        return H5pContent::orderBy('h5p_contents.id', 'desc')->paginate($this->pagination);
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    protected function getListeners()
    {
        return ['closeEditor' => 'closeEditor'];
    }

    public function mount(){
        $this->h5p = App::make('LaravelH5p');
        $this->core = $this->h5p::$core;
        $this->settings = $this->h5p::get_editor();
    }

    public function showModal($id, $title){
        $this->showModal = true;
        $this->contentId = $id;
        $this->title = $title;
    }

    public function delete(){
        $this->showModal = false;
        if(!$this->contentId || !H5pController::destroy($this->contentId)){
            session()->flash('h5p.deleted.error', 'H5P konnte nicht gelöscht werden.');
        } else {
            session()->flash('h5p.deleted.complete', 'H5P ' . $this->title . ' gelöscht.');
        }
        $this->contentId = null;
    }

    public function newContent(){
        $this->nonce = bin2hex(random_bytes(4));
        $this->showEditor = true;
        $this->contentId = null;
        // $this->emit('editorOpened');
        $this->emit('editorOpened', $this->nonce);
    }

    public function closeModal(){
        $this->showModal = false;
    }

    public function showEditor($id){
        $this->showEditor = true;
        $this->contentId = $id;
        $this->nonce = bin2hex(random_bytes(4));
        //laravel-h5p.js is listening for this...
        $contentPath = asset('storage/h5p/content/' . $id);
        $this->emit('editorOpened', $this->nonce, $contentPath, $id);
    }

    public function closeEditor(){
        $this->showEditor = false;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-content-overview');
    }
}