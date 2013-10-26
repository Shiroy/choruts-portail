<?php

class choruts extends MY_Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->library("bbcode");
        
        $carroussel = $this->Index->getCarroussel();
        $carroussel["contenue"] = $this->bbcode->parse($carroussel["contenue"]);
        $news = $this->Index->getNews();
        
        $this->twig->set("carroussel", $carroussel);
        $this->twig->set("news", $news);
        $this->twig->render("index.html.twig");
    }
}

?>