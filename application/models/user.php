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
class user extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getUserInfo($userId)
    {
        $user = $this->db->query('SELECT nom, prenom FROM auth_user WHERE id='.$userId)->result();
        return $user[0];
    }
    
    public function checkPassword($user, $pass)
    {
        //Hash computing Upper sha( Upper( Upper(pseudo) : pass ) ) )
        
        $hash = strtoupper(sha1(strtoupper($user).":".$pass));
        
        $users = $this->db->query("SELECT id FROM auth_user WHERE user='".$this->db->escape_str($user)."' AND password='".$hash."'");
        if($users->num_rows() == 0)
            return false; //Return false on failure
        
        return $users->row()->id; //Return user id if auth success
    }
}

?>
