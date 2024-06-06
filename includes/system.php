<?php

// Sets up the system with some variables used for creating output. 
// Each variable is defined in /config/settings.ini and retrieved by
// method calls.

class System {

    private $outputDirectory;
    private $outputType;
    private $language;

    public function __construct ($file, $location) {


        ini_set('log_errors',1);
        ini_set("error_log", $location . "/logs/php_error.log");
        date_default_timezone_set ('America/Los_Angeles');
    
        if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open the settings file at ' . $file . '.');
        $this->outputDirectory = $location . '/' . $settings['system']['outputDirectory'];
        $this->outputType = $settings['system']['outputType'];
    }

    public function output()
    {
        return $this->outputDirectory;
    }

    public function format()
    {
        return $this->outputType;
    }

    public function language()
    {
        return $this->language;
    }
}