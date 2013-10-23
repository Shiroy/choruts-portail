<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Controller
 *
 * @author antoine
 */

function bbcode_parse($str)
{
    return $this->bbcode->parse($str);
}

class MY_Controller extends CI_Controller
{
    protected $isLogged = false;
    protected $username = "";
    protected $userId = 0;
    protected $nom = "";
    protected $prenom = "";
    protected $allowAdminPanel = false;
    protected $allowPrivatePart = false;


    public function __construct()
    {
        parent::__construct();
        
        $userId = $this->session->userdata("user_id");
        if($userId !== false) //The user is authenticated
        {
            $this->userId = $userId;
            $this->isLogged = true;
            $userInfo = $this->user->getUserInfo($userId);
            $this->nom = $userInfo->nom;
            $this->prenom = $userInfo->prenom;
            $this->allowAdminPanel = $this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL);
            $this->allowPrivatePart = $this->user->isAllowedTo($this->userId, USER_RIGHT_VIEW_MEMBER_PART);
        }
        
        $this->twig->set('logged_in', $this->isLogged);
        $this->twig->set('user_id', $this->userId);
        $this->twig->set('login', $this->username);
        $this->twig->set('prenom', $this->prenom);
        $this->twig->set('nom', $this->nom);
        $this->twig->set('admin_panel_acces', ($this->isLogged && $this->allowAdminPanel) ? 1 : 0);
        $this->twig->set("view_private_part", $this->allowPrivatePart);
        
        $this->twig->set('base_url', $this->config->item('base_url'));
    }
    
    public function forceAuthentification()
    {
        redirect ($this->config->item('base_url')."/auth/login");
        exit();
    }
    
    public function redirect_meta($msg, $link) //Efectue une redirection meta
    {
        $this->twig->set("redirect_msg", $msg);
        $this->twig->set("redirect_url", $link);
        $this->twig->render("redirect_meta.html.twig");
    }
}

?>
