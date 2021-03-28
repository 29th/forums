<?php
class EmailInSessionTokenPlugin extends Gdn_Plugin {
  public function gdn_cookieIdentity_setIdentity_handler($sender, $args) {
    $UserID = $args['payload']['sub'];
    $User = Gdn::userModel()->getID($UserID);
    $args['payload']['name'] = $User->Email;
  }
}
