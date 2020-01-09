<?php if (!defined('APPLICATION')) exit();

class PersonnelFileModel extends Gdn_Model {
    const APIBaseURL = 'http://api.29th.org';
    const APIMembersResource = '/members/view';
    
    public function GetByID($ForumMemberID) {
        $response = $this->CallAPI(self::APIBaseURL . self::APIMembersResource, array('forum_member_id' => $ForumMemberID));
        
        if($response) {
            $data = json_decode($response, TRUE);
        
            if($data['status'] == TRUE) {
                return $data['member'];
            }
        }
        
        // Otherwise something went wrong
        return FALSE;
    }
    
    private function CallAPI($url, $data = false) {
        if($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
        return file_get_contents($url);
    }
}