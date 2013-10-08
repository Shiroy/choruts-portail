<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adminpanel
 *
 * @author antoine
 */
class adminpanel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_panel");
        
        if(!$this->isLogged)
            $this->forceAuthentification ();
    }
    
    public function index()
    {
        $carroussel = $this->Index->getCarroussel();
        $this->twig->set('carroussel', $carroussel);
        
        $this->twig->set("news", $this->admin_panel->getNews());
        
        $this->twig->render("adminpanel-index.html.twig");
    }
    
    public function addnews()
    {
        $this->twig->render('newsForm.html.twig');
    }
    
    public function publishNews()
    {
        echo sizeof($_POST);
        $title = $this->input->post('news_title');
        $content = $this->input->post('news_content');
        echo "$title<br/>$content";
        
        if($title != false && $content != false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->addNew($title, $content, $this->userId);
        }
        
        //redirect($this->config->item('base_url')."/adminpanel");
    }
}

?>
