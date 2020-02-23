<?php 
class PersonnelFilesPlugin extends Gdn_Plugin {
    public function profileController_addProfileTabs_handler($sender) {
        $this->PersonnelFileModel = new PersonnelFileModel();
        
        $sender->SetData('PersonnelFile', $this->PersonnelFileModel->GetByID($sender->User->UserID));
        
        // Did we get back a valid personnel file?
        if ($sender->Data('PersonnelFile', FALSE)) {
            // Attach the style sheet, load up the view, attach it all to the panel
            $sender->AddAsset('Panel', $sender->FetchView($this->GetView('panel.php')), 'Personnel File');
        }
    }
}
