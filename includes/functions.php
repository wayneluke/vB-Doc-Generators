<?php

// String functions.


// Determines if a string is a serialized array, a json encoded string, or just a string.
// Returns false if just a string. returns the decoded array if serialized or JSON.

function decodeString ($str) 
{
    if (json_validate($str)) {
        return json_decode($str);
    }

    $data = unserialize($str);
    if ($str === 'b:0;' || $data !== false) {
        return $data;
    }

    return false;
}

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
        cleanOutput($outDir);
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
