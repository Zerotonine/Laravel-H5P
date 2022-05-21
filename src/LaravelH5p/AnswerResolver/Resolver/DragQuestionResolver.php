<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class DragQuestionResolver extends Resolver {
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getContentUserData();
        $this->_getMaxScore();
        $this->_getResults();
        //dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        $this->questions = [$this->title]; //drag and drop has no question(s) so title is used to show something in the frontend
        //dd($this->contentObj);
        // $content = $this->contentObj?->content;
        // if(!isset($content)){
        //     return;
        // }
        // $task = trim(strip_tags($content->task));
        // $blanksText = trim(strip_tags($content->blanksText));
        // $blanksText = preg_replace_callback('/[_]{3,}/', function(){
        //     static $i = 0;
        //     return '_'.++$i.'_';
        // }, $blanksText);
        // array_push($this->questions, $task.'<br/>'.$blanksText);
    }

    private function correctAnswerHelper(array|object $element){
        $type = $element?->type;
        $library = preg_replace('/.?+[0-9.]+$/', '', $type->library);

        if($library === 'H5P.AdvancedText'){
            return trim(strip_tags($type->params->text));
        } else if($library === 'H5P.Image'){
            return trim($type->params->alt);
        }
    }

    protected function _extractCorrectAnswers(){
        //dd($this->contentObj);
        $dropZones = $this?->contentObj?->question?->task?->dropZones;
        $elements = $this?->contentObj?->question?->task?->elements;
        if(!isset($dropZones) || !isset($elements)){
            return;
        }
        //dd($dropZones);
        //dd($elements);
        $tempAnswers = [];

        foreach($dropZones as $n => $dropZone){
            $label = trim(strip_tags($dropZone->label));
            $suffixes = [];
            if(!isset($dropZone->correctElements) || empty($dropZone->correctElements)){continue;}

            foreach($dropZone->correctElements as $correctElement){ //correctElement == element index
                array_push($suffixes, $this->correctAnswerHelper($elements[$correctElement]));
            }
            array_push($tempAnswers, $label.': '.implode(', ', $suffixes));
        }
        array_push($this->correctAnswers, $tempAnswers);
    }

    protected function _extractGivenAnswers(){
        //dd($this->contentUserData);
        if(!isset($this->contentUserData)){
            return;
        }
        $data = $this->contentUserData?->data;
        $data = json_decode($data);

        foreach($data->answers as $answer){
            if(!isset($answer)){
                array_push($this->givenAnswers, null);
                continue;
            }
            array_push($this->givenAnswers, $answer[0]->dz); //index in givenAnswer = index of element in contenObj, value = index of dropZone
        }
    }

    protected function _getMaxScore()
    {
        $multiplier = 1;
        if(isset($this->multipliers)){
            $multiplier = $this->multipliers[0];
        }
        $elements = $this?->contentObj?->question?->task?->elements;
        $this->maxScore = count($elements) * $multiplier;
    }

    protected function _getResults(){
        // dd($this->correctAnswers);
        // dd($this->array_flatten($this->correctAnswers));
        //dd($this->getQuestionsAndAnswers());
        if(empty($this->givenAnswers)){return;}

        $dropZones = $this->contentObj?->question?->task?->dropZones;
        $result = 0;
        foreach($this->givenAnswers as $n => $givenAnswer){
            if(!isset($givenAnswer)){
                continue;
            }
            $dz = $dropZones[$givenAnswer];
            if(in_array($n, $dz->correctElements)){
                $result++;
            }
        }
        if(isset($this->multipliers)){
            array_push($this->results, $result * $this->multipliers[0]);
            return;
        }
        array_push($this->results, $result);

        // if(empty($this->givenAnswers)){return;}

        // $result = 0;
        // foreach($this->correctAnswers[0] as $n => $correctAnswer){
        //     if($correctAnswer === $this->givenAnswers[$n]){
        //         $result++;
        //     }
        // }
        // if(isset($this->multipliers)){
        //     array_push($this->results, $result * $this->multipliers[0]);
        // }
        // array_push($this->results, $result);
    }
}