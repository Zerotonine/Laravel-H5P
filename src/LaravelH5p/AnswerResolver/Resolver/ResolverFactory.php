<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Resolver;

class ResolverFactory {
    public static function getResolver(string $machineName, int $contentId, int $bundleId = null, object $params = null, string $title = null) : Resolver {
        switch ($machineName){
            case 'H5P.TrueFalse':
                return new TrueFalseResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.Blanks':
                return new BlanksResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.SingleChoiceSet':
                return new SingleChoiceSetResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.CoursePresentation':
                return new CoursePresentationResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.MultiChoice':
                return new MultiChoiceResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.DragText':
                return new DragTextResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.MarkTheWords':
                return new MarkTheWordsResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.AdvancedBlanks':
                return new AdvancedBlanksResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.InteractiveVideo':
                return new InteractiveVideoResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.Summary':
                return new SummaryResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.DragQuestion':
                return new DragQuestionResolver($contentId, $bundleId, $params, $title);
                break;
            case 'H5P.Questionnaire':
                return new QuestionnaireResolver($contentId, $bundleId, $params, $title);
                break;
        }
    }
}