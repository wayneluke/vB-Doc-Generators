<?php

class Template {

    private $dir= __DIR__ . '/templates/';
    private $template;

    public function __construct ($file, $type = 'md') {
        $temp = $this->dir . $file . '.' . $type;
        if (!file_exists($temp)) {
            exit("Template (" . $temp . ") doesn't exist.\n");
        } else {            
            $this->template=$temp;
        }
    }

    public function parse(array $tokens, array $values) {
        $content = file_get_contents($this->template);
        $content = str_replace($tokens, $values, $content);
        $content = preg_replace("/\n\n+/s","\n",$content);
        return $content;
    }

    public function render() {
        
    }
}