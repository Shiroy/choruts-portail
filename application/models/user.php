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
    
    public function getUserInfo($username)
    {
        $user = $this->db->query('SELECT id, nom, prenom FROM auth_user WHERE user=\''.$this->db->escape_str($username)."'")->result();
        return $user[0];
    }
}

?>
