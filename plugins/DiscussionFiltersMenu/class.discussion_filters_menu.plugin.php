<?php if (!defined('APPLICATION')) exit();

$PluginInfo['DiscussionFiltersMenu'] = array(
	'Name' => 'Discussion Filters Menu',
	'Description' => 'Add items to discussion filters menu on categories index page',
	'Version' => '1.0',
	'MobileFriendly' => TRUE,
	'Author' => 'Wilson29thID',
	'AuthorEmail' => 'wilson@29th.org',
	'AuthorUrl' => 'http://29th.org',
	'License' => 'MIT'
);

class DiscussionFiltersMenu extends Gdn_Plugin {
    
    public function Base_AfterDiscussionFilters_Handler($Sender) {
        echo '<li class="UnreadDiscussions' . ($Controller->RequestMethod == 'unread' ? ' Active' : '') . '">' . Anchor(Sprite('SpDiscussions').' Unread Discussions', '/discussions/unread') . '</li>';
    }
    
}
