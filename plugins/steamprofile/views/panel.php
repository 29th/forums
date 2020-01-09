<?php if (!defined('APPLICATION')) exit(); ?>
<div class="Box Steam">
   <h4>Steam Profile</h4>
   <dl>
      <dt class="SteamAvatar"><img alt="Avatar Icon" src="<?php echo $this->Data('SteamProfile')->avatarIcon; ?>" /></dt>
      <dd class="SteamCard">
         <?php echo Gdn_Theme::Link('http://steamcommunity.com/profiles/'.$this->Data('SteamProfile')->steamID64, $this->Data('SteamProfile')->steamID, '<a href="%url" class="%class" target="_blank" >%text</a>'); ?><br />
         <?php echo Gdn_Format::Text($this->Data('SteamProfile')->stateMessage); ?>
      </dd>
      <?php if ($this->Data('SteamProfile')->privacyState == 'public'): ?>
      <dt class="SteamMemberSince">Member Since</dt>
      <dd class="SteamMemberSince"><?php echo Gdn_Format::Text($this->Data('SteamProfile')->memberSince); ?></dd>
      <dt class="SteamRating">Steam Rating</dt>
      <dd class="SteamRating"><?php echo Gdn_Format::Text($this->Data('SteamProfile')->steamRating); ?></dd>
      <dt class="SteamPlayingTime">Playing Time</dt>
      <dd class="SteamPlayingTime"><?php echo Gdn_Format::Text($this->Data('SteamProfile')->hoursPlayed2Wk.' hrs past 2 weeks'); ?></dd>
      <?php if ($this->Data('MostPlayedGame', FALSE)) : ?>
      <dt class="SteamPlayedGame"><img alt="<?php echo Gdn_Format::Text($this->Data('MostPlayedGame')->gameName); ?>" src="<?php echo $this->Data('MostPlayedGame')->gameIcon; ?>" /></dt>
      <dd class="SteamPlayedGame">
         <?php echo Gdn_Format::Text($this->Data('MostPlayedGame')->gameName); ?><br />
         <?php echo Gdn_Format::Text($this->Data('MostPlayedGame')->hoursPlayed.' hrs / '.$this->Data('MostPlayedGame')->hoursOnRecord).' hrs'; ?></br>
         <?php echo Gdn_Theme::Link('http://steamcommunity.com/profiles/'.$this->Data('SteamProfile')->steamID64.'/stats/'.$this->Data('MostPlayedGame')->statsName, 'View stats', '<a href="%url" class="%class" target="_blank" >%text</a>'); ?>
      </dd>
      <?php endif; ?>
      <dt></dt>
      <dd><?php echo Gdn_Theme::Link('http://steamcommunity.com/profiles/'.$this->Data('SteamProfile')->steamID64.'/games?tab=all', 'View All Games', '<a href="%url" class="%class" target="_blank" >%text</a>'); ?></dd>
      <dt></dt>
      <dd><?php echo Gdn_Theme::Link('http://steamcommunity.com/profiles/'.$this->Data('SteamProfile')->steamID64.'/wishlist', 'View Wishlist', '<a href="%url" class="%class" target="_blank" >%text</a>'); ?></dd>
      <?php endif; ?>
      <dt class="SteamUpdated">Updated</dt>
      <dd class="SteamUpdated"><?php echo Gdn_Format::FuzzyTime($this->Data('SteamProfile')->DateRetrieved, TRUE); ?></dd>
   </dl>
</div>
