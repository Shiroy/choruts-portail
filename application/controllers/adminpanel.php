<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adminpanel
 *
 * @author antoine
 */
class adminpanel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_panel");        
        
        if(!$this->isLogged)
            $this->forceAuthentification();
        
        if(!$this->allowAdminPanel) 
        {
            $this->_notAllowed();            
        }
        
        $this->twig->set("is_admin_panel", 1);
    }
    
    public function index()
    {
        $carroussel = $this->Index->getCarroussel();
        $this->twig->set('carroussel', $carroussel);
        
        $this->twig->set("news", $this->admin_panel->getNews());
        
        $this->twig->render("adminpanel-index.html.twig");
    }
    
    public function addnews()
    {
        $this->twig->render('newsForm.html.twig');
    }
    
    public function publishNews()
    {
        $title = $this->input->post('news_title');
        $content = $this->input->post('news_content');
        
        if($title !== false && $content !== false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->addNews($title, $content, $this->userId);
        }
        
        $this->redirect_meta("La news a été correctement publiée.", $this->config->item('base_url')."/adminpanel");
    }
    
    public function editNews($newsId)
    {
        if(!is_numeric($newsId))
            return;
        
        $this->twig->set("edit", $newsId);
        $news = $this->admin_panel->getNewsForEdit($newsId);
        $this->twig->set("news", $news);
        $this->twig->render('newsForm.html.twig');
    }
    
    public function updatenews()
    {
        if($this->input->post("news_id") === false) //Formulaire cheaté
            return;
        
        $newsId = $this->input->post('news_id');
        if(!is_numeric($newsId))
            return;
        
        $title = $this->input->post('news_title');
        $content = $this->input->post('news_content');
        
        if($title !== false && $content !== false)
        {
            $title = htmlspecialchars($title);
            $content = htmlspecialchars($content);
            
            $this->admin_panel->updateNews($title, $content, $newsId);
        }
        
        $this->redirect_meta("La news a été correctement mise à jour.", $this->config->item('base_url')."/adminpanel");
    }
    
    public function delnews($newsId)
    {
        if(!is_numeric($newsId))
            return;
        
        $this->admin_panel->deleteNews($newsId);
        $this->redirect_meta ("La news a bien été supprimée", $this->config->item('base_url')."/adminpanel");
    }
    
    public function carroussel()
    {
        $title = $this->input->post('carroussel_titre');
        $content = $this->input->post('carroussel_contenue');
        
        if($title !== false && $content !== false)
            $this->admin_panel->update_carroussel($title, $content);
        
        $this->redirect_meta ("Le carroussel a été correctement mis à jour", $this->config->item('base_url')."/adminpanel");
    }
    
    public function users($page = 1)
    {
        if($page < 1)
            $page = 1;
        
        define("USER_PER_PAGE", 20);
        
        $users = $this->user->getUsers($page, USER_PER_PAGE);
        
        $nbPage = ($users['count'] / USER_PER_PAGE) + 1;        
        
        $this->twig->set("nb_page", $nbPage);
        $this->twig->set("current_page", $page);
        $this->twig->set("users", $users['users']);
        
        $this->twig->render("adminpanel-users.html.twig");        
    }
    
    public function adduser()
    {
        if($this->input->post() === false)
        {
            $this->twig->render("admin-adduser.html.twig");
        }
        else
        {
            $this->load->library("form_validation");
            
            $this->form_validation->set_rules('username', "nom d'utilisateur", 'required|is_unique[auth_user.user]|alpha');
            $this->form_validation->set_rules('userpass', "mot de passe", 'required');
            $this->form_validation->set_rules('nom', 'nom', 'required');
            $this->form_validation->set_rules('prenom', 'prenom', 'required');
            $this->form_validation->set_rules('mail', "e-mail", 'required|is_unique[auth_user.mail]|valid_email');
            
            if($this->form_validation->run() == false)
            {
                $this->twig->set('form_error', validation_errors("<p class='text-error'>", "</p>"));
                $this->twig->set('form_value', $this->input->post());
                $this->twig->render("admin-adduser.html.twig");
                return;
            }
            
            $user = $this->input->post("username");
            $pass = $this->input->post("userpass");
            $mail = $this->input->post("mail");
            $nom = $this->input->post("nom");
            $prenom = $this->input->post("prenom");
            
            $this->user->addUser($user, $pass, $mail, $nom, $prenom);
            
            $this->redirect_meta("L'utilisateur a été correctement créer", $this->config->item('base_url')."/adminpanel/users");
        }
    }
    
    function groups()
    {
        $groups = $this->user->getGroups();
        $this->twig->set("groups", $groups);
        $this->twig->render("adminpanel-groups.html.twig");
    }
    
    function groupe($groupId)
    {
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL | USER_RIGHT_VIEW_MEMBER_PART))
            show_404 ();
        
        if(!is_numeric($groupId))
            show_404();
        
        $groupeInfo = $this->user->getGroupeDetail($groupId);
        if($groupeInfo === false)
            show_404();
        
        $group = $groupeInfo['info'];
        
        if($group->rights & USER_RIGHT_ACCES_ADMIN_PANEL)
        {
            $this->twig->set("right_admin_panel", true);
        }
        if($group->rights & USER_RIGHT_EDIT_MEMBERS)
        {
            $this->twig->set("right_edit_members", true);
        }
        if($group->rights & USER_RIGHT_VIEW_MEMBER_PART)
        {
            $this->twig->set("right_member_part", true);
        }
        if($group->rights & USER_RIGHT_PUBLISH_NEWS)
        {
            $this->twig->set("right_publish_news", true);
        }
        if($group->rights & USER_RIGHT_VIEW_FILE)
        {
            $this->twig->set("right_view_files", true);            
        }
        if($group->rights & USER_RIGHT_EDIT_FILES)
        {
            $this->twig->set("right_edit_files", true);            
        }
        
        $this->twig->set('groupe', $group);
        $this->twig->set('groupeId', $group->id);
        $this->twig->set('membre', $groupeInfo['membre']);
        $this->twig->render("adminpanel-groupedetail.html.twig");
    }
    
    function updategroup()
    {
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL | USER_RIGHT_VIEW_MEMBER_PART))
            show_404 ();
        
        if($this->input->post("group_id") === false)
            show_404();
        
        $groupId = $this->input->post("group_id");
        
        $right = 0;
        if($this->input->post("right-admin") !== false)
            $right |= USER_RIGHT_ACCES_ADMIN_PANEL;
        if($this->input->post("right-edit-members") !== false)
            $right |= USER_RIGHT_EDIT_MEMBERS;
        if($this->input->post("right-member-part") !== false)
            $right |= USER_RIGHT_VIEW_MEMBER_PART;
        if($this->input->post("right-publish-news") !== false)
            $right |= USER_RIGHT_PUBLISH_NEWS;
        if($this->input->post("right-view-files") !== false)
            $right |= USER_RIGHT_VIEW_FILE;
        if($this->input->post("right-edit-files") !== false)
            $right |= USER_RIGHT_EDIT_FILES;
        
        $name = $this->input->post("name");
        if($name !== false)
        {
            if($groupId != 0)
            {
                $this->user->updateGroupe($groupId, $name, $right);
                $this->redirect_meta("Le groupe $name a bien été mis à jour.", $this->config->item('base_url')."/adminpanel/groupe/$groupId");
            }                
            else
            {
                $this->user->addGroup($name, $right);
                $this->redirect_meta("Le groupe $name a bien été créer.", $this->config->item('base_url')."/adminpanel/groups");
            }                
        }
        
        $this->redirect_meta("Une erreur de mise à jour s'est produite, les changements sont annulés.", $this->config->item('base_url')."/adminpanel/groupe/$groupId"); //J'aime bien faire flipper les gens :)
    }
    
    function groupeaddfromprofile($userId)
    {
        if(!is_numeric($userId))
            show_404 ();
        
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL | USER_RIGHT_VIEW_MEMBER_PART))
            show_404 ();
        
        $groupeId = $this->input->post("groupe");
        
        if(!is_numeric($groupeId))
            show_404 ();
        
        $this->user->addGroupeMember($userId, $groupeId);
        
        $this->redirect_meta("L'utilisateur a été correctement ajouté au groupe", $this->config->item('base_url')."/users/profile/$userId");
    }
    
    public function groupmemberdel($groupId, $userId)
    {
        if(!is_numeric($userId) || !is_numeric($groupId))
            show_404 ();
        
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL | USER_RIGHT_VIEW_MEMBER_PART))
            show_404 ();
        
        $this->user->delGroupMember($userId, $groupId);
        
        $this->redirect_meta("L'utilisateur a été correctement supprimé au groupe", $this->config->item('base_url')."/adminpanel/groupe/$groupId");
    }
    
    public function addgroup()
    {
        if(!$this->user->isAllowedTo($this->userId, USER_RIGHT_ACCES_ADMIN_PANEL | USER_RIGHT_VIEW_MEMBER_PART))
            show_404 ();
        
        $this->twig->set('groupeId', 0);
        $this->twig->render("adminpanel-groupedetail.html.twig");
    }
    
    public function apropos()
    {
        if($this->input->post() === false) //Première appel => formulaire
        {
            $texte = $this->Index->apropos();
            $this->twig->set("apropos", $texte->content);
            $this->twig->render("adminpanel-apropos.html.twig");
        }
        else
        {
            $newContent = $this->input->post("apropos-content");
            $this->Index->updateApropos($newContent);
            $this->redirect_meta("La page a été correctement mise à jour.", $this->config->item('base_url')."/adminpanel/apropos");
        }
    }
            
    function _notAllowed()
    {
        $this->twig->render("adminpanel-access-error.html.twig");
        exit();
    }
}

?>
