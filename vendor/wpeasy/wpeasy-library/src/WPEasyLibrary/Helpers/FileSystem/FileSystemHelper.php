<?php


namespace WPEasyLibrary\Helpers\FileSystem;


class FileSystemHelper
{
    static function deleteCacheContents($dir)
    {
        $lwr = strtolower($dir);
        if(false === strpos($dir, 'cache'))
            throw new \ErrorException('Only cache folders may be deleted with this method');

        foreach (glob($dir) as $file) {
            if (is_dir($file)) {
                FileSystemHelper::deleteCacheContents("$file/*");
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
}