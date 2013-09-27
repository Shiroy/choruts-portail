<?php
require_once 'Twig-1.13.2/lib/Twig/Autoloader.php';

class Twig
{
    private $loader;
    private $twig;
    private $variableArray;
    
    public function __construct()
    {
        Twig_Autoloader::register();
        
        $this->loader = new Twig_Loader_Filesystem('application/views');
        $this->twig = new Twig_Environment($this->loader, array(/*'cache' => 'application/cache'*/));
    }
    
    public function set($varname, $value)
    {
        $this->variableArray[$varname] = $value;
    }
    
    public function render($template)
    {
        if(sizeof($this->variableArray) == 0)
            echo $this->twig->render ($template);
        else
            echo $this->twig->render($template, $this->variableArray);
    }
}