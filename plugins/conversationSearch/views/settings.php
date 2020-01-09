<?php defined('APPLICATION') or die; ?>

<h1>This is No Settings Page...</h1>
<div class="Warning">
If you don't have problems using this plugin, you can close this page. It's sole purpose is helping to troubleshoot.
</div>
<div class="Description">
<p>If you don't get any search results or you see strange database errors when searching, it is most probably that setting the fulltext index on your database has failed. Please use a tool like phpMyAdmin or something else, and change the indexes of table <strong>GDN_ConversationMessage</strong>. You would have to add a <strong>fulltext</strong> index to column <strong>Body</strong>.</p>
<p>If this isn't working, you have to either update your <strong>MySQL</strong> server to at least <strong>version 5.6</strong> or you have to change the <strong>table engine</strong> to <strong>MyISAM</strong>.</p>
<p>Good luck!</p>
</div>