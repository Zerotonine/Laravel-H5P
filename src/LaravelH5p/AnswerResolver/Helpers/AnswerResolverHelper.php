<?php

namespace EscolaSoft\LaravelH5p\AnswerResolver\Helpers;
use EscolaSoft\LaravelH5p\Eloquents\H5pContentContainer as Bundle;
use EscolaSoft\LaravelH5p\AnswerResolver\Enums\SupportedTypesEnum as SupportedTypes;

class AnswerResolverHelper {


    /**
     * Function which gets all used libraries in a bundle
     * @param int $bundleId the id of the bundle/container
     * @return array $library_names an array of objects which has machineName, name/title and the contentId of the content using the lib inside. If nothing is found null will be returned.
     */
    public static function getBundleLibraries(int $bundleId) : array{
        $bundle = Bundle::where(['id' => $bundleId])->first();
        $contents = $bundle?->contents;
        if($contents === null)
            return null;

        $library_names = [];
        foreach($contents as $content){
            $lib = $content?->library;
            array_push($library_names, (object)[
                'machineName' => $lib?->name,
                'name' => $lib?->title,
                'contentId' => $content?->id,
            ]);
        }
        return $library_names;
    }

    public static function getContentIds(int $bundleId) : array {
        $bundle = Bundle::where(['id' => $bundleId])->first();
        $contents = $bundle?->contents;
        $ids = [];
        if($contents == null)
            return null;

        foreach($contents as $content){
            array_push($ids, $content?->id);
        }

        return $ids;
    }

    /**
     * Functions checks which ContentTypes in a bundle are currenty supported,
     * returns als NOT supported Types
     * @param array $library_names array produced by func getBundleLibraries
     * @return array not_supported array with the human readable names of the libraries in it which are currently not supported
     */
    public static function getNotSupportedTypes(array $library_names) : array{
        $machineNames = SupportedTypes::machineNames();
        $notSupported = [];

        if(empty($library_names))
            return array();

        foreach($library_names as $libName){
            if(is_array($libName)){
                $libName = (object) $libName;
            }

            if(!in_array($libName?->machineName, $machineNames)){
                array_push($notSupported, $libName->name);
            }
        }
        return array_unique($notSupported);
    }

    /**
     * Returns all currently supported types in a given Library_names array
     * @param array $library_names array returned by func getBundleLibraries
     * @return array supported array with the library objects in it
     */
    public static function getSupportedLibraries(array $library_names) : array{
        $machineNames = SupportedTypes::machineNames();
        $supported = [];

        if(empty($library_names)){
            return array();
        }

        foreach($library_names as $libName){
            if(is_array($libName)){
                $libName = (object) $libName;
            }
            if(in_array($libName->machineName, $machineNames)){
                array_push($supported, $libName);
            }
        }
        return $supported;
    }
}