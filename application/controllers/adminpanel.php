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
            $this->forceAuthentification();
        
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL)) 
        {
            $this->_notAllowed();            
        }                
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
        $title = $this->input->post('news_title');
        $content = $this->input->post('news_content');
        
        if($title != false && $content != false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->addNews($title, $content, $this->userId);
        }
        
        //redirect($this->config->item('base_url')."/adminpanel");
    }
    
    function _notAllowed()
    {
        $this->twig->render("adminpanel-access-error.html.twig");
        exit();
    }
}

?>
