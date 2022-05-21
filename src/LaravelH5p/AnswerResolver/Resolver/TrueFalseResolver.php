<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;


class TrueFalseResolver extends Resolver{
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
    }

    protected function _extractQuestions(){
        //$this->questions = array($this->contentObj?->question);
        $tempQuestions = array($this->contentObj?->question);
        foreach($tempQuestions as $question){
            array_push($this->questions, trim(strip_tags($question)));
        }
    }

    protected function _extractCorrectAnswers(){
        $this->correctAnswers = [$this->contentObj?->correct];
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();
        if($contentUserData === null){
            $this->givenAnswers = [];
            return;
        }
        $data = $contentUserData?->data;
        $this->givenAnswers = array(json_decode($data)?->answer);
    }

}