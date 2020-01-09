<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['OnlineNow'] = array(
   'Name' => 'OnlineNow',
   'Description' => "Lists the users and their avatar who are currently online browsing the forum. The name and time appear when you hover the image and you can post to the person's activity page in a popup by clicking the name above the avatar that appears when you hover the image. This is a community supported plugin.Works on 2.1",
   'Version' => '2.9',
   'Author' => "Gary Mardell then modified to have user avatar by peregrine and implemented by vrijvlinder. This is a community supported plugin.",
   'RegisterPermissions' => array('Plugins.OnlineNow.ViewHidden', 'Plugins.OnlineNow.Manage'),
   'SettingsUrl' => '/settings/onlinenow'
);

/**
 * TODO:
 * Admin option to allow users it hide the module
 * User Meta table to store if they are hidden or not
 */

class OnlineNowPlugin extends Gdn_Plugin {

   public function SettingsController_OnlineNow_Create($Sender) {
      $Sender->Permission('Garden.Settings.Manage');
      $Sender->AddSideMenu('/settings/onlinenow');
      $Sender->Form = new Gdn_Form();
      $Validation = new Gdn_Validation();
      $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
      $ConfigurationModel->SetField(array('Plugins.OnlineNow.Location.Show', 'Plugins.OnlineNow.Frequency', 'Plugins.OnlineNow.Hide'));
      $Sender->Form->SetModel($ConfigurationModel);

      if ($Sender->Form->AuthenticatedPostBack() === FALSE) {    
         $Sender->Form->SetData($ConfigurationModel->Data);    
      } else {
         $Data = $Sender->Form->FormValues();
         $ConfigurationModel->Validation->ApplyRule('Plugins.OnlineNow.Frequency', array('Required', 'Integer'));
         $ConfigurationModel->Validation->ApplyRule('Plugins.OnlineNow.Location.Show', 'Required');
         if ($Sender->Form->Save() !== FALSE)
            $Sender->StatusMessage = T("Your settings have been saved.");
      }

      // creates the page for the plugin options such as display options
      $Sender->Render($this->GetView('olu-dashsettings.php'));
}
   public function PluginController_ImOnline_Create($Sender) {

      $Session = Gdn::Session();
      $UserMetaData = $this->GetUserMeta($Session->UserID, '%'); 

      // render new block and replace whole thing opposed to just the data
      include_once(PATH_PLUGINS.DS.'OnlineNow'.DS.'class.onlinenowmodule.php');
      $OnlineNowModule = new OnlineNowModule($Sender);
      $OnlineNowModule->GetData(ArrayValue('Plugin.OnlineNow.Invisible', $UserMetaData));
      echo $OnlineNowModule->ToString();

   }

   public function Base_Render_Before($Sender) {
      $Sender->AddCssFile('onlinenow.css', 'plugins/OnlineNow');
      $ConfigItem = C('Plugins.OnlineNow.Location.Show', 'every');
      $Controller = $Sender->ControllerName;
      $Application = $Sender->ApplicationFolder;
      $Session = Gdn::Session();     

        // Check if its visible to users
        if (C('Plugins.OnlineNow.Hide', TRUE) && !$Session->IsValid()) {
            return;
        }

        $ShowOnController = array();        
        switch($ConfigItem) {
            case 'every':
                $ShowOnController = array(
                    'discussioncontroller',
                    'categoriescontroller',
                    'discussionscontroller',
                    'profilecontroller',
                    'activitycontroller'
                );
                break;
            case 'discussion':
            default:
                $ShowOnController = array(
                    'discussioncontroller',
                    'discussionscontroller',
                    'categoriescontroller'
                );              
        }

      if (!InArrayI($Controller, $ShowOnController)) return; 

       $UserMetaData = $this->GetUserMeta($Session->UserID, '%');     
       include_once(PATH_PLUGINS.DS.'OnlineNow'.DS.'class.onlinenowmodule.php');
       $OnlineNowModule = new OnlineNowModule($Sender);
       $OnlineNowModule->GetData(ArrayValue('Plugin.OnlineNow.Invisible', $UserMetaData));
       $Sender->AddModule($OnlineNowModule);

       $Sender->AddJsFile('/plugins/OnlineNow/onlinenow.js');
       $Frequency = C('Plugins.OnlineNow.Frequency', 4);
       if (!is_numeric($Frequency))
          $Frequency = 4;

       $Sender->AddDefinition('OnlineNowFrequency', $Frequency);

   }

   public function Base_GetAppSettingsMenuItems_Handler($Sender) {
      $Menu = $Sender->EventArguments['SideMenu'];
      $Menu->AddLink('Add-ons', 'OnlineNow', '/settings/onlinenow', 'Garden.Settings.Manage');
   }

   // User Settings
   public function ProfileController_AfterAddSideMenu_Handler($Sender) {
      $SideMenu = $Sender->EventArguments['SideMenu'];
      $Session = Gdn::Session();
       // this tests to see is user is logged in or antixst
    if (!($Session->IsValid())) return;
      $ViewingUserID = $Session->UserID;

      if ($Sender->User->UserID == $ViewingUserID) {
         $SideMenu->AddLink('Options', T('Online Settings'), '/profile/OnlineNow', FALSE, array('class' => 'Popup'));
      }
   }

    public function ProfileController_OnlineNow_Create($Sender) {

      $Session = Gdn::Session();
       // this tests to see is user is logged in
    if (!($Session->IsValid())) return;
    $Sender->EditMode(TRUE);
    $UserID = $Session->UserID;
    $Sender->GetUserInfo("", "", $UserID);

      // Get the data
      $UserMetaData = $this->GetUserMeta($UserID, '%');
      $ConfigArray = array(
            'Plugin.OnlineNow.Invisible' => NULL
         );

      if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
         // Convert to using arrays if more options are added.
         $ConfigArray = array_merge($ConfigArray, $UserMetaData);
         $Sender->Form->SetData($ConfigArray);
      }
      else {
         $Values = $Sender->Form->FormValues();
         $FrmValues = array_intersect_key($Values, $ConfigArray);

         foreach($FrmValues as $MetaKey => $MetaValue) {
            $this->SetUserMeta($UserID, $this->TrimMetaKey($MetaKey), $MetaValue); 
         }

         $Sender->StatusMessage = T("Your changes have been saved.");
      }

      $Sender->Render($this->GetView('olu-profilesettings.php'));
       }



       public function Setup() { 
          $Structure = Gdn::Structure();
          $Structure->Table('OnlineNow')
                ->Column('UserID', 'int(11)', FALSE, 'primary')
            ->Column('Timestamp', 'datetime')
             ->Column('Photo','varchar(255)', NULL)
                ->Column('Invisible', 'int(1)', 0)
             ->Set(FALSE, FALSE); 
       }
    }
