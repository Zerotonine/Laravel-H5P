<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Helpers;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as Bundle;

class AnswerResolverHelper {


    /**
     * Function which gets all used libraries in a bundle
     * @param $bundleId the id of the bundle/container
     * @return $library_names an array of associatve arrays whiche have machineName and name/title inside. If nothing is found null will be returned.
     */
    public static function getBundleLibraries(int $bundleId){
        $bundle = Bundle::where(['id' => $bundleId])->first();
        $contents = $bundle?->contents;
        if($contents === null)
            return null;

        $library_names = [];
        foreach($contents as $content){
            $lib = $content?->library;
            array_push($library_names, [
                'machineName' => $lib?->name,
                'name' => $lib?->title
            ]);
        }
        return $library_names;
    }

}