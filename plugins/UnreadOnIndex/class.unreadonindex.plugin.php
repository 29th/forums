<?php if (!defined('APPLICATION')) exit();

$PluginInfo['UnreadOnIndex'] = array(
	'Name' => 'UnreadOnIndex',
	'Description' => 'Show new/unread tags on category index',
	'Version' => '1.0',
	'MobileFriendly' => TRUE,
	'Author' => 'Wilson29thID',
	'AuthorEmail' => 'wilson@29th.org',
	'AuthorUrl' => 'http://29th.org',
	'License' => 'MIT'
);

class UnreadOnIndex extends Gdn_Plugin {
    
    public function Base_AfterCategoryTitle_Handler($Sender, $Args) {
        if( ! $Args['Category']['Read']) {
            echo ' <strong class="HasNew JustNew NewCommentCount" title="You haven\'t read this yet.">new</strong>';
        }
    }

}
