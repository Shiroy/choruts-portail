<?php

class choruts extends CI_Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $carroussel = $this->Index->getCarroussel();
        
        $this->twig->set("carroussel", $carroussel);
        $this->twig->render("index.html.twig");
    }
}

?>