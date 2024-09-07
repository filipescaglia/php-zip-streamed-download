<?php

abstract class ZipUtil
{
    public static function zipDir(string $sourcePath, string $outputPath): void
    {
        $pathInfo = pathinfo($sourcePath);
        $parentPath = $pathInfo['dirname'];
        $dirName = $pathInfo['basename'];

        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $zip, strlen("$parentPath/"));
        $zip->close();
    }

    /**
     * @param string[] $sourcePaths
     */
    public static function zipFiles($sourcePaths, string $outputPath): void
    {
        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        foreach ($sourcePaths as $sourcePath) {
            if (!file_exists($sourcePath) || is_dir($sourcePath)) {
                continue;
            }

            $zip->addFile($sourcePath, basename($sourcePath));
        }

        $zip->close();
    }

    private static function folderToZip(string $folder, ZipArchive &$zipFile, int $exclusiveLength)
    {
        $handle = opendir($folder);
    
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
        
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
        
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    $zipFile->addEmptyDir($localPath);
            
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
    
        closedir($handle);
    }
}