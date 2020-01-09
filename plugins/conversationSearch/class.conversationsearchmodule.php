<?php
class ConversationSearchModule extends Gdn_Module {
    /** @var integer The ID of the currently displayed conversation */
    public $conversationID = 0;

    public function __construct($sender = '') {
        parent::__construct($sender);
    }

    public function assetTarget() {
        return 'Panel';
    }

    public function toString() {
        $this->Form = new Gdn_Form();
        echo '<div class="Box BoxConversationSearch">';
        echo '<h4>'.t('Search in Conversations').'</h4>';
        echo $this->Form->open(['action' => url('/messages/search'), 'method' => 'get']);
        echo $this->Form->textBox('Search', ['aria-label' => t('Enter your search term.'), 'class' => 'InputBox']);
        // Allow searching in current conversation if appropriate.
        if ($this->conversationID > 0) {
            echo $this->Form->checkBox(
                'ID',
                'Only this conversation',
                [
                    'value' => $this->conversationID,
                    'checked' => 'checked'
                ]
            );
        }
        echo $this->Form->button('Search', ['aria-label' => t('Search'), 'Name' => '']);
        echo $this->Form->close();
        echo '</div>';
    }
}
