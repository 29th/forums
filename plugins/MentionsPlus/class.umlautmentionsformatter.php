<?php if (!defined('APPLICATION')) exit();

class UmlautMentionsFormatter {
   /**
    *  replaces /library/core/functions.general.php function GetMentions
    */
   public function GetMentions($String) {
      // This one grabs mentions that start at the beginning of $String
      // without spaces
      $StrippedValidationRegex = '['.str_replace(' ', '', str_replace('\s', '', C('Garden.User.ValidationRegex', '\d\w_'))).']'.C('Garden.User.ValidationLength','{3,20}');
      $NoSpacesRegex = '('.$StrippedValidationRegex.')\b';
      // with spaces
      $WithSpacesRegex = C('Plugins.MentionsPlus.MentionStart', '"').'('.ValidateUsernameRegex().')'.C('Plugins.MentionsPlus.MentionStop', '"');
      preg_match_all(
         '/(?:^|[\s,\.>])@('.$NoSpacesRegex.'|'.$WithSpacesRegex.')/iu',
         $String,
         $Matches
      );
      return array_filter(array_unique(array_merge($Matches[2], $Matches[3])));
   }

   /**
    *  replaces /library/core/class.format.php function Mention
    *  code is as close to original function (taken from 2.0.18.9) as possible 
    */
   public function FormatMentions($Mixed) {
      if (!is_string($Mixed)) {
         return Gdn_Format::To($Mixed, 'Mentions');
      }

      // Handle @mentions.
      if(C('Garden.Format.Mentions')) {
         // without spaces
         $StrippedValidationRegex = '['.str_replace(' ', '', str_replace('\s', '', C('Garden.User.ValidationRegex'))).']'.C('Garden.User.ValidationLength','{3,20}');
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@('.$StrippedValidationRegex.')\b/iu',
            '\1'.Anchor('@\2', '/profile/\\2'),
            $Mixed
         );

         // with spaces
         $Mixed = preg_replace(
            '/(^|[\s,\.>])@'.C('Plugins.MentionsPlus.MentionStart', '"').'('.ValidateUsernameRegex().')'.C('Plugins.MentionsPlus.MentionStop', '"').'/iu',
            '\1'.Anchor('@'.C('Plugins.MentionsPlus.MentionStart', '"').'\2'.C('Plugins.MentionsPlus.MentionStop', '"'), '/profile/\\2'),
            $Mixed
         );
      }
      
      // Handle #hashtag searches
      if(C('Garden.Format.Hashtags')) {
         $Mixed = preg_replace(
            '/(^|[\s,\.>])\#([\w\-]+)(?=[\s,\.!?]|$)/iu',
            '\1'.Anchor('#\2', '/search?Search=%23\2&Mode=like').'\3',
            $Mixed
         );
      }
      // Handle "/me does x" action statements
      if(C('Garden.Format.MeActions')) {
         $Mixed = preg_replace(
            '/(^|[\n])(\\'.C('Plugins.MentionsPlus.MeActionCode', '/me').')(\s[^(\n)]+)/iu',
            '\1'.Wrap(Wrap('\2', 'span', array('class' => 'MeActionName')).'\3', 'span', array('class' => 'AuthorAction')),
            $Mixed
         );
      }
      return $Mixed;
   }
}
