<?php

class admin_panel extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getNews()
    {
        return $this->db->query("SELECT site_news.id, titre, DATE_FORMAT(date, \"%e %M %Y\") AS date, nom, prenom FROM site_news INNER JOIN auth_user ON auteur = auth_user.id")->result();
    }
}

?>
