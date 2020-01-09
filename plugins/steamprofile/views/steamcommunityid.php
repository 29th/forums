<?php if (!defined('APPLICATION')) exit(); ?>
	<li class="SteamProfile">
      <?php
         echo $this->Form->Label('Steam Profile');

         // Do we happen to already have a Steam ID for our current user?
         if ($this->Data('SteamID64')) {
            // If so, we just output it.  Nothing fancy.
            echo '<div>'.T('Steam ID').': '.Gdn_Format::Text($this->Data('SteamID64')).'</div>';
         } else {
            // If not, we drop in a button and set the stage for OpenID magic.
            echo Anchor(
               Img('plugins/steamprofile/design/images/sits_small.png', array('alt' => 'Sign in through Steam')),
               $this->Data('SteamAuthenticationUrl'),
               '',
               array('title' => 'Sign in through Steam')
            );
         }
      ?>
   </li>
