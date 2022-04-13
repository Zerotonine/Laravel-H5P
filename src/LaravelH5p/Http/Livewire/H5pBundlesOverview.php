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
use Illuminate\Support\Facades\Route;

class H5pBundlesOverview extends Component
{
    use WithPagination;

    public $pagination = 10;

    public $showCreateModal = false;
    public $showAddPackages = false;
    public $bundleId = null;
    public $bundleTitle = null;
    public $selectedPackages = [];
    public $search;

    protected function getListeners(){
        return ['closeBundleCreateModal' => 'closeBundleCreateModal'];
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function getEntriesProperty(){
        if($this->search){
            return H5pBundles::where('title', 'LIKE', '%' .$this->search.'%')->orderBy('id', 'desc')->paginate($this->pagination);
        }
        return H5pBundles::orderBy('id', 'desc')->paginate($this->pagination);
    }

    public function getContentsProperty(){
        $contents = H5pContent::whereDoesntHave('bundles', function (Builder $query) {
            $query->where('container_id', $this->bundleId);
        })->orderBy('id', 'desc')->get();

        return $contents;
    }

    public function getBundlesProperty(){
        if($this->bundleId)
            return H5pBundles::where('id', $this->bundleId)->get();
    }

    public function addToBundle($contentId){
        if($this->bundleId && $contentId){
            ContainerContents::create(['content_id' => $contentId, 'container_id' => $this->bundleId]);
        }
    }

    public function removeFromBundle($contentId){
        if($this->bundleId && $contentId){
            ContainerContents::where(['content_id' => $contentId, 'container_id' => $this->bundleId])->delete();
        }
    }

    public function closeBundleCreateModal(){
        $this->showCreateModal = false;
    }

    public function showAddPackages($bundleId, $bundleTitle){
        $this->resetPage();
        $this->showAddPackages = true;
        $this->bundleId = $bundleId;
        $this->bundleTitle = $bundleTitle;
    }

    public function closeAddPackages(){
        $this->showAddPackages = false;
        $this->bundleId = null;
        $this->bundleTitle = null;
    }

    public function selectPackage($contentId){
        array_push($this->selectedPackages, $contentId);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-bundles-overview');
    }
}