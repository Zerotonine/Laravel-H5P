<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class DragTextResolver extends Resolver {
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
        $this->_getResults();
        //dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        $textField = $this->contentObj?->textField;
        $textField = trim(strip_tags($textField));
        $textField = preg_replace_callback('/[?<=*].*?[?=*]/',
            function() {
                static $i = 0;
                return '_'.++$i.'_';
            }, $textField
        );
        array_push($this->questions, $textField);
    }

    protected function _extractCorrectAnswers(){
        $textField = $this->contentObj?->textField;
        $textField = trim(strip_tags($textField));
        preg_match_all('/[?<=*](.*?)[?=*]/', $textField, $matches);
        if(count($matches) > 1){
            $this->correctAnswers = array($matches[1]);
        } else {
            $this->correctAnswers = [];
        }
    }

    protected function _extractGivenAnswers(){
        //TODO: check data structure
        $contentUserData = $this->_getContentUserData();
        if($contentUserData === null){
            $this->givenAnswers = [];
            return;
        }
        $data = $contentUserData?->data;
        $data = json_decode($data);
        $this->givenAnswers = $data ?? [];
    }

    protected function _getMaxScore()
    {
        $multiplier = 1;
        if(isset($this->multipliers)){
            $multiplier = $this->multipliers[0];
        }
        foreach($this->correctAnswers as $correctAnswer){
            //$this->maxScore += count($correctAnswer);
            $this->maxScore += count($correctAnswer) * $multiplier;
        }
    }

    protected function _getResults(){
        // dd($this->correctAnswers);
        // dd($this->array_flatten($this->correctAnswers));
        //dd($this->getQuestionsAndAnswers());
        if(empty($this->givenAnswers)){return;}

        $result = 0;
        foreach($this->givenAnswers as $givenAnswer){
            if(is_array($givenAnswer)){$givenAnswer = (object)$givenAnswer;}

            if($givenAnswer->draggable === $givenAnswer->droppable){
                $result++;
            }
        }
        if(isset($this->multipliers)){
            array_push($this->results, $result * $this->multipliers[0]);
            return;
        }
        array_push($this->results, $result);
    }
}