<?php if (!defined('APPLICATION')) exit();
$PluginInfo['ConversationsClearButton'] = array(
	'Name' => 'Conversations Clear Button',
	'Description' => 'Adds a clear/delete button to every conversation in the inbox.',
	'Version' => '0.2',
	'RequiredApplications' => array('Vanilla' => '2.1'),
	'Author' => 'Bleistivt',
	'AuthorUrl' => 'http://bleistivt.net',
	'MobileFriendly' => true
);

class ConversationsClearButtonPlugin extends Gdn_Plugin {
	public function MessagesController_BeforeConversationMeta_Handler($Sender) {
		echo Wrap(
			Anchor('x',
				'/messages/clear/'.$Sender->EventArguments['Conversation']->ConversationID.'/'.Gdn::Session()->TransientKey(),
				'Delete InboxClearConversation', array('title' => T('Delete Conversation'))
			),
			'div', array('class' => 'Options'));
	}
	public function MessagesController_Render_Before($Sender) {
		$Sender->Head->AddString('
	<style type="text/css">
		.DataList a.InboxClearConversation{position:absolute;right:5px;top:5px;}
	</style>');
		$Sender->AddJsFile('clearbutton.js', 'plugins/ConversationsClearButton');
	}
}
