<?php if (!defined('APPLICATION')) exit();
/**
* Renders a list of users who are taking part in a particular discussion.
*/
class OnlineNowModule extends Gdn_Module {

	protected $_OnlineUsers;

	public function __construct($Sender = '') {
		parent::__construct($Sender);
	}

	public function GetData($Invisible = FALSE) {
		$SQL = Gdn::SQL();
		// $this->_OnlineUsers = $SQL
		// insert or update entry into table
		$Session = Gdn::Session();
		//check for valid session
        if (!($Session->IsValid())) return;
        $Photo = $Session->User->Photo;
		$Invisible = ($Invisible ? 1 : 0);

		if ($Session->UserID)
			$SQL->Replace('OnlineNow', array(
				'UserID' => $Session->UserID,
				'Timestamp' => Gdn_Format::ToDateTime(),
				'Photo'=> $Session->User->Photo,
				'Invisible' => $Invisible),
				array('UserID' => $Session->UserID)
			);     

		$Frequency = C('Plugins.OnlineNow.Frequency', 4);
		$History = time() - $Frequency;

		$SQL
			->Select('u.UserID, u.Name, u.Photo,u.Email, w.Timestamp, w.Invisible')
			->From('OnlineNow w')
			->Join('User u', 'w.UserID = u.UserID')
			->Where('w.Timestamp >=', date('Y-m-d H:i:s', $History))
			->OrderBy('u.Name');

		if (!$Session->CheckPermission('Plugins.OnlineNow.ViewHidden'))
			$SQL->Where('w.Invisible', 0);

		$this->_OnlineUsers = $SQL->Get();
	}
    
/*function GetPhoto($User, $Options = array()) {
   $Session = Gdn::Session();
   if (!($Session->IsValid())) return;
       $Photo = $Session->User->Photo;
       if (!$Photo && function_exists('UserPhotoDefaultUrl'))
          $Photo = UserPhotoDefaultUrl($User);
 
       if ($Photo) {
          if (!preg_match('`^https?://`i', $Photo)) {
             $PhotoUrl = Gdn_Upload::Url(ChangeBasename($Photo, 'n%s'));
          } else {
             $PhotoUrl = $Photo;
          }
          $Href = Url(UserUrl($User));
          return '<a title="'.htmlspecialchars($User->Name).'" href="'.$Href.'"'.$LinkClass.'>'
             .Img($Session->User->PhotoUrl, array('alt' => htmlspecialchars($User->Name),'class' => 'ProfilePhotoSmall','title' => htmlspecialchars($User->Name))).'</a>';
       
       } else {
         return '';
       }
    }
 */

       	public function AssetTarget() {
		//return 'Foot';
		return 'Panel';
	}

	public function ToString() {
            $String = '';
            $Session = Gdn::Session();
            if (!($Session->IsValid())) return;
            ob_start();
            ?>
                <div id="OnlineNow">
                    <h4><?php echo T("Online Now"); ?> (<?php echo $this->_OnlineUsers->NumRows(); ?>)</h4>
                    <div class="list-group">
            <?php
         if ($this->_OnlineUsers->NumRows() > 0) { 
                foreach($this->_OnlineUsers->Result() as $User) {

                    if (($Session->CheckPermission('Plugins.OnlineNow.ViewHidden')) || ($User->Invisible < 1)) {
                       $Href = Url(UserUrl($User));
                       echo "<a href=\"".$Href."\" class=\"list-group-item\">";
                       echo htmlspecialchars($User->Name) . ' (' . Gdn_Format::Date($User->Timestamp) . ')';
                       //echo '<span class="Time">'.'<a class="'.'Popup Name'.'" title="'.'Post to Activity'.'" href="'.$Href.'"'.'>'.htmlspecialchars($User->Name).'</a>'.'<a class="'.'Popup Time'.'" href="'.'messages/add/'.htmlspecialchars($User->Name).'" title="'.'Send PM'.'"'.'>'.Gdn_Format::Date($User->Timestamp).'</a>'.'</span>';
                        
                       echo "</a>";
                       }

                     }
                    }
            echo "</div>";
            echo "</div>";
            $String = ob_get_contents();
            @ob_end_clean();
            return $String;
        }
      }