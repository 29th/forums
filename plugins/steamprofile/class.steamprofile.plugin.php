<?php if (!defined('APPLICATION')) exit();

// Defining the plugin attributes...
$PluginInfo['steamprofile'] = array(
   'Author' => 'Ryan Perry',
   'AuthorEmail' => '',
   'AuthorUrl' => 'http://initvector.com',
   'Description' => 'Allows the integration of a user\'s Steam profile into their Vanilla profile',
   'HasLocale' => FALSE,
   'MobileFriendly' => TRUE,
   'Name' => 'Steam Profile',
   'PluginUrl' => 'https://github.com/initvector/VanillaAddons',
   'RegisterPermissions' => array(),
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'RequiredPlugins' => FALSE,
   'RequiredTheme' => FALSE, 
   'Version' => '0.9',
);

class SteamProfilePlugin extends Gdn_Plugin {

   /**
    * Defining the location of Steam's OpenID authentication provider
    */
   const SteamOpenIDAuth = 'https://steamcommunity.com/openid/login';

   /**
    * Steam returns the claimed_id of the OpenID authentication transaction
    * as a URL.  This constant determines what isn't an ID-specific value.
    */
   const SteamOpenIDClaimedID = 'http://steamcommunity.com/openid/id/';

   /**
    * Hooks into the Render function to ensure we can serve up any
    * Steam Profile notifications, even between page loads
    *
    * @param Gdn_Controller $Sender
    */
   public function Base_Render_Before(&$Sender) {
      $CurrentNotification = $this->_SteamProfileNotification();

      if ($CurrentNotification) {
         $Sender->InformMessage($CurrentNotification, 'Dismissable');
      }
   }

   /**
    * Verifies the authenticity of an OpenID authentication, returned from Steam
    * 
    * @param array $OpenIDParameters The GET parameters from the is_res submission
    */
   private function CheckAuthentication($OpenIDRequest) {
      // PHP replaces dots (.) with underscores on incoming variables.  We have to add them back.
      $OpenIDParameters = array();
      $OpenIDParameters['openid.assoc_handle'] = GetValue('openid_assoc_handle', $OpenIDRequest, '');
      $OpenIDParameters['openid.claimed_id'] = GetValue('openid_claimed_id', $OpenIDRequest, '');
      $OpenIDParameters['openid.identity'] = GetValue('openid_identity', $OpenIDRequest, '');
      $OpenIDParameters['openid.mode'] = 'check_authentication';
      $OpenIDParameters['openid.ns'] = GetValue('openid_ns', $OpenIDRequest, '');
      $OpenIDParameters['openid.op_endpoint'] = GetValue('openid_op_endpoint', $OpenIDRequest, '');
      $OpenIDParameters['openid.response_nonce'] = GetValue('openid_response_nonce', $OpenIDRequest, '');
      $OpenIDParameters['openid.return_to'] = GetValue('openid_return_to', $OpenIDRequest, '');
      $OpenIDParameters['openid.sig'] = GetValue('openid_sig', $OpenIDRequest, '');
      $OpenIDParameters['openid.signed'] = GetValue('openid_signed', $OpenIDRequest, '');

      // Loading up all of our previously collected values into an array and sending them off for verificiation
      $CheckAuthResult = file_get_contents(self::SteamOpenIDAuth.'?'.http_build_query($OpenIDParameters));

      // Was this a valid authentication, after all?
      if (strstr($CheckAuthResult, 'is_valid:true')) {
         // If so, save the Steam ID and carry on
         $this->SetUserMeta(
            Gdn::Session()->UserID,
            'SteamID64',
            str_replace(self::SteamOpenIDClaimedID, '', $OpenIDParameters['openid.claimed_id'])
         );
         return TRUE;
      } else {
         // Otherwise, indicate an invalid attempt
         return FALSE;
      }
   }

   /**
    * We're going to need to hook into the profile controller to make this
    * work. Steam will return the user to this page, OpenID parameters in tow.
    *
    * @param Gdn_Controller $Sender
    */
   public function ProfileController_SteamProfileOpenID_Create(&$Sender) {
      // Grabbing the $_GET array, processed by our framework
      $RequestGet = Gdn::Request()->Get();

      // Nabbing the mode of the OpenID request, if any
      $OpenIDMode = GetValue('openid_mode', $RequestGet);

      // Are we receiving an authentication result?
      if ($OpenIDMode == 'id_res') {
         // Is the quthentication valid?
         if ($this->CheckAuthentication($RequestGet)) {
            // If so, alert the user of the successful linking
            $this->_SteamProfileNotification(T('Successfully linked your Steam profile'), 'Dismissable');
         } else {
            // If not, we just throw out an "invalid attempt" error
            $this->_SteamProfileNotification(T('Invalid Steam authentication attempt'), 'Dismissable');
         }
      } elseif ($OpenIDMode == 'error') { // Are we receiving an arror?
         // Notify the user
         $this->_SteamProfileNotification(T('Unable to link your Steam profile').': '.GetValue('openid_error', $RequestGet));
      } else { // No idea what happened here.  Send 'em home.
         Redirect('/');
      }

      // ..and boom goes the dynamite...
      Redirect('/profile');
   }

