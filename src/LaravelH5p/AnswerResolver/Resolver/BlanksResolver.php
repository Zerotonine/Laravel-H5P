<?php

//RegEx [?<=*](.*?)[?=*]

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class BlanksResolver extends Resolver{
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        $this->_getContentUserData();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
        $this->_getMaxScorePerSubItem();
        $this->_getResults();
    }

    protected function _extractQuestions(){
        //$this->questions = $this->contentObj?->questions;
        $tempQuestions = $this->contentObj?->questions;
        foreach($tempQuestions as $question){
            $question = trim(strip_tags($question));
            //$question = preg_replace('/[?<=*].*?[?=*]/', '\n___', $question);
            $question = preg_replace_callback('/[?<=*].*?[?=*]/',
                function ($matches) {
                    static $i = 0;
                    $matches = '_'.++$i.'_';
                    return $matches;
                },
                $question
            );
            array_push($this->questions, $question);
        }
    }

    protected function _extractCorrectAnswers(){
        $tempQuestions = $this->contentObj?->questions;
        $allMatches = [];
        foreach($tempQuestions as $question){
            preg_match_all('/[?<=*](.*?)[?=*]/', trim(strip_tags($question)), $matches);
            array_push($allMatches, count($matches) > 1 ? $matches[1] : []);
        }
        $this->correctAnswers = $allMatches;
        //dd($this->correctAnswers);
    }

    protected function _extractGivenAnswers(){
        //$contentUserData = $this->_getContentUserData();
        $contentUserData = $this->contentUserData;
        if($contentUserData === null){
            $this->givenAnswers = [];
            return;
        }
        $data = $contentUserData?->data;
        $this->givenAnswers = json_decode($data);
    }

    protected function _getMaxScore(){
        foreach($this->correctAnswers as $n => $correctAnswer){
            //dd($this->multipliers);
            // $this->maxScore += count($correctAnswer);
            if(isset($this->multipliers) && array_key_exists($n, $this->multipliers)){
                $this->maxScore += count($correctAnswer) * $this->multipliers[$n];
            } else {
                $this->maxScore += count($correctAnswer);
            }
        }
    }

    protected function _getMaxScorePerSubItem(){
        foreach($this->correctAnswers as $correctAnswer){
            array_push($this->maxScorePerSubItem, count($correctAnswer));
        }
    }

    protected function _getResults(){
        // dd($this->correctAnswers);
        // dd($this->array_flatten($this->correctAnswers));
        //dd($this->getQuestionsAndAnswers());
        if(empty($this->givenAnswers)){return;}

        $helpIndex = 0;
        foreach($this->correctAnswers as $n => $correctAnswer){
            $result = 0;
            foreach($correctAnswer as $m => $answer){
                if($this->givenAnswers[$n+$m+$helpIndex] === $answer){
                    $result++;
                }
            }
            if(isset($this->multipliers) && array_key_exists($n, $this->multipliers)){
                array_push($this->results, $result * $this->multipliers[$n]);
            } else {
                array_push($this->results, $result);
            }
            $helpIndex++;
        }
    }
}