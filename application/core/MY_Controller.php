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
class MY_Controller extends CI_Controller
{
    protected $isLogged = false;
    protected $username = "";
    protected $userId = 0;
    protected $nom = "";
    protected $prenom = "";
    protected $allowAdminPanel = false;


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
        }
        
        $this->twig->set('logged_in', $this->isLogged);
        $this->twig->set('login', $this->username);
        $this->twig->set('prenom', $this->prenom);
        $this->twig->set('nom', $this->nom);
        $this->twig->set('admin_panel_acces', ($this->isLogged && $this->allowAdminPanel) ? 1 : 0);
        
        $this->twig->set('base_url', $this->config->item('base_url'));
    }
    
    public function forceAuthentification()
    {
        redirect ($this->config->item('base_url')."/auth/login");
        exit();
    }
}

?>
