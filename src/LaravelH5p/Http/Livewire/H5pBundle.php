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
    public $bundle;
    public $demo;

    protected $queryString = [
        'bundle' => ['except' => '', 'as' => 'b'],
        'demo' => ['except' => '', 'as' => 'd']
    ];

    public $activeContentId = null;
    public $title = "";

    public function getEntriesProperty(){
        $bundle = H5pBundles::where(['id' => $this->bundle])->first() ?? null;
        $contents = null;
        if(!is_null($bundle)){
            $contents = $bundle->contents;
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
    }

    public function switchContent($contentId){
        $this->activeContentId = $contentId;
        $this->emit('contentChanged', $contentId);
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