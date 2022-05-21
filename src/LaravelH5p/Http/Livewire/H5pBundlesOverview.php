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
use Livewire\WithFileUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use EscolaSoft\LaravelH5p\Exporter\FeedbackExporter;


//TODO: remove
use EscolaSoft\LaravelH5p\AnswerResolver\Helpers\AnswerResolverHelper;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\BlanksResolver;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\CoursePresentationResolver;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\ResolverFactory;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\SingleChoiceSetResolver;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\TrueFalseResolver;

class H5pBundlesOverview extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $pagination = 10;

    public $showCreateModal = false;
    public $showDeletionModal = false;
    public $showAddPackages = false;
    public $bundleId = null;
    public $bundleTitle = null;
    public $selectedPackages = [];
    public $search;
    public $notSupported = [];
    public $questionsInBundle = [];

    public $libs = [];

    public $background;
    public $background_id = 0;
    public $background_path = null;

    public $watermark;
    public $watermark_id = 0;
    public $watermark_path = null;
    public $watermark_opacity = 0;
    public $multipliers = null;

    public $settingsState = false;
    public $bundleContentState = false;
    public $questionsState = false;
    public $exportState = false;

    public $questionnaire = null;

    public function getExportState(){
        return $this->exportState ? 'open' : '';
    }

    public function getSettingsState(){
        return $this->settingsState ? 'open' : '';
    }

    public function getBundleContentState(){
        return $this->bundleContentState ? 'open' : '';
    }

    public function getQuestionsState(){
        return $this->questionsState ? 'open' : '';
    }

    protected function getListeners(){
        return ['closeBundleCreateModal' => 'closeBundleCreateModal'];
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function getEntriesProperty(){
        if($this->search){
            return H5pBundles::where(['user_id' => Auth::user()->id])->where('title', 'LIKE', '%' .$this->search.'%')->orderBy('id', 'desc')->paginate($this->pagination);
        }
        return H5pBundles::where(['user_id' => Auth::user()->id])->orderBy('id', 'desc')->paginate($this->pagination);
    }

    //TODO: remove
    public function debug(){
        //$libs = AnswerResolverHelper::getBundleLibraries($this->bundleId);
        //$answer = AnswerResolverHelper::getNotSupportedTypes($libs);
        //$tfContent = new TrueFalseResolver($libs[0]->contentId, $this?->bundleId);
        //dd($tfContent->getQuestions()[0]);
        // dd($tfContent->getCorrectAnswers());
        //dd($tfContent->getGivenAnswers());
        //$this->setQuestionsInBundle();
        //dd($this->multipliers);
        // dd($this->questionsInBundle);
        dd($this->questionnaire);
    }

    public function showDeletionModal($bundleId){
        $this->bundleId = $bundleId;
        $this->showDeletionModal = true;
    }

    public function deleteBundle(){
        if(!$this->checkUserBundleAccess($this->bundleId)){
            return;
        }
        H5pBundles::where(['id' => $this->bundleId])->delete();
        $this->showDeletionModal = false;
        $this->bundleId = null;
        session()->flash('h5p.deleted.complete', 'Inhalt erfolgreich gelöscht');
    }

    private function _getMultipliers(){
        $multipliers = H5pBundles::select('multipliers')->where(['id' => $this->bundleId])->first();
        if(isset($multipliers) && trim($multipliers->multipliers) !== ""){
            $this->multipliers = $multipliers->multipliers;
        }
        $this->emit('multipliersSet');
    }

    public function saveMultipliers($jsonMultipliers){
        $multipliers = H5pBundles::where(['id' => $this->bundleId])->first();
        $multipliers->multipliers = json_encode($jsonMultipliers);
        $multipliers->save();
        //$this->multipliers = json_encode($jsonMultipliers);
    }

    public function getContentsProperty(){
        $contents = H5pContent::where(['user_id' => Auth::user()->id])->whereDoesntHave('bundles', function (Builder $query) {
            $query->where('container_id', $this->bundleId);
        })->orderBy('id', 'desc')->get();

        return $contents;
    }

    public function getBundlesProperty(){
        if($this->bundleId)
            return H5pBundles::where(['id' => $this->bundleId])->get();
    }

    public function addToBundle($contentId){
        if(!$this->checkUserContentAccess($contentId)){
            return;
        }
        if($this->bundleId && $contentId){
            ContainerContents::create(['content_id' => $contentId, 'container_id' => $this->bundleId]);
        }

        $this->libs = AnswerResolverHelper::getBundleLibraries($this->bundleId);
        $this->checkIfContentTypeIsSupported();
        $this->setQuestionsInBundle();
    }

    public function removeFromBundle($contentId){
        if($this->bundleId && $contentId){
            ContainerContents::where(['content_id' => $contentId, 'container_id' => $this->bundleId])->delete();
            $multipliers = H5pBundles::where(['id' => $this->bundleId])->first();
            $json = json_decode($multipliers->multipliers, true);
            unset($json[$contentId]);
            $multipliers->multipliers = json_encode($json);
            $multipliers->save();
        }

        //TODO: find a better way for lib updating
        // $this->questionsInBundle = array();
        $this->libs = AnswerResolverHelper::getBundleLibraries($this->bundleId);
        $this->checkIfContentTypeIsSupported();
        $this->setQuestionsInBundle();
    }

    private function checkUserContentAccess($contentId){
        $content = H5pContent::where(['id' => $contentId])->first();
        if(!isset($content) || $content->user_id !== Auth::user()->id){
            return false;
        }
        return true;
    }

    public function closeBundleCreateModal(){
        $this->showCreateModal = false;
    }

    public function showAddPackages($bundleId, $bundleTitle){
        // $this->resetPage();
        if(!$this->checkUserBundleAccess($bundleId)){return;}
        $this->showAddPackages = true;
        $this->bundleId = $bundleId;
        $this->bundleTitle = $bundleTitle;
        $this->libs = AnswerResolverHelper::getBundleLibraries($bundleId);
        $this->checkIfContentTypeIsSupported();

        $this->_getBackgroundAndWatermark();
        $this->setQuestionsInBundle();
        $this->_getMultipliers();
    }

    private function checkUserBundleAccess($bundleId):bool{
        $bundle = H5pBundles::where(['id' => $bundleId])->first();
        if(!isset($bundle) || $bundle->user_id !== Auth::user()->id){
            return false;
        }
        return true;
    }

    private function checkIfContentTypeIsSupported(){
        //$libs = AnswerResolverHelper::getBundleLibraries($this->bundleId);
        if($this->libs){
            $this->notSupported = [];
            $this->notSupported = AnswerResolverHelper::getNotSupportedTypes($this->libs);
        }
    }

    //TODO: REFACTOR
    private function setQuestionsInBundle(){
        $updatedMultipliers = false;
        $this->questionsInBundle = array();
        $this->questionnaire = null;
        $supported = AnswerResolverHelper::getSupportedLibraries($this->libs);
        $multipliers = H5pBundles::where(['id' => $this->bundleId])->first();
        $json = json_decode($multipliers->multipliers, true);
        if(!isset($json)){
            $json = array();
        }
        //dd($this->libs);
        //TODO: refactor and give this function a better name
        foreach($supported as $sup){
            $resolver = ResolverFactory::getResolver($sup->machineName, $sup->contentId, $this->bundleId);
            // if($sup->name === 'Questionnaire'){$this->questionnaire = (array)$resolver->getQuestionsAndAnswers(); continue;}
            if($sup->name === 'Questionnaire'){$this->questionnaire = $sup->contentId; continue;}

            $questionsAndAnswers = $resolver->getQuestionsAndAnswers();
            if(!array_key_exists("$sup->contentId", $json)){
                $temp = [$sup->contentId => []];
                for($i = 0; $i < count($questionsAndAnswers->questions); $i++){
                    array_push($temp[$sup->contentId], 1);
                }
                $json += $temp;
                $multipliers->multipliers = json_encode($json);
                $multipliers->save();
                $updatedMultipliers = true;
            }
            array_push($this->questionsInBundle, $questionsAndAnswers);
        }
        //dd($this->questionsInBundle);
        if($updatedMultipliers){
            $this->_getMultipliers();
            $this->emit('updatedMultipliers');
        }
    }


    public function closeAddPackages(){
        $this->showAddPackages = false;
        $this->bundleId = null;
        $this->bundleTitle = null;
        $this->questionsInBundle = array();
        $this->questionnaire = null;
    }

    public function selectPackage($contentId){
        array_push($this->selectedPackages, $contentId);
    }

    //TODO: check file upload
    public function save(){
        $this->validate([
            'background' => 'image|max:4096',
        ]);
        $filename = $this->background->getFilename();
        $final_path = 'public/h5p/bundles/'.$this->bundleId.'/background/'.$filename;
        $pubPath = '/storage/h5p/bundles/'.$this->bundleId.'/background/'.$filename;

        $files = Storage::files('public/h5p/bundles/'.$this->bundleId.'/background/');
        if(!empty($files)){
            Storage::delete($files);
        }
        Storage::move('livewire-tmp/'.$filename, $final_path);
        $bundle = H5pBundles::where(['id' => $this->bundleId])->first();
        $bundle->background_path = $pubPath;
        $bundle->save();

        $this->background_path = $pubPath;
        $this->background = null;
        $this->background_id++;
    }

    public function saveWatermark(){
        $bundle = H5pBundles::where(['id' => $this->bundleId])->first();
        if($this->watermark){
            $this->validate([
                'watermark' => 'image|max:2048',
            ]);
            $filename = $this->watermark->getFilename();
            $final_path = 'public/h5p/bundles/'.$this->bundleId.'/watermark/'.$filename;
            $pubPath = '/storage/h5p/bundles/'.$this->bundleId.'/watermark/'.$filename;

            $files = Storage::files('public/h5p/bundles/'.$this->bundleId.'/watermark/');
            if(!empty($files)){
                Storage::delete($files);
            }
            Storage::move('livewire-tmp/'.$filename, $final_path);
            $bundle->watermark_path = $pubPath;
            $this->watermark_path = $pubPath;
        }
        if($this->watermark_opacity > 100) {
            $this->watermark_opacity = 100;
        } elseif ($this->watermark_opacity < 0){
            $this->watermark_opacity = 0;
        }
        $bundle->watermark_opacity = $this->watermark_opacity;
        $bundle->save();

        $this->watermark = null;

        $this->watermark_id++;
    }

    private function _getBackgroundAndWatermark(){
        $bundle = H5pBundles::select(['background_path', 'watermark_path', 'watermark_opacity'])->where(['id' => $this->bundleId])->first();
        $this->background_path = $bundle?->background_path;
        $this->watermark_path = $bundle?->watermark_path;
        $this->watermark_opacity = $bundle?->watermark_opacity;
    }

    public function exportFeedback(){
        if(!isset($this->questionnaire)){return;}
        $resolver = ResolverFactory::getResolver('H5P.Questionnaire', $this->questionnaire, $this->bundleId);
        $downloadPath = FeedbackExporter::exportFeedback((array)$resolver->getQuestionsAndAnswers());
        return response()->download($downloadPath, time().'_feedback.xlsx');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        //ensure that values are objects, otherwise livewire makes arrays out of them after every rerender...>.>
        if(isset($this->questionsInBundle) && !empty($this->questionsInBundle) && is_array($this->questionsInBundle[0])){
            foreach($this->questionsInBundle as $n => $item){
                $this->questionsInBundle[$n] = (object)$this->questionsInBundle[$n];
            }
        }
        return view('lh5p::h5p-bundles-overview');
    }
}