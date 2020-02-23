<?php if (!defined('APPLICATION')) exit(); 
$PersonnelBaseUrl = 'https://personnel.29th.org'; ?>
<div class="PersonnelFile">
    <h4>Personnel File</h4>
    <div class="list-group">
        <div class="list-group-item">
            <div>
                <?php echo Gdn_Theme::Link($PersonnelBaseUrl . '/#members/' . $this->Data('PersonnelFile')['id'], $this->Data('PersonnelFile')['short_name'], '<a href="%url" class="%class" target="_blank" >%text</a>'); ?>
            </div>
            <?php if($this->Data('PersonnelFile')['unit']['id']): ?>
                <div>
                    <?php echo $this->Data('PersonnelFile')['position']['name']; ?>,
                    <?php echo Gdn_Theme::Link($PersonnelBaseUrl . '/#units/' . $this->Data('PersonnelFile')['unit']['id'], $this->Data('PersonnelFile')['unit']['abbr'], '<a href="%url" class="%class" target="_blank" >%text</a>'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
