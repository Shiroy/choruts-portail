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
        if(is_null($dirId) or !is_numeric($dirId))
            show_404 ();
        
        if(!$this->canEdit)
            show_404 ();
        
        $config['upload_path'] = "assets/uploaded_files/";
        $config['encrypt_name'] = TRUE;
        $config['allowed_types'] = "*";
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        
        if ( ! $this->upload->do_upload("file_to_upload"))
        {
            $this->redirect_meta ($this->upload->display_errors(), base_url ("files/"));
            exit();
        }
        
        $upload_data = $this->upload->data();
        
        $this->file->insertFile($upload_data['orig_name'], $upload_data['file_size'] * 1024, $dirId, $upload_data['full_path']);
        
        $this->redirect_meta ("Le fichier a été correctement téléchargé", base_url("files/directory/$dirId"));
    }
    
    public function content($id=NULL)
    {
        if(is_null($id) or !is_numeric($id))
            show_404 ();
        
        $file = $this->file->fileDownloadInfo($id);
        header("Content-type: ".mime_content_type($file->path));
        echo file_get_contents($file->path);
        exit();
    }
    
    public function download($id=NULL)
    {
        if(is_null($id) or !is_numeric($id))
            show_404 ();
        
        $this->load->helper("download");
        $file = $this->file->fileDownloadInfo($id);
        force_download($file->name, file_get_contents($file->path));
    }
    
    public function newDir()
    {
        if(!$this->canEdit)
            show_404 ();
        
        $this->load->library("form_validation");
        $this->form_validation->set_rules('new_dir_name', 'nom du nouveau dossier', 'required');
        $this->form_validation->set_rules('parent_dir', 'parent_dir', 'required|numeric');
        if($this->form_validation->run() == false)
        {
            $this->redirect_meta(validation_errors(), base_url());
        }
        
        $newDirName = $this->input->post("new_dir_name");
        $parentDir = $this->input->post("parent_dir");
        
        $this->file->createDir($newDirName, $parentDir);
        
        $this->redirect_meta("Le dossier a été correctement créé", base_url("files/directory/$parentDir"));
    }

    public function rm($id)
    {
        if(is_null($id) or !is_numeric($id))
            show_404 ();
        
        if(!$this->canEdit)
            show_404 ();
        
        $this->confirm("Voulez vous vraiment supprimer ce fichier ? Cette action est irréversible !!");
        
        $parentId = $this->file->removeFile($id);        
        
        $this->garbageCollector();
        
        $this->redirect_meta("Le fichier a été correctement supprimé", base_url("files/directory/$parentId"));
    }
    
    public function rmdir($id)
    {
        if(is_null($id) or !is_numeric($id))
            show_404 ();
        
        if(!$this->canEdit)
            show_404 ();
        
        $this->confirm("Voulez vous vraiment supprimer ce dossier et tout son contenue ? Cette action est irréversible !!");
        
        $parentId = $this->file->removeDirectory($id);        
        
        $this->garbageCollector();
        
        $this->redirect_meta("Le dossier a été correctement supprimé", base_url("files/directory/$parentId"));
    }
    
    private function garbageCollector() //Purge les fichiers non référencés du disuqe serveur
    {
        $file_to_keep = $this->file->getAllPath();
        
        for($i = 0 ; $i < count($file_to_keep) ; $i++)
            $file_to_keep[$i] = basename($file_to_keep[$i]->path);
        
        $all_file = array_diff(scandir("assets/uploaded_files/", SCANDIR_SORT_NONE), array(".", ".."));
        
        $file_to_remove = array_diff($all_file, $file_to_keep);
        
        foreach ($file_to_remove as $f)
        {
            unlink("assets/uploaded_files/".$f);
        }
    }
}

?>
