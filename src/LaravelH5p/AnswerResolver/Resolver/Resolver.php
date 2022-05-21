<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

use Illuminate\Support\Facades\Auth;

use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentsUserData;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as H5pBundles;


abstract class Resolver {
    public $type = '';
    protected $contentObj;
    protected $contentId;
    protected $content;
    protected $questions = [];
    protected $correctAnswers = [];
    protected $givenAnswers = [];
    protected $bundleId;
    protected $title;
    protected $maxScore = 0;
    protected $maxScorePerSubItem = [];
    protected $results = [];
    protected $contentUserData = null;
    protected $multipliers = null;

    private $params;

    public function __construct(int $contentId, int $bundleId = null, object $params = null, string $title = null)
    {
        $this->contentId = $contentId;
        $this->bundleId = $bundleId;
        $this->params = $params;
        if(isset($title)){
            $this->title = $title;
        }
        $this->_getContent();
        if(!isset($params)){
            $this->_getContentUserData();
        }
        // $this->_getMaxScorePerSubItem();
        //$this->_extractCorrectAnswers();
        //$this->_extractQuestions();
        //$this->_extractGivenAnswers();
    }

    abstract protected function _extractQuestions();
    abstract protected function _extractCorrectAnswers();
    abstract protected function _extractGivenAnswers();
    //abstract protected function _subContentGivenAnswers();


    /**
     * Returns an object with name (of the content package), array questions, array correct_answers and array given_answers
     * @return object
     */
    public function getQuestionsAndAnswers() : object {
        $retVal = (object) [
            'content_id' => $this?->contentId,
            'bundle_id' => $this?->bundleId,
            'name' => $this?->title,
            //'questions' => self::getQuestions(),
            'questions' => $this?->questions,
            'correct_answers' => $this?->correctAnswers,
            'given_answers' => $this?->givenAnswers,
            'max_score' => $this?->maxScore,
            'max_score_per_sub_item' => $this?->maxScorePerSubItem,
            'multipliers' => $this?->multipliers,
            'results' => $this?->results,
            'type' => $this?->type,
        ];

        return $retVal;
    }

    /**
     * Calculates the MaxScore, has to be overriden for some types
     */
    protected function _getMaxScore(){
        $this->maxScore = count($this->questions);
    }

    /**
     * Builds an Array with the maxScore of every single sub type (item),
     * important for applying score multipliers for calculating results.
     */
    protected function _getMaxScorePerSubItem(){
        $this->maxScorePerSubItem = [$this->maxScore];
    }

    /**
     * Builds an Array with the quiz results based on content user data,
     * necessary for applying multipliers
     */
    protected function _getResults(){

    }

    private function _getContent(){
        if($this->params === null){
            $this->content = H5pContent::where(['id' => $this->contentId])->first();

            if($this->content){
                $this->title = $this->content?->title;
                $this->contentObj = json_decode($this->content?->parameters);
            }
        } else {
            $this->title = $this->title;
            $this->contentObj = $this->params;
        }
    }

    protected function _getContentUserData() {
        $uid = Auth::user()?->id;
        $contentUserData = null;
        $multipliers = null;
        if($this->bundleId){
            $contentUserData = H5pContentsUserData::where([
                'user_id' => $uid, 'content_id' => $this->contentId,
                'container_id' => $this->bundleId])->first();

            $multipliers = H5pBundles::where(['id' => $this->bundleId])->first();
        } else {
            $contentUserData = H5pContentsUserData::where([
                'user_id' => $uid,
                'content_id' => $this->contentId
            ])->first();
        }

        if(isset($multipliers)){
            $multipliers = json_decode($multipliers->multipliers, true);
            if(is_array($multipliers) && in_array($this->contentId, array_keys($multipliers))){
                $this->multipliers = $multipliers[$this->contentId];
            }
        }

        $this->contentUserData = isset($contentUserData) ? json_decode($contentUserData) : null;
        return isset($contentUserData) ? json_decode($contentUserData) : null;
    }

    /**
     * Returns Questions
     * @return array Questions
     */
    public function getQuestions() : array{
        return $this->questions;
    }

    public function getCorrectAnswers() : array {
        return $this->correctAnswers ?? [];
    }

    public function getGivenAnswers() : array {
        return $this->givenAnswers ?? [];
    }

    /**
     * returns the maxScore
     * @return int maxScore
     */
    public function getMaxScore() : int {
        return $this->maxScore ?? 0;
    }

    protected function array_flatten($array) {
        if (!is_array($array)) {
          return FALSE;
        }
        $result = array();
        foreach ($array as $key => $value) {
          if (is_array($value)) {
            $result = array_merge($result, self::array_flatten($value));
          }
          else {
            $result[$key] = $value;
          }
        }
        return $result;
    }
}