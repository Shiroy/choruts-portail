<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author antoine
 */
class auth extends MY_Controller
{
    function __construct() {
        parent::__construct();
    }
    
    public function login()
    {
        if($this->isLogged)
            redirect ($this->config->item('base_url'));
        
        if($this->input->post() === false)
            $this->twig->render("login-form.html.twig");
        else
        {
            $user = $this->user->checkPassword($this->input->post('login'), $this->input->post('pass'));
            if($user === false)
            {
                $this->twig->set("error", 1);
                $this->twig->set("username", $this->input->post('login'));
            }
            else
            {
                $this->session->set_userdata("user_id", $user);
                redirect($this->config->item('base_url'));
            }                
            
            $this->twig->render("login-form.html.twig"); //If auth sucess, this line should not be executed
        }
    }
    
    public function logout()
    {
        $this->session->unset_userdata("user_id");
        redirect($this->config->item('base_url'));
    }
}

?>
