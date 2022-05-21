<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Http\Controllers\H5pController;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as H5pBundles;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainerContents as ContainerContents;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class H5pBundle extends Component
{
    //TODO: Reduce roundtrips
    public $bundle;
    public $demo;

    public $background = null;

    protected $queryString = [
        'bundle' => ['except' => '', 'as' => 'b'],
        'demo' => ['except' => '', 'as' => 'd']
    ];

    public $activeContentId = null;
    public $title = "";
    public $showResults = false;
    public $backgound = null;
    public $watermark = null;
    public $watermark_opacity = 0;

    public function getEntriesProperty(){
        $bundle = H5pBundles::where(['id' => $this->bundle])->first() ?? null;
        $contents = null;
        if(!is_null($bundle)){
            $contents = $bundle->contents;
            $this->background = $bundle->background_path;
            $this->watermark = $bundle->watermark_path;
            $this->watermark_opacity = $bundle->watermark_opacity;
        } else {
            return null;
        }

        // $this->activeContentId = $contents[0]->id;

        return $contents;
    }

    public function mount(){
        $entries = $this->getEntriesProperty();
        // $this->activeContentId = $this->getEntriesProperty()[0]->id;
        $this->activeContentId = count($entries) > 0 ? $entries[0]->id : null;
        // if(!empty($this->background)){
        //     $this->background = storage_path('app/'.$this->background);
        // }
        //dd($this->background);
    }

    public function switchContent($contentId){
        $this->showResults = false;
        $this->activeContentId = $contentId;
        $this->emit('contentChanged', $contentId);
    }

    public function showResults(){
        if($this->showResults){return;}
        $this->showResults = true;
        $this->activeContentId = null;
    }

    public function getTitle($contentId){
        return H5pContent::findOrFail(['id' => $contentId])->first()->title;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-bundle');
    }
}