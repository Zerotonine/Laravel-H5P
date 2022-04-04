<?php

/*
 *
 * @Copyright      Zerotonine
 * @Created        2022-03-24
 * @Filename       DatastructureHelpers.php
 * @Description    Helper Functions for working with different datastructures
 *
 */

namespace EscolaSoft\LaravelH5p\Helpers;

class DatastructureHelpers
{
    /**
     * Returns an array with all values that have the specified key
     *
     * @param array $array Array where you wanna search
     * @param string $key the key you're looking for
     * @param array &$aggregator holds the search results
     * @return array
     */
    public static function findArrayValues($array, $key, &$aggregator = array()){
        if(count($array) === 0)
            return $aggregator;
        if(array_key_exists($key, $array)){
            array_push($aggregator, $array[$key]);
            return $aggregator;
        }

        $new_array = array_filter($array, 'is_array');
        foreach($new_array as $arr){
            self::findArrayValues($arr, $key, $aggregator);
        }
        return $aggregator;
    }
}
