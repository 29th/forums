<?php if (!defined('APPLICATION')) exit();
//original Plugin by @peregrine,Redistributed by VrijVlinder with  Peregrine's permission.
 
$PluginInfo['AddMenuitem'] = array(
   'Name' => 'Add Menu Item',
   'Description' => 'Adds up to 5 Items to menu - New and Improved.  3 links for non-mobile devices and  2 links for all devices. ',
   'Version' => '2.3',
   'Author' => "VrijVlinder",
   'MobileFriendly' => TRUE,
   'License'=> "GNU GPL2",
   'SettingsUrl' => '/dashboard/plugin/addmenuitem'
);


class AddMenuitemPlugin extends Gdn_Plugin {
 
   public function Base_Render_Before($Sender) {
      // Add "Items" to menu
    //  $Session = Gdn::Session();
    //  if ($Sender->Menu && $Session->IsValid()) {  
      if ($Sender->Menu) {  
      $itemname1 = (C('Plugins.AddMenuitem.Name1'));
      $itemlink1 = (C('Plugins.AddMenuitem.Link1'));
      $itemname2 = (C('Plugins.AddMenuitem.Name2'));
      $itemlink2 = (C('Plugins.AddMenuitem.Link2'));
      $itemname3 = (C('Plugins.AddMenuitem.Name3'));
      $itemlink3 = (C('Plugins.AddMenuitem.Link3')); 
      $itemname4 = (C('Plugins.AddMenuitem.Name4'));
      $itemlink4 = (C('Plugins.AddMenuitem.Link4')); 
      $itemname5 = (C('Plugins.AddMenuitem.Name5'));
      $itemlink5 = (C('Plugins.AddMenuitem.Link5')); 

     $IsMobile = (IsMobile());

 if (!$IsMobile) {  
   if (($itemname1)  && ($itemlink1)) {
         $Sender->Menu->AddLink($itemname1, T($itemname1), $itemlink1,FALSE,array('class' => 'sub-menu'),array('class' => 'sub-anchor'));
         }
      if ($itemname2  && $itemlink2) {
         $Sender->Menu->AddLink($itemname2, T($itemname2), $itemlink2,FALSE,array('class' => 'sub-menu'),array('class' => 'sub-anchor'));
         }
      if (($itemname3)  && ($itemlink3)) {
         $Sender->Menu->AddLink($itemname3, T($itemname3), $itemlink3,FALSE,array('class' => 'sub-menu'),array('class' => 'sub-anchor'));
         }
     }
     if (($itemname4)  && ($itemlink4)) {
         $Sender->Menu->AddLink($itemname4, T($itemname4), $itemlink4,FALSE,array('class' => 'sub-menu'),array('class' => 'sub-anchor'));
         }
      if (($itemname5)  && ($itemlink5)) {
         $Sender->Menu->AddLink($itemname5, T($itemname5), $itemlink5,FALSE,array('class' => 'sub-menu'),array('class' => 'sub-anchor'));
         }
      
      }
  
   }
  
   public function PluginController_AddMenuitem_Create($Sender) {
        $Sender->Title('Add Menu Item');
        $Sender->AddSideMenu('plugin/addmenuitem');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.AddMenuitem.Name1',
            'Plugins.AddMenuitem.Link1',
            'Plugins.AddMenuitem.Name2',
            'Plugins.AddMenuitem.Link2',
            'Plugins.AddMenuitem.Name3',
            'Plugins.AddMenuitem.Link3',
            'Plugins.AddMenuitem.Name4',
            'Plugins.AddMenuitem.Link4',
            'Plugins.AddMenuitem.Name5',
            'Plugins.AddMenuitem.Link5',
       
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('ami-settings.php'));
    }


   
  public function Setup() {
        
    }
    
    }
