<?php
// This script writes out markdown files for each stylvar group. 


// Only allow this to be run from the CLI.
if (PHP_SAPI != 'cli') { die ('Not Allowed'); } 

// Get some important files.
// Note: I may be able to do this with an Autoload function. Who knows?
require_once('./includes/system.php');
require_once('./includes/database.php');
require_once('./includes/querydef.php');
require_once('./includes/template.php');
require_once('./includes/functions.php');

// Setup System
$sys = new System(__DIR__ . "/config/settings.ini", __DIR__);
$db = new Database(__DIR__ . "/config/settings.ini");
if (!empty($db)) {
    echo "Database Connection Successful\n\r";
} else {
    die ('unable to connect');
}

//--------------------------------------------

$separator=DIRECTORY_SEPARATOR;
$outDir = $sys->output() . $separator . 'stylevars';
$type = $sys->format(); // Type of template to output.

// Setup Variables for page generation.

$templateTokens=['~title~','~description','~date~','~group~','~version~','~content~','~weight~'];
$contentTokens=['~title~','~image~','~description~','~help~','~additionalinfo~','~varname~','~type~','~defaultvalue~'];
$imageTokens=['~imageurl~','~caption~'];


$queries = new QueryDefs();

$Queries = $queries->getQueries('stylevars');

$clean = true;
$version = $queries->getVersion($db);
$now=date('Y-m-d h:ia');

$groups = $db->run_query($Queries['groups']);


$itemReplace=[];
$currentItem='';

// Need a clean output directory.
cleanOutput($outDir);


$pageCounter=10;
foreach ($groups as $group) {
    $description = $db->fetch_query($Queries['groupdesc'],[strtolower($group['stylevargroup'])]);
    //print_r($description); die;
    echo $group['stylevargroup'] . "\n\r";
    $stylevars = $db->run_query($Queries['stylevars'],[$group['stylevargroup']]);
    $content='';
    foreach ($stylevars as $stylevar) {
        
        if ($type='html') {
            $value_list = "<ul>\n";
        } else {
            $value_list="\n";
        }
        if (!isset($stylevar['title']) or $stylevar['title'] == null or $stylevar['title'] === '') {
            $stylevar['title'] = $stylevar['stylevarid'];
        }        
        $default = $db->fetch_query($Queries['default_value'],[$stylevar['stylevarid']]);
        $values = unserialize($default['value']);
        foreach ($values as $key => $value) {
            $inherit=0;
            if (strpos($key,'stylevar_') === 0) {
                $key = str_replace('stylevar_','',$key);
            }

            if (!empty($value) || $inherit) {
                if ($type == 'html') {
                    $value_list .= "<li>" . $key . ": " . $value . "</li>\n"; 
                } else {
                    if (strpos($key, 'inherit_')===0) {
                        $value_list .= "  ";
                    }
                    $value_list .= "- " . $key . ": " . $value; 
                }
                $value_list .= "\n";
            }
        }
        if ($type='html') {
            $value_list .= "</ul>";
        } else {
            $value_list .= "\n";
        }
        $value_list = trim($value_list);
        echo "\t". $stylevar['title'] ."\n\r";
        $itemReplace=[
            $stylevar['title'],          // title
            '',                          // image
            $stylevar['description'],    // description
            '',                          // help
            '',                          // additional info
            $stylevar['stylevarid'],     // variable name
            $stylevar['datatype'],       // type
            $value_list,                 // default values    
        ];
        $currentItem = new Template('stylevar_item', $type);
        $content.=$currentItem->parse($contentTokens,$itemReplace) . "\n";
    }
    
    $pageCounter +=10;
    if ($group['stylevargroup']==='Global') { 
        $weight = 1; 
    } elseif ($group['stylevargroup']==='GlobalPalette') {
        $weight = 2; 
    } else { 
        $weight = $pageCounter;
    }
    $templateReplace=[$group['stylevargroup'], $description['text'], $now, $group['stylevargroup'], $version, $content, $weight];

    $stylevarPage = new Template('stylevar_page',$type);
    $page=$stylevarPage->parse($templateTokens,$templateReplace);
    file_put_contents($outDir . $separator . $group['stylevargroup'] . '.'. $type, $page);

}

