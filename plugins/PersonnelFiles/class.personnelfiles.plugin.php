<?php if (!defined('APPLICATION')) exit();

// Defining the plugin attributes...
$PluginInfo['PersonnelFiles'] = array(
   'Author' => 'Wilson29thID',
   'AuthorEmail' => 'wilson@29th.org',
   'AuthorUrl' => 'http://29th.org',
   'Description' => 'Allows the integration of a user\'s Personnel File into their Vanilla profile',
   'HasLocale' => FALSE,
   'MobileFriendly' => TRUE,
   'Name' => 'Personnel Files',
   'PluginUrl' => '',
   'RegisterPermissions' => array(),
   'RequiredApplications' => array('Vanilla' => '2.1.3'),
   'RequiredPlugins' => FALSE,
   'RequiredTheme' => FALSE, 
   'Version' => '0.1',
);

class PersonnelFiles extends Gdn_Plugin {

    public function ProfileController_AddProfileTabs_Handler(&$Sender) {
        $this->PersonnelFileModel = new PersonnelFileModel();
        
        $Sender->SetData('PersonnelFile', $this->PersonnelFileModel->GetByID($Sender->User->UserID));
        
        // Did we get back a valid personnel file?
        if ($Sender->Data('PersonnelFile', FALSE)) {
        
            // Attach the style sheet, load up the view, attach it all to the panel
            $Sender->AddCssFile('style.css', 'plugins/steamprofile');
            $Sender->AddAsset('Panel', $Sender->FetchView($this->GetView('panel.php')), 'Steam');
        }
    }
}
