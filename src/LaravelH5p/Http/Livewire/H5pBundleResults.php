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

class H5pBundleResults extends Component
{
    public $entries = null;
    public $bundleId = null;
    protected $scores = null;

    public function getResultsProperty(){
        // return H5pBundles::where(['id' => $this->bundleId])->first();
        $results = H5pResult::where(['container_id' => $this->bundleId, 'user_id' => Auth::user()->id])->get();
        $retVal = ['score' => [], 'max_score' => []];

        if(!$results){
            return null;
        }

        foreach($this->entries as $entry){
            $found = false;
            foreach($results as $result){
                if($result->content_id === $entry->id){
                    array_push($retVal['score'], $result->score);
                    array_push($retVal['max_score'], $result->max_score);
                    $found = true;
                    break;
                }
            }
            if(!$found){
                array_push($retVal['score'], 'n/a');
                array_push($retVal['max_score'], 'n/a');
            }
        }
        $this->scores = $retVal;
        return $retVal;
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
        return round($a / $b * 100);
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