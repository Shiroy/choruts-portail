<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of users
 *
 * @author antoine
 */
class users extends MY_Controller{
   
    public function __construct() {
        parent::__construct();
    }
    
    public function profile($userId)
    {
        if(!$this->isLogged || (!$this->user->isAllowedTo($this->userId, USER_RIGHT_VIEW_MEMBER_PART) && $userId != $this->userId))
            $this->forceAuthentification ();
        
        if(!is_numeric($userId))
            show_404 ();
        
        $profile = $this->user->profile($userId);
        if($profile === false)
            show_404 ();
        
        $this->twig->set("profile", $profile);
        
        if($userId == $this->userId || $this->user->isAllowedTo($this->userId, USER_RIGHT_EDIT_MEMBERS))
        {
            $this->twig->set("editable", true);
            $this->twig->set("userId", $userId);
        }
        else
            $this->twig->set("editable", false);
        
        if($this->user->isAllowedTo($this->userId, USER_RIGHT_EDIT_MEMBERS | USER_RIGHT_ACCES_ADMIN_PANEL))
        {
            $groups = $this->user->getGroupNotIn($userId);
            $this->twig->set("groupe", $groups);
            $this->twig->set("groupe_editable", true);
        }           
        
        $this->twig->render("user-profile.html.twig");
    }
    
    public function updateprofile()
    {
        if($this->input->post() === false)
            return;
        
        if(!$this->isLogged || (!$this->user->isAllowedTo($this->userId, USER_RIGHT_EDIT_MEMBERS) && $userId != $this->userId))
            $this->forceAuthentification ();        
        
        $this->load->library("form_validation");
        
        if($this->input->post("userId") === false)
            return false;
        
        $userId = $this->input->post("userId");
        if(!is_numeric($userId))
            return;
        
        $this->form_validation->set_rules('nom', 'nom', 'required|alpha');
        $this->form_validation->set_rules('prenom', 'prenom', 'required|alpha');
        $this->form_validation->set_rules('mail', "e-mail", 'required|valid_email');
        $this->form_validation->set_rules('telephone', 'numéros de téléphone', 'numeric|exact_length[10]');
        
        if($this->form_validation->run() == false)
        {
            redirect($this->config->item('base_url')."/users/profile/$userId");
            exit();            
        }
        
        $nom = $this->input->post('nom');
        $prenom = $this->input->post('prenom');
        $mail = $this->input->post('mail');
        $telephone = $this->input->post('telephone');
        $pupitre = $this->input->post('pupitre');
        
        $this->user->updateUser($userId, $nom, $prenom, $mail, $telephone, $pupitre);
        
        $this->redirect_meta("Votre profil a bien été mis à jour", $this->config->item('base_url')."/users/profile/$userId");
    }
    
    public function changepassword($userId)
    {
        if(!$this->isLogged)
            $this->forceAuthentification ();
        
        if($this->user->isCASUser($userId))
            show_404 ();
        
        if(!($this->user->isAllowedTo($userId, USER_RIGHT_EDIT_MEMBERS) || $this->userId==$userId))
            show_404 ();
        
        if($this->input->post("newPass") == false)
        {
            $this->redirect_meta("Vous devez saisir un nouveau mot de passe.", $this->config->item('base_url')."/users/profile/$userId");
            return;
        }
        
        $this->user->updatePassword($userId, $this->input->post("newPass"));
        
        $this->redirect_meta("Le mot de passe a bien été changé.", $this->config->item('base_url')."/users/profile/$userId");
    }
}

?>
