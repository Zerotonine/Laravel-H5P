<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class SummaryResolver extends Resolver {
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
        $intro = $this->contentObj?->intro;
        if(!isset($intro)){
            return;
        }
        $intro = trim(strip_tags($intro));
        array_push($this->questions, $intro);
    }

    protected function _extractCorrectAnswers(){
        //dd($this->contentObj);
        if(!isset($this->contentObj->summaries) || !isset($this->contentObj->summaries[0]->summary)){
            return;
        }

        $summaries = $this->contentObj?->summaries;
        $correctAnswers = [];
        //dd($summaries);
        foreach($summaries as $summary){
            array_push($correctAnswers, trim(strip_tags($summary->summary[0]))); //right answer must be at index 0
        }
        array_push($this->correctAnswers, $correctAnswers);
        //dd($correctAnswers);
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();
        if(!isset($contentUserData)){
            return;
        }
        $summaries = $this->contentObj?->summaries;
        if(!isset($summaries)){
            return;
        }
        $data = $contentUserData?->data;
        $data = json_decode($data);
        $summaryCount = count($summaries);
        if(count($data->answers) === 0){
            foreach($summaries as $summary){
                array_push($this->givenAnswers, [0]);
            }
        } elseif(count($data->answers) === $summaryCount){
            foreach($data->answers as $answer){
                if(isset($answer)){
                    array_push($this->givenAnswers, $answer);
                } else {
                    array_push($this->givenAnswers, [0]);
                }
            }
        } else {
            foreach($data->answers as $answer){
                if(isset($answer)){
                    array_push($this->givenAnswers, $answer);
                } else {
                    array_push($this->givenAnswers, [0]);
                }
            }
            $delta = $summaryCount - count($data->answers);
            for($i = 0; $i < $delta; $i++){
                array_push($this->givenAnswers, [0]);
            }
        }
    }

    protected function _getMaxScore()
    {
        foreach($this->correctAnswers as $correctAnswer){
            $this->maxScore += count($correctAnswer);
        }
    }
}