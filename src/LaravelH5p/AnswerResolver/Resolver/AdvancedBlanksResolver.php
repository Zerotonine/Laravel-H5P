<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class AdvancedBlanksResolver extends Resolver{
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
        $this->_getResults();
        // dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        $content = $this->contentObj?->content;
        if(!isset($content)){
            return;
        }
        $task = trim(strip_tags($content->task));
        $blanksText = trim(strip_tags($content->blanksText));
        $blanksText = preg_replace_callback('/[_]{3,}/', function(){
            static $i = 0;
            return '_'.++$i.'_';
        }, $blanksText);
        array_push($this->questions, $task.'<br/>'.$blanksText);
    }

    protected function _extractCorrectAnswers(){
        $content = $this->contentObj?->content;
        if(!isset($content)){
            return;
        }
        $blanksList = $content->blanksList;
        $answers = [];
        foreach($blanksList as $blank){
            array_push($answers, trim(strip_tags($blank->correctAnswerText)));
        }
        array_push($this->correctAnswers, $answers);
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();
        if(!isset($contentUserData)){
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
        foreach($this->correctAnswers[0] as $n => $correctAnswer){
            if($correctAnswer === $this->givenAnswers[$n]){
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