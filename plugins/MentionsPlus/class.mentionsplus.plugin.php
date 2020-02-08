<?php if (!defined('APPLICATION')) exit();

$PluginInfo['MentionsPlus'] = array(
   'Name' => 'Mentions+',
   'Description' => 'Mentions+ allows usage of nearly arbitrary characters in mentions. Mentions will use every character that is allowed for registration. User names with spaces could be escaped to be used in mentions, too',
   'Version' => '0.12',
   'RequiredApplications' => array('Vanilla' => '>=2.0.18'),
   'SettingsUrl' => '/settings/mentionsplus',
   'SettingsPermission' => 'Garden.Moderation.Manage',
   'HasLocale' => FALSE,
   'Author' => 'Robin',
   'License' => 'GNU GPLv2',
   'MobileFriendly' => TRUE
);

class MentionsPlusPlugin extends Gdn_Plugin {
   public function Setup() {
    // Set config settings only if they are not already set
      if (!C('Garden.User.ValidationRegex')) {
         // numbers, letters a to z, space and special German characters
         SaveToConfig('Garden.User.ValidationRegex', '\d\w_ äöüß');
      }
      if (!C('Garden.User.ValidationLength')) {
         SaveToConfig('Garden.User.ValidationLength', '{3,20}');
      }
      if (!C('Plugins.MentionsPlus.MentionStart')) {
         SaveToConfig('Plugins.MentionsPlus.MentionStart', '"');
      }
      if (!C('Plugins.MentionsPlus.MentionStop')) {
         SaveToConfig('Plugins.MentionsPlus.MentionStop', '"');
      }
      if (!C('Plugins.MentionsPlus.MeActionCode')) {
         SaveToConfig('Plugins.MentionsPlus.MeActionCode', '/me');
      }
   }

   public function SettingsController_MentionsPlus_Create($Sender) {
      $Sender->Permission('Garden.Settings.Manage');
      $Sender->SetData('Title', T('Mentions+ Settings'));
      $Sender->AddSideMenu('dashboard/settings/plugins');

      $Conf = new ConfigurationModule($Sender);
      $Conf->Initialize(array(
         'Garden.User.ValidationRegex' => array(
            'LabelCode' => 'Letters allowed in user name',
            'Control' => 'TextBox',
            'Default' => '\d\w_ äöüß',
            'Description' => T('Settings ValidationRegex Description', 'Regular expression that evaluates a valid username (<a href="http://www.php.net/manual/en/ref.pcre.php">regex</a>)')
         ),
         'Garden.User.ValidationLength' => array(
            'LabelCode' => 'Min/max length of user names',
            'Control' => 'TextBox',
            'Default' => '{3,20}',
            'Description' => T('Settings ValidationLength Description', 'Minimum and maximum length of user names in <a href="http://www.php.net/manual/en/ref.pcre.php">regex</a> notation')
         ),
         'Plugins.MentionsPlus.MentionStart' => array(
            'LabelCode' => 'Beginning escape character',
            'Control' => 'TextBox',
            'Default' => '"',
            'Description' => T('Settings MentionStart Description', 'If using whitespaces in usernames you have to mark what belongs to a username: @"hans wurst".<br /><strong>Enter only one single character!</strong>')
         ),
         'Plugins.MentionsPlus.MentionStop' => array(
            'LabelCode' => 'Ending escape character',
            'Control' => 'TextBox',
            'Default' => '"',
            'Description' => T('Settings MentionStop Description', 'If you would like to use different characters for escaping, you could set them separately: @{hans wurst}.<br /><strong>Enter only one single character!</strong>')
         ),
         'Plugins.MentionsPlus.MeActionCode' => array(
            'LabelCode' => '/me action code',
            'Control' => 'TextBox',
            'Default' => '/me',
            'Description' => T('Settings MeActionCode Description', 'By default "/me" is not translatable. In theory, you could set it to "->ich" or anything you like with this setting.<br /><strong>This feature is not tested at all!</strong>')
         )
      ));
      $Conf->RenderAll();
   }
}
