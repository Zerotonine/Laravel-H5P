<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class MarkTheWordsResolver extends Resolver {
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
        //dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        $taskDescription = $this->contentObj?->taskDescription;
        $taskDescription = trim(strip_tags($taskDescription));
        $textField = $this->contentObj?->textField;
        $textField = trim(strip_tags($textField));
        $textField = str_replace('*', '', $textField);
        array_push($this->questions, $taskDescription.'<br/>'.$textField);
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
        foreach($this->correctAnswers as $correctAnswer){
            $this->maxScore += count($correctAnswer);
        }
    }
}