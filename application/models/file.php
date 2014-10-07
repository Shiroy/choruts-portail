<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of file
 *
 * @author antoine
 */
class file extends CI_Model
{
    function __construct() {
        parent::__construct();
    }
    
    public function listDirectory($directory)
    {
        $result = array();
        $result['file'] = $this->db->query("SELECT id, name, FORMAT(taille_fichier / (1024*1024), 2) AS taille FROM choruts_file_files WHERE directory =".$this->db->escape($directory)." ORDER BY name")->result();
        $result['directories'] = $this->db->query("SELECT id, name FROM choruts_file_directories WHERE parent=".$this->db->escape($directory)." ORDER BY name")->result();
        //var_dump($result);
        return $result;
    }
    
    public function rootDirId()
    {
        $root = $this->db->query("SELECT id FROM choruts_file_directories WHERE parent IS NULL")->row();        
        return $root->id;
    }
    
    public function dirParent($directoryId)
    {
        $root = $this->db->query("SELECT parent FROM choruts_file_directories WHERE id=".$this->db->escape($directoryId))->row();
        return $root->parent;
    }
    
    public function insertFile($filename, $size, $dirId, $internalName)
    {
        $this->db->query("INSERT INTO choruts_file_files(name, taille_fichier, path, directory) VALUES(".$this->db->escape($filename).", ".$this->db->escape($size).", ".$this->db->escape($internalName).", ".$this->db->escape($dirId).")");
    }
    
    public function fileDownloadInfo($id)
    {
        return $this->db->query("SELECT name, path FROM choruts_file_files WHERE id=".$this->db->escape($id))->row();
    }
}

?>
