<?php

class ConversationsPreviewPlugin extends Gdn_Plugin {

    public function messagesController_render_before($sender) {
        $sender->addJsFile('preview.js', 'plugins/conversationspreview');
        $sender->addDefinition('conversationsPreview.preview', t('Preview'));
        $sender->addDefinition('conversationsPreview.edit', t('Edit'));
    }

    public function messagesController_preview_create($sender) {
        $sender->permission('Conversations.Conversations.Add');

        $request = Gdn::request();

        if (!$request->isAuthenticatedPostBack()) {
            throw permissionException('Javascript');
        }

        $sender->EventArguments['MessageBody'] = $request->post('Body');

        $sender->fireEvent('BeforeMessagePreviewFormat');

        echo '<div class="Message">'.Gdn_Format::to(
            $sender->EventArguments['MessageBody'],
            $request->post('Format', c('Garden.InputFormatter'))
        ).'</div>';

        $sender->fireEvent('AfterMessagePreviewFormat');
    }

}
