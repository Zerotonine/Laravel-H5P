<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Traits;

trait NamesFromEnumTrait {
    public static function names() : array{
        return array_column(static::cases(), 'name');
    }

    public static function machineNames() : array {
        return preg_replace('/(^H5P_)/', 'H5P.', static::names());
    }
}