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
    private $isLogged;
    private $username;
    
    public function __construct()
    {
        parent::__construct();
        $this->twig->set('login_url', $this->config->item('login_url'));
        $this->twig->set('login_appli', urlencode(current_url()));
        
        if($this->input->cookie('st') === false)
        {
            redirect ($this->config->item('login_url')."requestTicket.php?appli=".urlencode(current_url()), 'location', 301);
            exit();
        }
        
        $st = $this->input->cookie('st');
        $this->input->set_cookie('st', 0, false);
        if($st == "0") //Utilisateur anonyme
        {
            $this->isLogged = false;
            $this->username = "";
        }
        else
        {            
            $validation = file_get_contents($this->config->item('login_url')."validate.php?ticket=$st");
            //var_dump($validation);
            if(strlen($validation) == 0)
            {
                $this->isLogged = false;
                $this->username = "";
            }
            else
            {
                $auth_response = json_decode($validation);
                if($auth_response->sucess != 0)
                {
                    $this->isLogged = true;
                    $this->username = $auth_response->username;
                }
                else
                {
                    $this->isLogged = false;
                    $this->username = "";
                }
            }
        }
        
        $this->twig->set('logged_in', $this->isLogged);
        $this->twig->set('login', $this->username);
    }
}

?>
