<?php
namespace Utils;

class ResourceLoader
{
    private static $resourceFolder = __DIR__ . '/../../resources/';

    public static function load($filename)
    {
        if (!file_exists($resourceFolder . $filename)) {
            throw new \UnexpectedValueException("File '{$filename}' not found in resources directory.");
        }

        return file_get_contents($resourceFolder . $filename);
    }
}
