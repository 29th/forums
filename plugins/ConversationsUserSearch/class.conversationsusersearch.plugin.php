<?php

class ConversationsUserSearchPlugin extends Gdn_Plugin {

    public function messagesController_render_before($sender) {
        if (Gdn_Theme::inSection('ConversationList')) {
            $sender->addDefinition('MaxRecipients', 1);
            // On mobile devices, show the search bar module below the content.
            $sender->addModule('ConversationsUserSearchModule', isMobile() ? 'Content' : 'Panel');
        }
    }

    public function messagesController_user_create($sender, $userID = '', $page = 0) {
        if (!Gdn::session()->isValid()) {
            redirect('/entry/signin?Target='.rawurlencode($sender->SelfUrl));
        }

        // Forward search requests to the actual page.
        if (Gdn::request()->isAuthenticatedPostBack()) {
            if ($searchUser = Gdn::userModel()->getByUsername(Gdn::request()->post('UserSearch'))) {
                redirect('messages/user/'.$searchUser->UserID);
            } else {
                redirect('messages/inbox');
            }
        }

        if (!$searchUser = Gdn::userModel()->getID($userID)) {
            throw notFoundException('User');
        }

        $sender->title(sprintf(
            t('Conversations with %s'),
            htmlspecialchars($searchUser->Name)
        ));

        Gdn_Theme::section('ConversationList');

        list($offset, $limit) = offsetLimit($page, c('Conversations.Conversations.PerPage'));
        $page = pageNumber($offset, $limit);

        $conversations = $this->getbyUser(
            $sender->ConversationModel,
            Gdn::session()->UserID,
            $searchUser->UserID,
            $offset,
            $limit
        )->resultArray();

        $sender->EventArguments['Conversations'] = &$conversations;
        $sender->fireEvent('beforeMessagesAll');

        $sender->setData('Conversations', $conversations);
        $sender->setData('_PagerUrl', 'messages/user/'.$searchUser->UserID.'/{Page}');
        $sender->setData('_Page', $page);
        $sender->setData('_Limit', $limit);
        $sender->setData('_CurrentRecords', count($conversations));

        $sender->render('all', 'messages', 'conversations');
    }

    // Model method to get all conversations between two users.
    public function getbyUser($conversationModel, $userID, $searchUser, $offset, $limit) {
        // Modified version of ConversationModel::get2(), uc2 is the viewing user.
        $data = $conversationModel->SQL
            ->select('c.*')
            ->select('uc2.DateLastViewed')
            ->select('uc2.CountReadMessages')
            ->select('uc2.LastMessageID', '', 'UserLastMessageID')
            ->from('UserConversation uc')
            ->join('UserConversation uc2', 'uc.ConversationID = uc2.ConversationID and uc2.UserID = '.$userID)
            ->join('Conversation c', 'c.ConversationID = uc2.ConversationID')
            ->where('uc.UserID', $searchUser)
            ->where('uc2.Deleted', 0)
            ->orderBy('uc2.DateConversationUpdated', 'desc')
            ->limit($limit, $offset)
            ->get();

        $data->datasetType(DATASET_TYPE_ARRAY);
        $result =& $data->result();

        // Add some calculated fields.
        foreach ($result as &$row) {
            if ($row['UserLastMessageID']) {
                $row['LastMessageID'] = $row['UserLastMessageID'];
            }
            $row['CountNewMessages'] = $row['CountMessages'] - $row['CountReadMessages'];
            unset($row['UserLastMessageID']);
        }

        // Join the participants.
        $conversationModel->joinParticipants($result);

        // Join in the last message.
        Gdn_DataSet::join($result, [
            'table' => 'ConversationMessage',
            'prefix' => 'Last',
            'parent' => 'LastMessageID',
            'child' => 'MessageID',
            'InsertUserID',
            'DateInserted',
            'Body',
            'Format'
        ]);

        return $data;
    }

}
