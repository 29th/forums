<?php if (!defined('APPLICATION')) exit();

class SteamProfileModel extends Gdn_Model {

   /**
    * Checks the local Steam Profile cache for existing user profile
    * information.  If found, serve it up.  If not found, attempt to fetch
    * it from the Steam Community website.
    *
    * @param string $SteamID A sixty-four bit integer representing the target Steam ID
    * @return mixed SimpleXMLElement on success, FALSE on failure
    */
   public function GetByID($SteamID) {
      // Verify that the ID is only digits and that we have SimpleXML capabilities
      if (preg_match('/\d+/', $SteamID) && function_exists('simplexml_load_file')) {
         /**
          * Check to see if there are any cached profile records matching the ID and are
          * more than five minutes old
          */
         $CachedProfile = $this->SQL
            ->Select()
            ->From('SteamProfileCache')
            ->Where('SteamID64', $SteamID)
            ->Where('DateRetrieved >', Gdn_Format::ToDateTime(strtotime('-5 minutes')))
            ->Get()
            ->Firstrow();

         // Any cached entries?
         if ($CachedProfile) {
            // ...if so, load up the profile XML into a SimpleXMLElement...
            $CommunityProfile = simplexml_load_string($CachedProfile->ProfileXML, 'SimpleXMLElement', LIBXML_NOCDATA);
            // set the DateRetrieved of the cached record and go
            $CommunityProfile->DateRetrieved = $CachedProfile->DateRetrieved;
            return $CommunityProfile;
         } else {
            // ...if not, attempt to grab the profile's XML
            $CommunityProfile = simplexml_load_file('http://steamcommunity.com/profiles/'.$SteamID.'?xml=1', 'SimpleXMLElement', LIBXML_NOCDATA);

            // Were we able to successfully fetch the profile?
            if ($CommunityProfile && !isset($CommunityProfile->error)) {
               // ...if so, insert or update the profile XML into the cache table
               $this->SQL->Replace(
                  'SteamProfileCache',
                  array('SteamID64' => $SteamID, 'ProfileXML' => $CommunityProfile->asXML(), 'DateRetrieved' => Gdn_Format::ToDateTime()),
                  array('SteamID64' => $SteamID),
                  TRUE
               );

               // Set the DateRetrieved record to now and go
               $CommunityProfile->DateRetrieved = Gdn_Format::ToDateTime();
               return $CommunityProfile;
            }
         }
      }

      // If we hit this point, something bad has happened.
      return FALSE;
   }
}
