<?php

class ConversationsUserSearchModule extends Gdn_Module {

    public function assetTarget() {
        return 'Panel';
    }

    public function toString() {
        $controller = Gdn::controller();
        return wrap(
            panelHeading(t('Search by User'))
                .$controller->Form->open(['action' => url('messages/user')])
                .wrap(
                    $controller->Form->textBox('UserSearch', ['MultiLine' => true, 'class' => 'MultiComplete']),
                    'div',
                    ['class' => 'TextBoxWrapper']
                )
                .$controller->Form->close('Search', '', ['class' => 'Button Action']),
            'div',
            ['class' => 'Box UserSearch']
        );
    }

}
