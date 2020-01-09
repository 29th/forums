<?php
$PluginInfo['conversationSearch'] = [
    'Name' => 'Conversation Search',
    'Description' => 'Allows searching in conversations.',
    'Version' => '0.2.0',
    'RequiredApplications' => [
        'Vanilla' => '>= 2.3',
        'Conversations' => '>= 2.3'
    ],
    'SettingsPermission' => 'Garden.Settings.Manage',
    'SettingsUrl' => 'settings/conversationsearch',
    'MobileFriendly' => true,
    'HasLocale' => true,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/r_j',
    'License' => 'MIT'
];

/**
 * Possible enhancements ("todos")
 * - settings page is buggy for 2.4 beta
 * - allow filtering by author and date
 * - enclose subject in search (maybe with dropdown: search in subject, body, both). Needs uncommenting in structure()!!!
 * - make list entries click targets (fuck CSS, I think I give up...)
 */

class ConversationSearchPlugin extends Gdn_Plugin {
    /**
     * Init db changes and config values.
     *
     * @return void.
     */
    public function setup() {
        $this->structure();
        touchConfig(
            'conversationSearch.PerPage',
            c('Garden.Search.PerPage', 20)
        );
    }

    /**
     * Add fulltext index to ConversationMessage and Subject.
     *
     * Adding fulltext keys isn't easy with Vanilla, so instead of using a
     * simple statement done with the query builder, raw sql needs to be used.
     * This implementation should work, but if the plugin isn't working as
     * expected, that might be the cause for the problems.
     *
     * Searching in Subject isn't yet implemented. As soon as searching in
     * Subject should be implemented, the line in the tables array must be
     * un-commented!!!
     *
     * @return void.
     */
    public function structure() {
        $versionInfo = explode('.', Gdn::sql()->version());
        $px = Gdn::database()->DatabasePrefix;

        $tables = [
            // Searching in Subject is not implemented by now.
            // The line needs to be un-commented if that feature should be
            // implemented!!!
            // ['Name' => 'Conversation', 'FulltextColumn' => 'Subject'],
            ['Name' => 'ConversationMessage', 'FulltextColumn' => 'Body']
        ];

        foreach($tables as $table) {
            $tableName = Gdn::database()->DatabasePrefix.$table['Name'];
            $tableName = Gdn::sql()->formatTableName($tableName);

            // InnoDB in MySQL < 5.6 doesn't support fulltext, so force MyISAM.
            if (!($versionInfo[0] >= 5 && $versionInfo[1] >= 6)) {
                $sql = "ALTER TABLE `{$tableName}` ENGINE = MyISAM; ";
                Gdn::structure()->query($sql);
            }
            // Get all indexes of current table.
            $indexes = Gdn::structure()
                ->query("SHOW INDEXES FROM `{$tableName}`")
                ->resultObject();
            // Loop through indexes to determine of column already has fulltext index.
            $indexExists = false;
            foreach($indexes as $index) {
                if (
                    $index->Column_name == $table['FulltextColumn'] &&
                    $index->Index_type == 'FULLTEXT'
                ) {
                    $indexExists = true;
                }
            }
            if (!$indexExists) {
                $keyName = "TX_{$table['Name']}_ConversationSearch";
                $sql = "ALTER TABLE `{$tableName}` ";
                $sql .= "ADD FULLTEXT $keyName (`{$table['FulltextColumn']}`); ";
                Gdn::structure()->query($sql);
            }
        }
        /**
         * The Easiest way isn't working because InnoDBs fulltext
         * capabilities are not supported  yet.
         */
        /*
        Gdn::database()->structure()
            ->table('Conversation')
            ->column('Subject', 'varchar(255)', null, 'fulltext')
            ->set();
        Gdn::database()->structure()
            ->table('ConversationMessage')
            ->column('Body', 'text', false, 'fulltext')
            ->set();
        */
    }

