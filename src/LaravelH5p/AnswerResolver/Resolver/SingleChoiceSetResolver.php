<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class SingleChoiceSetResolver extends Resolver {
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScorePerSubItem();
        $this->_getMaxScore();
        $this->_getResults();
        //dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        $tempChoices = $this->contentObj?->choices;
        foreach($tempChoices as $choice){
            array_push($this->questions, trim(strip_tags($choice?->question)));
        }
    }

    protected function _extractCorrectAnswers(){
        $tempChoices = $this->contentObj?->choices;
        $tempAnswers = [];
        if($tempChoices === null) {
            return;
        }
        foreach($tempChoices as $choice){
            $answers = $choice?->answers;
            /*foreach($answers as $n => $answer){
                $answer = trim(strip_tags($answer));
                if($n === 0){
                    array_push($tempAnswers, "<strong>".$answer."</strong>");
                } else {
                    array_push($tempAnswers, $answer);
                }
            }*/
            array_push($this->correctAnswers, trim(strip_tags($answers[0])));
            //$tempAnswers = [];
        }
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();
        if($contentUserData === null){
            $this->givenAnswers = [];
        }
        $data = $contentUserData?->data;
        $data = json_decode($data);
        $this->givenAnswers = $data?->userResponses ?? [];
    }

    protected function _getMaxScorePerSubItem(){
        $this->maxScorePerSubItem = $this->multipliers;
    }

    protected function _getMaxScore(){
        if(isset($this->multipliers)){
            $this->maxScore = array_sum($this->maxScorePerSubItem);
        }
    }

    protected function _getResults(){
        // dd($this->correctAnswers);
        // dd($this->array_flatten($this->correctAnswers));
        //dd($this->getQuestionsAndAnswers());
        if(empty($this->givenAnswers)){return;}
        $result = 0;

        if(!isset($this->maxScorePerSubItem)){
            array_push($this->results, $result);
            return;
        }
        foreach($this->givenAnswers as $n => $givenAnswer){
            if($givenAnswer === 0){ //correct answer is on index 0 in this ctype so the correct answer has to be 0
                $result += $this->maxScorePerSubItem[$n];
            }
        }
        array_push($this->results, $result);
    }

}