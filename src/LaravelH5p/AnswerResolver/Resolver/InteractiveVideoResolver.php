<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

use EscolaSoft\LaravelH5p\AnswerResolver\Enums\SupportedTypesEnum;

class InteractiveVideoResolver extends Resolver {
    private $supportedLibraries = [];
    private $resolvers = [];
    private $subTitles = [];

    public function __construct(int $contentId, int $bundleId = null, $params = null, string $title = null) {
        parent::__construct($contentId, $bundleId, $params, $title);
        $this->_setSupportedLibraries();
        $this->_setResolvers();
        $this->_extractCorrectAnswers();
        $this->_extractQuestions();
        $this->_extractGivenAnswers();
        $this->_setSubTitles();
        $this->_getMaxScore();
        //dd($this->supportedLibraries);
        //dd($this->resolvers);
        //dd($this->subTitles);
        //dd($this->getQuestionsAndAnswers());
    }

    private function _setSupportedLibraries(){
        $supported = SupportedTypesEnum::machineNames();
        $interactions = $this->contentObj?->interactiveVideo?->assets?->interactions;
        if(!isset($interactions)){
            return;
        }

        foreach($interactions as $interaction){
            $lib = trim(preg_replace('/[\d]+.[\d]+/', '', $interaction->action->library));
            if(in_array($lib, $supported)){
                array_push($this->supportedLibraries, (object)['name' => $lib, 'params' => $interaction->action->params, 'title' => $interaction->action->metadata->title]);
            }
        }

        //At the end of an interactive video there is always a summary
        $summary = $this->contentObj?->interactiveVideo?->summary?->task;
        $lib = trim(preg_replace('/[\d]+.[\d]+/', '', $summary->library));
        array_push($this->supportedLibraries, (object)['name' => $lib, 'params' => $summary->params, 'title' => $summary->metadata->title]);
    }

    private function _setResolvers(){
        foreach($this->supportedLibraries as $sup){
            $resolver = ResolverFactory::getResolver($sup->name, -1, $this->bundleId, $sup->params, $sup->title);
            array_push($this->resolvers, $resolver);
        }
    }

    private function _setSubTitles(){
        foreach($this->resolvers as $resolver){
            array_push($this->subTitles, $resolver->title);
        }
    }

    protected function _extractQuestions(){
        foreach($this->resolvers as $resolver){
            //dd($resolver);
            $this->questions = array_merge($this->questions, $resolver->questions);
        }
        //dd($this->questions);
    }

    protected function _extractCorrectAnswers(){
        foreach($this->resolvers as $resolver){
            $this->correctAnswers = array_merge($this->correctAnswers, $resolver->correctAnswers);
        }
    }

    protected function _extractGivenAnswers(){
        //TODO: impl
        // foreach($this->resolvers as $resolver){
        //     $this->givenAnswers = array_merge($this->givenAnswers, $resolver->givenAnswers);
        // }
    }

    /**
     * Returns an object with name (of the content package), array questions, array correct_answers and array given_answers
     * @return object
     */
    public function getQuestionsAndAnswers() : object {
        $retVal = (object) [
            'content_id' => $this?->contentId,
            'bundle_id' => $this?->bundleId,
            'name' => $this?->title,
            //'questions' => self::getQuestions(),
            'questions' => $this?->questions,
            'correct_answers' => $this?->correctAnswers,
            'given_answers' => $this?->givenAnswers,
            'titles' => $this?->subTitles,
            'max_score' => $this?->maxScore,
        ];

        return $retVal;
    }

    protected function _getMaxScore()
    {
        foreach($this->correctAnswers as $correctAnswer){
            if(is_array($correctAnswer)){
                $this->maxScore += count($correctAnswer);
            } else {
                $this->maxScore++;
            }
        }
    }
}