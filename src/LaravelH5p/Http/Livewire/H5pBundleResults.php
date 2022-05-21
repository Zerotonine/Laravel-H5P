<?php

namespace EscolaSoft\LaravelH5p\Http\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use EscolaSoft\LaravelH5p\Events\H5pEvent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Http\Controllers\H5pController;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as H5pBundles;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainerContents as ContainerContents;
use EscolaSoft\LaravelH5p\Eloquents\H5pResult;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use EscolaSoft\LaravelH5p\AnswerResolver\Helpers\AnswerResolverHelper;
use EscolaSoft\LaravelH5p\AnswerResolver\Resolver\ResolverFactory;
class H5pBundleResults extends Component
{
    public $entries = null;
    public $bundleId = null;
    protected $scores = null;
    protected $libs = null;
    protected $contents = [];
    protected $supportedLibraries = null;
    public $multipliers = null;

    public function mount(){
        $this->_setLibs();
        $this->_setSupportedLibraries();
        $this->_setContents();
        // $this->_getMultipliers();
        // dd($this->multipliers);
        //dd($this->contents);
        // dd($this->entries);
        //dd($this->supportedLibraries);
    }

    public function getResultsProperty(){
        // return H5pBundles::where(['id' => $this->bundleId])->first();
        $results = H5pResult::where(['container_id' => $this->bundleId, 'user_id' => Auth::user()->id])->get();
        $retVal = ['score' => [], 'max_score' => [], 'content_id' => []];

        if(!$results){
            return null;
        }
        $this->_getMultipliers();
        //dd($this->multipliers);
        foreach($this->entries as $n => $entry){
            $found = false;
            $resolver = $this->_getResolver($entry->id);

            if(isset($resolver)){
                $qna = $resolver->getQuestionsAndAnswers();
                array_push($retVal['score'], array_sum($qna->results));
                array_push($retVal['max_score'], $qna->max_score);
                array_push($retVal['content_id'], $qna->content_id);
            } else {
                $result_ids = array_column(json_decode($results, true), 'content_id');
                $index = array_search($entry->id, $result_ids);

                if(!$index === false){
                    array_push($retVal['score'], $results[$index]->score);
                    array_push($retVal['max_score'], $results[$index]->max_score);
                    array_push($retVal['content_id'], $results[$index]->content_id);
                } else {
                    array_push($retVal['score'], 'n/a');
                    array_push($retVal['max_score'], 'n/a');
                }
            }
            // foreach($results as $result){
            //     if($result->content_id === $entry->id){
            //         array_push($retVal['score'], $result->score * $this->multipliers[$entry->id][$n]);
            //         array_push($retVal['max_score'], $result->max_score * $this->multipliers[$entry->id][$n]);
            //         array_push($retVal['content_id'], $result->content_id);
            //         $found = true;
            //         break;
            //     }
            // }
            /*if(!$found){
                $resolver = $this->_getResolver($entry->id);

                if(is_null($resolver)){
                    array_push($retVal['score'], 'n/a');
                    array_push($retVal['max_score'], 'n/a');
                } else {
                    $qna = $resolver->getQuestionsAndAnswers();
                    $tempMaxScore = 0;

                    foreach($qna->max_score_per_sub_item as $m => $subScore){
                        $tempMaxScore += $subScore * $this->multipliers[$entry->id][$m];
                    }
                    array_push($retVal['score'], 0);
                    // array_push($retVal['max_score'], $resolver->getMaxScore() * $this->multipliers[$entry->id][$n]);
                    array_push($retVal['max_score'], $tempMaxScore);
                }
            }*/
        }
        $this->scores = $retVal;
        return $retVal;
    }

    private function _getResolver($contentId){
        $index = array_search($contentId, array_column($this->supportedLibraries, 'contentId'));
        if($index === false) {return null;}
        $resolver = ResolverFactory::getResolver($this->supportedLibraries[$index]->machineName, $contentId, $this->bundleId);
        return $resolver;
    }


    public function getTotalScoreProperty(){
        $scores = [];
        $scores['score'] = array_diff($this->scores['score'], array('n/a'));
        $scores['max_score'] = array_diff($this->scores['max_score'], array('n/a'));

        $scores['score'] = array_reduce($scores['score'], function ($a, $b){return $a+$b;}, 0);
        $scores['max_score'] = array_reduce($scores['max_score'], function ($a, $b){return $a+$b;}, 0);
        return (object)$scores;
    }

    public function getPercentage($a, $b){
        if($a === 0){
            return 0;
        }
        return round($a / $b * 100);
    }

    private function _setLibs(){
        if(isset($this->bundleId)){
            $this->libs = AnswerResolverHelper::getBundleLibraries($this->bundleId);
        }
    }

    private function _setSupportedLibraries(){
        if(isset($this->libs)){
            $this->supportedLibraries = AnswerResolverHelper::getSupportedLibraries($this->libs);
        }
    }

    private function _setContents(){
        if(isset($this->libs)){
            $supported = AnswerResolverHelper::getSupportedLibraries($this->libs);
            foreach($supported as $sup){
                $resolver = ResolverFactory::getResolver($sup->machineName, $sup->contentId, $this->bundleId);
                array_push($this->contents, $resolver->getQuestionsAndAnswers());
            }
        }
    }

    private function _getMultipliers(){
        $multipliers = H5pBundles::select('multipliers')->where(['id' => $this->bundleId])->first();
        if(isset($multipliers) && trim($multipliers->multipliers) !== ""){
            $this->multipliers = json_decode($multipliers->multipliers, true);
        }
        // dd($this->multipliers);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lh5p::h5p-bundle-results');
    }
}