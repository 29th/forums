<?php

class AddRegistrationQuestionPlugin extends Gdn_Plugin {

    public function gdn_dispatcher_appStartup_handler() {
        if (c('AddRegistrationQuestion.Basic')) {
            saveToConfig('Garden.Registration.SkipCaptcha', true, false);
        }
    }


    public function entryController_registerFormBeforeTerms_handler($sender) {
        echo wrap($sender->Form->label($this->question(), 'Question').$sender->Form->textBox('Question'), 'li');
    }


    public function entryController_registerValidation_handler($sender) {
        if (!$this->isCorrect($sender->Form->getValue('Question'))) {
            $sender->Form->addError('The security question was answered incorrectly.');
            $sender->render();
            exit();
        }
    }


    public function settingsController_addRegistrationQuestion_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->title('Registration Question');

        $conf = new ConfigurationModule($sender);
        $conf->initialize([
            'AddRegistrationQuestion.Question' => [
                'Control' => 'textbox',
                'LabelCode' => 'Question',
                'Description' => 'Do not use the default question and change this from time to time for best results.',
                'Default' => $this->question()
            ],
            'AddRegistrationQuestion.Answer' => [
                'Control' => 'textbox',
                'LabelCode' => 'Answer',
                'Description' => 'The check for the correct answer is case-insensitive. You can specify multiple comma-separated answers.',
                'Default' => $this->answer()
            ],
            'AddRegistrationQuestion.Basic' => [
                'Control' => 'checkbox',
                'LabelCode' => 'Use this as the only form of registration validation (disable CAPTCHA).'
            ]
        ]);

        $conf->renderAll();
    }


    private function isCorrect($attempt = '') {
        $answers = explode(',', $this->answer());

        foreach ($answers as $answer) {
            if (strcasecmp(trim($answer), $attempt) == 0) {
                return true;
            }
        }

        return false;
    }   


    private function question() {
        return t(c('AddRegistrationQuestion.Question', 'Are you a bot?'));
    }


    private function answer() {
        return t(c('AddRegistrationQuestion.Answer', 'no'));
    }

}
