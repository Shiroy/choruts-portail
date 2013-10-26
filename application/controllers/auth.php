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
                $this->twig->render("login-form.html.twig");
            }
            else
            {
                $this->session->set_userdata("user_id", $user);
                $this->redirect_meta("Vous êtes à présent connecté.", $this->config->item('base_url'));                
            }
        }
    }
    
    public function logout()
    {
        $this->session->unset_userdata("user_id");
        if($this->cas->is_authenticated())
            $this->cas->logout();
        $this->redirect_meta("Vous êtes à présent déconnecté.", $this->config->item('base_url'));
    }    
    
    public function cas()
    {
        $this->load->library('cas');        
        $this->cas->force_auth();        
        $user = $this->cas->user();
        $casAcount = $user->userlogin;
        $casUser = $this->user->casAccountExist($casAcount);
        
        if($casUser === false) //Le compte n'existe pas
        {
            if($this->input->post() === false) //Avons-nous des données POST ?
            {
                $this->twig->render("user-cas.html.twig");
            }
            else
            {
                $this->load->library("form_validation");
                $this->form_validation->set_rules('nom', 'nom', 'required|alpha');
                $this->form_validation->set_rules('prenom', 'prenom', 'required|alpha');
                
                if($this->form_validation->run() == false)
                {
                    $this->twig->set("form_error", validation_errors("<p class='text-error'>", "</p>"));
                    $this->twig->set("post", $this->input->post());
                    $this->twig->render("user-cas.html.twig");
                }
                else
                {
                    $nom = $this->input->post("nom");
                    $prenom = $this->input->post("prenom");
                    $this->user->addCasAccount($casAcount, $nom, $prenom);
                    $this->redirect_meta("Votre compte a été correctement créé", $this->config->item('base_url'));
                }
            }
        }
        else
        {
            $this->session->set_userdata("user_id", $casUser->id);
            $this->redirect_meta("Vous êtes à présent connecté.", $this->config->item('base_url'));
        }
    }
}

?>
