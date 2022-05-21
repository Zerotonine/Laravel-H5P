<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

use EscolaSoft\LaravelH5p\Eloquents\H5pContent;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentsUserData;

use Illuminate\Support\Facades\DB;

class QuestionnaireResolver extends Resolver {
    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->type = 'Questionnaire';
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        if(!isset($params)){
            $this->_extractGivenAnswers();
        }
        $this->_getMaxScore();
        // dd($this->getQuestionsAndAnswers());
    }

    protected function _extractQuestions(){
        if(!isset($this->contentObj->questionnaireElements)){return;}
        $questionnaireElements = $this->contentObj?->questionnaireElements;
        if(!isset($questionnaireElements[0]->library)){return;}
        $questions = [];
        foreach($questionnaireElements as $element){
            $libName = preg_replace('/.?[0-9.]+$/', '', $element->library->library);
            $q = [];
            switch($libName){
                case 'H5P.SimpleMultiChoice':
                    $q['possibleAnswers'] = [];
                    if(isset($element->library->params->alternatives) && count($element->library->params->alternatives) > 0){
                        foreach($element->library->params->alternatives as $alternative){
                            array_push($q['possibleAnswers'], $alternative->text);
                        }
                    }
                case 'H5P.OpenEndedQuestion':
                    $q['type'] = $element->library->metadata->contentType;
                    $q['question'] = $element->library->params->question;
                    $q['subContentId'] = $element->library->subContentId;
                    $q['machineName'] = $libName;
                    array_push($questions, $q);
                    break;

            }
        }
        $this->questions = $questions;
    }

    protected function _extractCorrectAnswers(){
        //just feedback, so there are no correct answers
    }

    protected function _extractGivenAnswers(){
        $contentUserData = $this->_getContentUserData();

        if(!isset($contentUserData)){
            return;
        }
        if(!isset($this->questions) || count($this->questions) === 0){
            return;
        }
        //dd($contentUserData);
        //$data = $contentUserData?->data;
        //$data = json_decode($data);
        $dataArray = [];
        foreach($contentUserData as $cud){
            $temp = [];
            //$temp['user_id'] = $cud->user_id;
            $temp['user_name'] = $cud->name;
            $temp['bundle_name'] = $cud->title;
            $temp['email'] = $cud->email;
            $temp['data'] = json_decode($cud->data);
            array_push($dataArray, $temp);
        }

        if(!isset($dataArray[0]['data'])){
            return;
        }

        foreach($dataArray as $n => $data){
            $temp = [];
            foreach($data['data']->questions as $m => $answer){
                $givenAnswer = [];
                if($this->questions[$m]['machineName'] === 'H5P.OpenEndedQuestion'){
                    $givenAnswer['machineName'] = $this->questions[$m]['machineName'];
                    $givenAnswer['answers'] = $answer;
                } else if($this->questions[$m]['machineName'] === 'H5P.SimpleMultiChoice'){
                    $givenAnswer['machineName'] = $this->questions[$m]['machineName'];
                    $givenAnswer['answers'] = [];
                    $answerIndexes = explode('[,]', $answer);

                    foreach($answerIndexes as $index){
                        array_push($givenAnswer['answers'], $index !== "" ? $this->questions[$m]['possibleAnswers'][$index] : "");
                    }
                }
                //$givenAnswer['user_id'] = $dataArray[$n]['user_id'];
                $givenAnswer['user_name'] = $dataArray[$n]['user_name'];
                $givenAnswer['email'] = $dataArray[$n]['email'];
                $givenAnswer['bundle_name'] = $dataArray[$n]['bundle_name'];
                array_push($temp, $givenAnswer);
            }
            array_push($this->givenAnswers, $temp);
        }
        //dd($this->givenAnswers);
        // foreach($data->questions as $n => $answer){
        //     $givenAnswer = [];
        //     if($this->questions[$n]['machineName'] === 'H5P.OpenEndedQuestion'){
        //         $givenAnswer['machineName'] = $this->questions[$n]['machineName'];
        //         $givenAnswer['answers'] = $answer;
        //     } else if($this->questions[$n]['machineName'] === 'H5P.SimpleMultiChoice'){
        //         $givenAnswer['machineName'] = $this->questions[$n]['machineName'];
        //         $givenAnswer['answers'] = [];
        //         $answerIndexes = explode('[,]', $answer);

        //         foreach($answerIndexes as $index){
        //             array_push($givenAnswer['answers'], $index !== "" ? $this->questions[$n]['possibleAnswers'][$index] : "");
        //         }
        //     }
        //     array_push($this->givenAnswers, $givenAnswer);
        // }
    }

    protected function _getMaxScore()
    {
        //Questionnaire only for feedback, no Score available
    }

    protected function _getContentUserData() {
        //$uid = Auth::user()?->id;
        $contentUserData = null;
        if($this->bundleId){
            // $contentUserData = H5pContentsUserData::where([
            //     'content_id' => $this->contentId,
            //     'container_id' => $this->bundleId])->get();
            $contentUserData = DB::table('h5p_contents_user_data')
            ->join('users', 'h5p_contents_user_data.user_id', '=', 'users.id')
            ->join('h5p_content_container', 'h5p_contents_user_data.container_id', '=', 'h5p_content_container.id')
            ->select('h5p_contents_user_data.*', 'users.name', 'users.email', 'h5p_content_container.title')
            ->where('content_id', '=', $this->contentId)
            ->where('container_id', '=', $this->bundleId)
            ->get();
        } else {
            // $contentUserData = H5pContentsUserData::where([
            //     'user_id' => $uid,
            //     'content_id' => $this->contentId
            // ])->first();
        }
        return $contentUserData !== null ? json_decode($contentUserData) : null;
    }
}