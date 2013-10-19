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
        
        redirect($this->config->item('base_url')."/adminpanel");
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
    
    public function delnews($newsId)
    {
        if(!is_numeric($newsId))
            return;
        
        $this->admin_panel->deleteNews($newsId);
        redirect ($this->config->item('base_url')."/adminpanel");
    }
    
    public function carroussel()
    {
        $title = $this->input->post('carroussel_titre');
        $content = $this->input->post('carroussel_contenue');
        
        if($title !== false && $content !== false)
            $this->admin_panel->update_carroussel($title, $content);
        
        redirect ($this->config->item('base_url')."/adminpanel");
    }
    
    public function users($page = 1)
    {
        if($page < 1)
            $page = 1;
        
        define("USER_PER_PAGE", 20);
        
        $users = $this->user->getUsers(1, USER_PER_PAGE);
        
        $nbPage = ($users['count'] / USER_PER_PAGE) + 1;        
        
        $this->twig->set("nb_page", $nbPage);
        $this->twig->set("current_page", $page);
        $this->twig->set("users", $users['users']);
        
        $this->twig->render("adminpanel-users.html.twig");        
    }
    
    public function adduser()
    {
        if($this->input->post() === false)
        {
            $this->twig->render("admin-adduser.html.twig");
        }
        else
        {
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules('username', "nom d'utilisateur", 'required|is_unique[auth_user.user]|alpha');
            $this->form_validation->set_rules('userpass', "mot de passe", 'required');
            $this->form_validation->set_rules('nom', 'nom', 'required');
            $this->form_validation->set_rules('prenom', 'prenom', 'required');
            $this->form_validation->set_rules('mail', "e-mail", 'required|is_unique[auth_user.mail]|valid_email');
            
            if($this->form_validation->run() == false)
            {
                $this->twig->set('form_error', validation_errors("<p class='text-error'>", "</p>"));
                $this->twig->set('form_value', $this->input->post());
                $this->twig->render("admin-adduser.html.twig");
                return;
            }
            
            $user = $this->input->post("username");
            $pass = $this->input->post("userpass");
            $mail = $this->input->post("mail");
            $nom = $this->input->post("nom");
            $prenom = $this->input->post("prenom");
            
            $this->user->addUser($user, $pass, $mail, $nom, $prenom);
        }
    }
            
    function _notAllowed()
    {
        $this->twig->render("adminpanel-access-error.html.twig");
        exit();
    }
}

?>
