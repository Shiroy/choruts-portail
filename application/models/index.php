<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexModel
 *
 * @author antoine
 */
class Index extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function getCarroussel()
    {
        $carroussel = array();
        
        $titre = $this->db->query("SELECT content FROM site_string WHERE label='carroussel_titre'")->result();
        $carroussel['titre'] = $titre[0]->content;
        $contenue = $this->db->query("SELECT content FROM site_string WHERE label='carroussel_contenue'")->result();
        $carroussel['contenue'] = $contenue[0]->content;
        
        return $carroussel;
    }
    
    public function getNews()
    {
        return $this->db->query("SELECT titre, contenue, date, nom, prenom FROM site_news INNER JOIN auth_user ON auteur = auth_user.id")->result();
    }
}

?>
