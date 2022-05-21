<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Enums;
use EscolaSoft\LaravelH5p\AnswerResolver\Traits\NamesFromEnumTrait;

enum SupportedTypesEnum {
    use NamesFromEnumTrait;

    case H5P_TrueFalse; //check
    case H5P_Blanks; //check --- Verhaltenseinstellung prüfen, auf Rechtschreibfehler muss geachtet werden
    case H5P_SingleChoiceSet; //check
    case H5P_CoursePresentation; //check --- Verhaltenseinstellungen?!
    case H5P_MultiChoice; //check
    case H5P_DragText; //check
    case H5P_MarkTheWords; //check
    case H5P_AdvancedBlanks; //check --- Verhaltenseinstellungen beachten
    case H5P_InteractiveVideo; //check --- Verhaltenseinstellungen?!
    case H5P_Summary; //check --- Verhaltenseinstellungen?
    case H5P_DragQuestion; //Darstellung?! maxScore has to be implemented
    case H5P_Questionnaire; //just for feedback, given feedback only visible for Bundle creator
}