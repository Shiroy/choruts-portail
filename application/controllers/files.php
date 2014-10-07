<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of files
 *
 * @author antoine
 */
class files extends MY_Controller
{
    private $canView = false;
    private $canEdit = false;
    
    public function __construct() {
        parent::__construct();
        
        if(!$this->isLogged)
            $this->forceAuthentification ();
        
        $this->canView = $this->user->isAllowedTo($this->userId, USER_RIGHT_VIEW_FILE);
        if(!$this->canView)
            show_404 ();
        
        $this->canEdit = $this->user->isAllowedTo($this->userId, USER_RIGHT_EDIT_FILES);
        
        $this->load->model("file");
        $this->twig->set("canEdit", $this->canEdit);
    }
    
    public function index()
    {       
        $dirId = $this->file->rootDirId();
        $fileList = $this->file->listDirectory($dirId);
        $this->twig->set("current_dir_id", $dirId);
        
        //var_dump($fileList['file']);
        
        $this->twig->set("files", $fileList['file']);
        $this->twig->set("directories", $fileList['directories']);
        
        $this->twig->render("file-directory.html.twig");
    }
    
    public function directory($directoryId = null)
    {
        if(is_null($directoryId) or !is_numeric($directoryId))
            return $this->index();
        
        $this->twig->set("current_dir_id", $directoryId);
        
        $fileList = $this->file->listDirectory($directoryId);
        
        //var_dump($fileList['file']);
        
        $this->twig->set("files", $fileList['file']);
        $this->twig->set("directories", $fileList['directories']);
        $this->twig->set("parent", $this->file->dirParent($directoryId));
        
        $this->twig->render("file-directory.html.twig");
    }
    
    public function upload($dirId=NULL)
    { 
        if(!$this->canEdit)
            show_404 ();
        
        $config['upload_path'] = "assets/uploaded_files/";
        $config['encrypt_name'] = TRUE;
        $config['allowed_types'] = "*";
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        
        if ( ! $this->upload->do_upload("file_to_upload"))
            $this->redirect_meta ($this->upload->display_errors(), base_url ("files/"));
        
        $upload_data = $this->upload->data();
        
        $this->file->insertFile($upload_data['orig_name'], $upload_data['file_size'] * 1024, $dirId, $upload_data['full_path']);
        
        $this->redirect_meta ("Le fichier a été correctement téléchargé", base_url("files/directory/$dirId"));
    }
    
    public function content($id)
    {        
        $file = $this->file->fileDownloadInfo($id);
        header("Content-type: ".mime_content_type($file->path));
        echo file_get_contents($file->path);
        exit();
    }
    
    public function download($id)
    {
        $this->load->helper("download");
        $file = $this->file->fileDownloadInfo($id);
        force_download($file->name, file_get_contents($file->path));
    }
}

?>
