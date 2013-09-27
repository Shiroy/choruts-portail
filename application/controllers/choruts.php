<?php

class choruts extends CI_Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $this->twig->render("index.html.twig");
    }
}

?>