<?php

class admin_panel extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getNews()
    {
        return $this->db->query("SELECT site_news.id, titre, DATE_FORMAT(date, \"%e %M %Y\") AS date, nom, prenom FROM site_news INNER JOIN auth_user ON auteur = auth_user.id ORDER BY site_news.date DESC")->result();
    }
    
    public function addNews($title, $content, $userId)
    {        
        $this->db->query("INSERT INTO site_news(titre, contenue, date, auteur) VALUES (\"".$this->db->escape_str($title)."\", \"".$this->db->escape_str($content)."\", NOW(), $userId)");
    }
    
    public function updateNews($title, $content, $newsId)
    {
        $this->db->query("UPDATE site_news SET titre=?, contenue=? WHERE id=?", array($title, $content, $newsId));
    }
    
    public function deleteNews($newsId)
    {
        $this->db->query("DELETE FROM site_news WHERE id = ?", array($newsId));
    }

    public function update_carroussel($title, $content)
    {
        $this->db->query("UPDATE site_string SET content=? WHERE label='carroussel_contenue'", array($content));
        $this->db->query("UPDATE site_string SET content=? WHERE label='carroussel_titre'", array($title));
    }
    
    public function getNewsForEdit($id)
    {
        return $this->db->query("SELECT titre, contenue FROM site_news WHERE id = ?", array($id))->row();
    }
}

?>
