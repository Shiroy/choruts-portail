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
        
        if(!$this->allowAdminPanel) 
        {
            $this->_notAllowed();            
        }
        
        $this->twig->set("is_admin_panel", 1);
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
        
        if($title !== false && $content !== false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->addNews($title, $content, $this->userId);
        }
        
        //redirect($this->config->item('base_url')."/adminpanel");
    }
    
    public function editNews($newsId)
    {
        if(!is_numeric($newsId))
            return;
        
        $this->twig->set("edit", $newsId);
        $news = $this->admin_panel->getNewsForEdit($newsId);
        $this->twig->set("news", $news);
        $this->twig->render('newsForm.html.twig');
    }
    
    public function updatenews()
    {
        if($this->input->post("news_id") === false) //Formulaire cheaté
            return;
        
        $newsId = $this->input->post('news_id');
        if(!is_numeric($newsId))
            return;
        
        $title = $this->input->post('news_title');
        $content = $this->input->post('news_content');
        
        if($title !== false && $content !== false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->updateNews($title, $content, $newsId);
        }
        
        redirect($this->config->item('base_url')."/adminpanel");
    }
    
    public function carroussel()
    {
        $title = $this->input->post('carroussel_titre');
        $content = $this->input->post('carroussel_contenue');
        
        if($title !== false && $content !== false)
            $this->admin_panel->update_carroussel($title, $content);
        
        redirect ($this->config->item('base_url')."/adminpanel");
    }
            
    function _notAllowed()
    {
        $this->twig->render("adminpanel-access-error.html.twig");
        exit();
    }
}

?>
