<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Eloquents\H5pLibrary;
use EscolaSoft\LaravelH5p\Http\Controllers\H5pController;
use EscolaSoft\LaravelH5p\Http\Controllers\LibraryController;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Client\Request;
use H5PCore;

class H5pLibraryAdministration extends Component
{
    use WithPagination;

    public $settings;
    public $last_update;
    public $hubOn;
    public $required_files;
    public $search = null;

    public $pagination = 10;
    public $showDeletionModal = false;
    public $libraryId;


    public function mount(){
        $this->initialize();
    }

    public function getEntriesProperty(){
        if($this->search){
            return H5pLibrary::where('title', 'LIKE', '%'.$this->search.'%')->paginate($this->pagination);
        }
        return H5pLibrary::paginate($this->pagination);
    }

    public function searchUpdating(){
        $this->refreshPage();
    }

    public function showDeletionModal($libraryId){
        $this->showDeletionModal = true;
        $this->libraryId = $libraryId;
    }

    public function delete(){
        $result = LibraryController::destroy($this->libraryId);
        $this->libraryId = null;
        $this->showDeletionModal = false;
        if(empty($result)){
            session()->flash('h5p.delete.error', trans('laravel-h5p.library.db_delete_failed'));
            return;
        }
        elseif(!$result['result']){
            session()->flash('h5p.delete.error', $result['message']);
            return;
        }
        session()->flash('h5p.delete.success', $result['message']);
    }

    public function restricted($libraryId){
        $entry = H5pLibrary::where('id', $libraryId)->first();
        $result = null;
        if(!$entry){
            session()->flash('h5p.restricted.error', 'Bibliothek mit ID '.$libraryId.' konnte nicht gefunden werden.');
            return redirect(request()->header('Referer'));
        }
        if($entry){
            $entry->restricted === 1 ? $entry->restricted = 0 : $entry->restricted = 1;
            $result = $entry->update();
            if($result == true){
                session()->flash('h5p.restricted.success', 'Status von '.$entry->title.' geupdatet.');
                return redirect(request()->header('Referer'));
            }
            if($result == false){
                session()->flash('h5p.restricted.error', 'Status konnte nicht geupdatet werden.');
                return redirect(request()->header('Referer'));
            }
        }
    }

    protected function initialize(){
        $h5p = App::make('LaravelH5p');
        // $core = $h5p::$core;
        $interface = $h5p::$interface;
        $not_cached = $interface->getNumNotFiltered();

        $this->settings = $h5p::get_core([
            'libraryList' => [
                'notCached' => $not_cached,
            ],
            'containerSelector' => '#h5p-admin-container',
            'extraTableClasses' => '',
            'l10n'              => [
                'NA'             => trans('laravel-h5p.common.na'),
                'viewLibrary'    => trans('laravel-h5p.library.viewLibrary'),
                'deleteLibrary'  => trans('laravel-h5p.library.deleteLibrary'),
                'upgradeLibrary' => trans('laravel-h5p.library.upgradeLibrary'),
            ],
        ]);

        foreach($this->entries as $library){
            $usage = $interface->getLibraryUsage($library->id, $not_cached ? true : false);
            $this->settings['libraryList']['listData'][] = (object) [
                'id' => $library->id,
                'title' => $library->title.' ('.H5PCore::libraryVersion($library).')',
                'restricted' => ($library->restricted ? true : false),
                'numContent'             => $interface->getNumContent($library->id),
                'numContentDependencies' => intval($usage['content']),
                'numLibraryDependencies' => intval($usage['libraries']),
            ];
        }

        $this->last_update = config('laravel-h5p.h5p_content_type_cache_updated_at');

        $this->required_files = LibraryController::assets(['js/h5p-library-list.js']);

        if ($not_cached) {
            $settings['libraryList']['notCached'] = LibraryController::get_not_cached_settings($not_cached);
        } else {
            $settings['libraryList']['notCached'] = 0;
        }
        $this->hubOn = config('laravel-h5p.h5p_hub_is_enabled');
    }


    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-library-administration');
    }
}