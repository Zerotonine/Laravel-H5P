<?php

namespace  EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class MultiChoiceResolver extends Resolver {
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
        $tempQuestions = array($this->contentObj?->question);
        foreach($tempQuestions as $question){
            array_push($this->questions, trim(strip_tags($question)));
        }
    }

    protected function _extractCorrectAnswers(){
        $answers = $this->contentObj?->answers;

        $tempAnswers = [];
        foreach($answers as $answer){
            $answer->text = trim(strip_tags($answer->text));
            if($answer->correct){
                array_push($tempAnswers, $answer->text);
            }
        }
        array_push($this->correctAnswers, $tempAnswers);
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();
        if($contentUserData === null){
            $this->givenAnswers = [];
            return;
        }
        $data = $contentUserData?->data;
        $data = json_decode($data);
        $this->givenAnswers = $data?->answers ?? [];
    }

    protected function _getMaxScore(){
        $multiplier = null;
        if(!isset($this->multipliers)){
            $multiplier = 1;
        } else {
            $multiplier = $this->multipliers[0];
        }
        foreach($this->correctAnswers as $correctAnswer){
            $this->maxScore += count($correctAnswer) * $multiplier;
        }
    }

    protected function _getResults(){
        // dd($this->correctAnswers);
        // dd($this->array_flatten($this->correctAnswers));
        //dd($this->getQuestionsAndAnswers());
        if(empty($this->givenAnswers)){return;}
        $answers = $this->contentObj?->answers;
        $result = 0;
        foreach($this->givenAnswers as $givenAnswer){
            if($answers[$givenAnswer]->correct){
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