   /**
    * Handling the event fired at the end of the "Edit My Account" view
    * Adding on the "Steam ID" field to that view
    *
    * @param Gdn_Controller $Sender
    */
   public function ProfileController_EditMyAccountAfter_Handler($Sender) {
      // Grabbing the profile user's SteamID64 data
      $UserMetaSteamID64 = $this->GetUserMeta($Sender->User->UserID, 'SteamID64');
      $SteamID64 = GetValue('Plugin.steamprofile.SteamID64', $UserMetaSteamID64, '');

      // Standard OpenID checkid_setup values
      $OpenIDParameters = array(
         'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
         'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
         'openid.mode' => 'checkid_setup',
         'openid.ns' => 'http://specs.openid.net/auth/2.0'
      );

      // Site-specific OpenID checkid_setup values
      $OpenIDParameters['openid.realm'] = Gdn::Request()->Scheme().'://'.Gdn::Request()->Host();
      $OpenIDParameters['openid.return_to'] = Url('profile/steamprofileopenid', TRUE);

      // Building our OpenID checkid_setup query
      $Sender->SetData('SteamAuthenticationUrl',
         self::SteamOpenIDAuth.'?'.
         http_build_query($OpenIDParameters)
      );

      // Assisnging the retrieved data to the form field and loading up the form field view
      $Sender->SetData('SteamID64', $SteamID64);
      $Sender->Render($this->GetView('steamcommunityid.php'));
   }

   /**
    * Handling the event fired at the end of the BuildProfile method of the Profile controller
    * If a valid Steam ID is found, load the profile and add it to the profile sidebar.
    * If no valid Steam ID is found, do nothing.
    *
    * @param Gdn_Controller $Sender
    */
   public function ProfileController_AddProfileTabs_Handler(&$Sender) {
      // Instantiating our SteamProfile model and attempting to retrieve the profile data
      $this->SteamProfileModel = new SteamProfileModel();

      // Rustling up the SteamID64 data associated with the user, if available
      $UserMetaSteamID64 = $this->GetUserMeta($Sender->User->UserID, 'SteamID64');
      $SteamID64 = GetValue('Plugin.steamprofile.SteamID64', $UserMetaSteamID64, '');

      // Attempting to retrieve the profile data associated with the SteamID64 field
      $Sender->SetData('SteamProfile', $this->SteamProfileModel->GetByID($SteamID64));

      // Did we get back a valid profile?
      if ($Sender->Data('SteamProfile', FALSE)) {
         // Is there a record(s) for this user's "Most Played Games"?
         if (isset($Sender->Data('SteamProfile')->mostPlayedGames->mostPlayedGame)) {
            // If there are several results, there will be an array of elements.  Is there an array of elements?
            if (is_array($Sender->Data('SteamProfile')->mostPlayedGames->mostPlayedGame)) {
               //  ...if so, grab the first one.
               $Sender->SetData('MostPlayedGame', $Sender->Data('SteamProfile')->mostPlayedGames->mostPlayedGame[0]);
            } else {
               // ...if not, grab the single element.
               $Sender->SetData('MostPlayedGame', $Sender->Data('SteamProfile')->mostPlayedGames->mostPlayedGame);
            }
         }

         // Attach the style sheet, load up the view, attach it all to the panel
         $Sender->AddCssFile('style.css', 'plugins/steamprofile');
         $Sender->AddAsset('Panel', $Sender->FetchView($this->GetView('panel.php')), 'Steam');
      }
	}

   /**
    * The _InformMessages array is reset on page load.  This function is
    * used to store/retrieve the message value in the user's session to
    * retrieve on page load
    *
    * @param string $InformMessage Optional. If provided, stash the message.  If not, return the message.
    * @return mixed Returns string message, when retrieving.  No return when stashing
    */
   private function _SteamProfileNotification($InformMessage = '') {
      // Fire up a reference to our session object
      $Session = Gdn::Session();

      // Do we have anything to save?
      if ($InformMessage == '') {
         // If not, just return what we do have (if anything)
         return $Session->Stash('SteamProfileNotification');
      } else {
         // If so, save our new message
         $Session->Stash('SteamProfileNotification', $InformMessage);
      }
   }

   /**
    * Performs the contained tasks when the plugin is enabled in the dashboard
    */
   public function Setup() {
      // Perform database necessary modifications
      $this->Structure();
   }

   /**
    * Commits the required updates to the structure of the database
    */
   public function Structure() {
      // Loading up our structure object to enable the ability to modify the database
      $Structure = Gdn::Structure();

      // Adding a table to cache all of the retrieved Steam profile information
      $Structure
         ->Table('SteamProfileCache')
         ->PrimaryKey('ProfileCacheID')
         ->Column('SteamID64', 'varchar(20)', FALSE)
         ->Column('ProfileXML', 'text', FALSE)
         ->Column('DateRetrieved', 'datetime', FALSE)
         ->Set(FALSE, FALSE);
   }
}
