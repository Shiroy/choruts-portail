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
    
    public function isAllowedTo($user, $right)
    {
        /*Description sommaire de la requete : 
         * Selectionne tous les utilisateurs qui appartiennent à un group ayant les droits $right et vérifie que $user et dans la liste
         */
        $isUserAllowed = $this->db->query("SELECT 1 FROM auth_user WHERE id = $user AND id IN (SELECT userId FROM user_groups_members WHERE groupId IN (SELECT id FROM `user_groups` WHERE rights & $right))");
        
        return ($isUserAllowed->num_rows() != 0); //S'il y a un resultat, alors l'utilisateur a les autorisations nécéssaires
    }
    
    public function getUsers($page, $userPerPage)
    {
        $result = array();
        $rows = $this->db->query("SELECT COUNT(*) AS count FROM auth_user")->result();
        $result['count'] = $rows[0]->count;
        
        $firtstIndex = ($page-1) * $userPerPage;
        
        $result['users'] = $this->db->query("SELECT id, user, nom, prenom, mail FROM auth_user LIMIT $firtstIndex, $userPerPage")->result();
        
        return $result;
    }
    
    public function profile($userId)
    {
        $result = $this->db->query("SELECT user, password, nom, prenom, mail, telephone, type_voix FROM auth_user WHERE id=?", array($userId)); //Le champ password permet de savoir si l'utilisateur utilise CAS ou non
        
        if($result->num_rows() == 0)
            return false;
        
        return $result->row();
    }
    
    public function addUser($user, $pass, $email, $nom, $prenom)
    {
        //Hashage du mot de passe
        
        $hash = strtoupper(sha1(strtoupper($user).":".$pass));
        
        $this->db->query("INSERT INTO auth_user(user, password, mail, nom, prenom) VALUES (?, ?, ?, ?, ?)", array($user, $hash, $email, $nom, $prenom));
    }
    
    public function updateUser($userId, $nom, $prenom, $mail, $telephone, $pupitre)
    {
        $this->db->query("UPDATE auth_user SET nom=?, prenom=?, mail=?, telephone=?, type_voix=? WHERE id=?", array($nom, $prenom, $mail, $telephone, $pupitre, $userId));
    }
    
    public function casAccountExist($casAccount)
    {
        $query = $this->db->query("SELECT id FROM auth_user WHERE login_etu=?", array($casAccount));
        
        if($query->num_rows() == 0)
            return false;
        
        return $query->row();
    }
    
    public function addCasAccount($casAccount, $nom, $prenom)
    {
        $mail = $casAccount."@utc.fr";
        $this->db->query("INSERT INTO auth_user(user, password, nom, prenom, mail, login_etu) VALUES (?, 'CAS', ?, ?, ?, ?)", array($casAccount, $nom, $prenom, $mail, $casAccount));
    }
    
    public function getGroups()
    {
        return $this->db->query("SELECT id, nom FROM user_groups ORDER BY nom")->result();
    }
    
    public function getGroupeDetail($groupId)
    {
        $result = $this->db->query("SELECT id, nom, rights FROM user_groups WHERE id=?", array($groupId));
        if($result->num_rows() == 0)
            return false;
        
        $groupe["info"] = $result->row();        
        $groupe["membre"] = $this->db->query("SELECT id, nom, prenom FROM auth_user INNER JOIN user_groups_members ON id=userId WHERE groupId=?", array($groupId))->result();
        
        return $groupe;
    }
    
    public function updateGroupe($groupId, $name, $right)
    {
        $this->db->query("UPDATE user_groups SET nom=?, rights=? WHERE id=?", array($name, $right, $groupId));
    }
    
    public function getGroupNotIn($userId)
    {
        return $this->db->query("SELECT id, nom FROM user_groups WHERE id NOT IN (SELECT groupId FROM user_groups_members WHERE userId = ?)", array($userId))->result();
    }
    
    public function addGroupeMember($userId, $groupeId)
    {
        $this->db->query("INSERT INTO user_groups_members(userId, groupId) VALUES (?, ?)", array($userId, $groupeId));
    }
    
    public function delGroupMember($userId, $groupId)
    {
        $this->db->query("DELETE FROM user_groups_members WHERE userId=? AND groupId=?", array($userId, $groupId));
    }
    
    public function addGroup($name, $right)
    {
        $this->db->query("INSERT INTO user_groups (nom, rights) VALUES(?, ?)", array($name, $right));
    }
}

?>
