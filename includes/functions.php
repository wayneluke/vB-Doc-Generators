<?php

// Filesystem functions. 

function cleanOutput($dir) 
{
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0777, true)) {
            die ('Unable to create directory - ' .$dir);
        }
    } else {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                     RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
    }
    return true;
}

function writeFile (string $outDir, string $fileName, string $fileTxt) {
    if (!is_dir($outDir)) {
        // dir doesn't exist, make it
        createDirectory($outDir);
      }
      echo 'Creating ' . $fileName . ' file. ' . PHP_EOL;
      file_put_contents($outDir.'/'.$fileName,$fileTxt);  
}

// SLUG functions

function slugify($string='')
{
    $string=strip_tags($string);
    $string=preg_replace('/[^A-Za-z0-9-]+/', ' ', $string);
    $string=trim($string);
    $string=preg_replace('/[^A-Za-z0-9-]+/','-', $string); 
    $string=strtolower($string);
    return $string;
    
}

function unslugify($string='') {
    return ucwords(str_replace('-',' ',$string));
}
