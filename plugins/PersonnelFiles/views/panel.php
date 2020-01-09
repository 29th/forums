<?php if (!defined('APPLICATION')) exit(); 
$PersonnelBaseUrl = 'http://personnel.29th.org'; ?>
<div class="Box PersonnelFile">
    <h4>Personnel File</h4>
    <div class="short_name">
        <?php echo Gdn_Theme::Link($PersonnelBaseUrl . '/#members/' . $this->Data('PersonnelFile')['id'], $this->Data('PersonnelFile')['short_name'], '<a href="%url" class="%class" target="_blank" >%text</a>'); ?>
    </div>
    <div class="assignment">
        <?php if($this->Data('PersonnelFile')['unit']['id']): ?>
            <?php echo $this->Data('PersonnelFile')['position']['name']; ?>,
            <?php echo Gdn_Theme::Link($PersonnelBaseUrl . '/#units/' . $this->Data('PersonnelFile')['unit']['id'], $this->Data('PersonnelFile')['unit']['abbr'], '<a href="%url" class="%class" target="_blank" >%text</a>'); ?>
        <?php endif; ?>
    </div>
</div>