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
        
        if(!$this->isLogged)
            $this->forceAuthentification ();
    }
    
    public function profile($userId)
    {
        if(!is_numeric($userId))
            show_404 ();
        
        $profile = $this->user->profile($userId);
        if($profile === false)
            show_404 ();
        
        $this->twig->set("profile", $profile);
        
        if($userId == $this->userId || $this->user->isAllowedTo($this->userId, USER_RIGHT_EDIT_MEMBERS))
            $this->twig->set("editable", true);
        else
            $this->twig->set("editable", false);
        
        $this->twig->render("user-profile.html.twig");
    }
}

?>
