<?php

class choruts extends CI_Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $carroussel = $this->Index->getCarroussel();
        $news = $this->Index->getNews();
        
        $this->twig->set("carroussel", $carroussel);
        $this->twig->set("news", $news);
        $this->twig->render("index.html.twig");
    }
}

?>