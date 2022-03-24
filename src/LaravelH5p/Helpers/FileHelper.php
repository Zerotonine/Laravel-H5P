<?php

/*
 *
 * @Copyright      Zerotonine
 * @Created        2022-03-24
 * @Filename       FileHelper.php
 * @Description    Helper Functions for deleting files/directories
 *
 */

namespace EscolaSoft\LaravelH5p\Helpers;

class FileHelper
{
    /**
     * Deletes a directory and all files in it
     *
     * @param $path Path to directory
     * @return bool true if everything's deleted, false if not
     */
    public static function deleteDir($path){
        $files = array_diff(scandir($path), array('.', '..'));
        foreach($files as $file){
            (is_dir("$path/$file")) ? self::deleteDir("$path/$file") : unlink("$path/$file");
        }
        return rmdir($path);
    }
}
