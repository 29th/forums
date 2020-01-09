<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<h1><?php echo T("Online Now"); ?></h1>
      <div class="Info"><?php echo T('Where should the plugin be shown?'); ?></div>
      <table class="AltRows">
         <thead>
            <tr>
               <th><?php echo T('Sections'); ?></th>
               <th class="Alt"><?php echo T('Description'); ?></th>
            </tr>
         </thead>
         <tbody>
               <tr>
                  <th><?php
                     echo $this->Form->Radio('OnlineNow.Location.Show', "Every", array('value' => 'every', 'selected' => 'selected'));
                  ?></th>
                  <td class="Alt"><?php echo T("This will show the panel on every page."); ?></td>
               </tr>
                <tr>
                     <th><?php
                        echo $this->Form->Radio('OnlineNow.Location.Show', "Discussion", array('value' => "discussion"));
                     ?></th>
                     <td class="Alt"><?php echo T("This show the plugin on only selected discussion pages"); ?></td>
                </tr>
         </tbody>
      </table>
			<table class="AltRows">  
         <tbody>
               <tr>
                  <th><?php
                     echo $this->Form->Checkbox('OnlineNow.Hide', "Hide for non members of the site");
                  ?></th>
               </tr>             
         </tbody>
      </table>
      <table class="AltRows">
         <thead>
            <tr>
               <th><?php echo T('Frequency'); ?></th>
               <th class="Alt"><?php echo T('In seconds'); ?></th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <th><?php echo T('Rate of refresh'); ?></th>
               <td class="Alt"><?php echo $this->Form->TextBox('OnlineNow.Frequency'); ?></td>
            </tr>
         </tbody>
      </table>

<?php echo $this->Form->Close('Save');