    /**
     * Settings screen.
     *
     * Placeholder. Nor sure if this would ever be needed...
     * @param SettingsController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function settingsController_conversationSearch_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        if (method_exists($sender, 'setHighlightRoute')) {
            $sender->setHighlightRoute('settings/plugins');
        } else {
            $sender->addSideMenu('settings/plugins');
        }
        $sender->setData('Title', t('This ain\'t no Settings Page...'));
        $sender->render('settings', '', 'plugins/conversationSearch');
        // conversationSearch.PerPage
    }


    /**
     * Add module to messages controller.
     *
     * @param MessagesController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function messagesController_render_before($sender) {
        // Don't show module if we are already on the search page.
        if ($sender->RequestMethod == 'search') {
            return;
        }
        $conversationSearchModule = new ConversationSearchModule();
        $conversationSearchModule->conversationID = val('ConversationID', $sender->Conversation, 0);
        $sender->addModule($conversationSearchModule);
    }

    /**
     * Conversation search page.
     *
     * Shows search bar and search results.
     *
     * @param MessagesController $sender Instance of the calling class.
     *
     * @return void.
     */
    public function messagesController_search_create($sender) {
        Gdn_Theme::section('Conversation');

        // Only available for logged in users.
        if (!Gdn::session()->isValid()) {
            throw permissionException();
        }

        // Basic page setup.
        $sender->title(t('Search Conversations'));
        $sender->addBreadcrumb('Search', '/messages/search');
        $sender->View = $sender->fetchViewLocation('conversationsearch', '', 'plugins/conversationSearch');

        $search = $sender->Request->getValue('Search', '');
        if ($search == '') {
            $sender->render();
        }

        // get only a subset of results.
        $page = $sender->Request->getValue('Page', '');
        list($offset, $limit) = offsetLimit($page, c('conversationSearch.PerPage', 20));
        $sender->setData('_Limit', $limit);

        $filter['ID'] = $sender->Request->getValue('ID', 0);

        // $searchModel = new SearchModel();
        // $searchModel = new ConversationSearchModel();

        try {
            // $resultSet = $searchModel->search($search, $offset, $limit, $filter);
            $resultSet = $this->search($search, $offset, $limit, $filter);
        } catch (Gdn_UserException $ex) {
            $sender->Form->addError($ex);
            $resultSet = [];
        } catch (Exception $ex) {
            logException($ex);
            $sender->Form->addError($ex);
            $resultSet = [];
        }

        $sender->setData('Results', $resultSet);
        $sender->setData('ResultCount', count($resultSet));
        $sender->setData('Search',  $search);

        // Render view
        $sender->render();
    }

    /**
     * Performs a boolean search in conversation messages.
     *
     * Allows using "+" (AND) and "-" (NOT) in search.
     *
     * @param string $search The string to look up.
     * @param integer $offset The offset to start from (for pagination).
     * @param integer $limit The limit of results (for pagination).
     * @param array $filter Array with filter columns and criteria. Possible
     *   values by now:
     *   integer ID: a ConversationID
     *   integer|false userID: a conversations participant user id, defaults to
     *     session user. Using "false" will skip user restriction.
     *
     * @return array Search result.
     */
    private function search($search, $offset = 0, $limit = 20, $filter = []) {
        // If a third party plugin wants to take influence on the source,
        // this would be a possibility to change the search string.
        // $this->EventArguments['Search'] = &$Search;
        // $this->fireEvent('Search');

        // If there are no searches then return an empty array.
        if (trim($search) == '') {
            return [];
        }

        $filter = array_change_key_case($filter);

        // Filter: by user
        $userID = val('userid', $filter, Gdn::session()->UserID);
        if ($userID !== false) {
            Gdn::sql()->where('uc.UserID', $userID);
        }

        // Filter: by id
        if (array_key_exists('id', $filter) && $filter['id'] > 0) {
            Gdn::sql()->where('c.ConversationID', $filter['id']);
        }

        // Build the search sql.
        $sql = Gdn::sql()
            ->distinct()
            ->select('cm.Body', "match (%s) against (:search1 in boolean mode)", 'Relevance')
            ->select('cm.MessageID as PrimaryID, c.Subject as Title, cm.Body as Summary, cm.Format')
            ->select("'messages/', cm.ConversationID, '#Message_', cm.MessageID", 'concat', 'Url')
            ->select('cm.DateInserted')
            ->select('cm.InsertUserID as UserID')
            ->select("'Message'", '', 'RecordType')
            ->select('u.Name, u.Photo, u.Email')
            ->from('ConversationMessage cm')
            ->join('Conversation c', 'cm.ConversationID = c.ConversationID', 'left outer')
            ->join('UserConversation uc', 'uc.ConversationID = cm.ConversationID', 'left outer')
            ->join('User u', 'cm.InsertUserID = u.UserID', 'left outer')
            ->where('uc.Deleted', 0) // Conversations which have been left cannot be searched
            ->where("match(cm.Body) against (:search2 in boolean mode)", null, false, false)
            ->orderBy('cm.DateInserted', 'desc')
            ->limit($limit, $offset)
            ->getSelect();

        // Third party plugins would be able to take influence on the query.
        // $this->EventArguments['Sql'] = &$sql;
        // $this->fireEvent('AfterBuildSearchQuery');

        // Add two named parameters for our search and execute the query.
        Gdn::sql()->namedParameter(':search1', false, $search);
        Gdn::sql()->namedParameter(':search2', false, $search);
        $result = Gdn::sql()->query($sql)->resultArray();

        // Condense Body To Summary.
        foreach ($result as $key => $value) {
            if (isset($value['Summary'])) {
                $result[$key]['Summary'] = condense(Gdn_Format::to($value['Summary'], $value['Format']));
            }
        }

        return $result;
    }
}
