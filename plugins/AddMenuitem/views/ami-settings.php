<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>


<h1><?php echo Gdn::Translate('Add Menu Item'); ?></h1>

<div class="Info"><?php echo Gdn::Translate('Add Menu Item Options.'); ?></div>

<table class="AltRows">
    <thead>
        <tr>
            <th><?php echo Gdn::Translate('Name to Appear in Menu'); ?></th>
            <th class="Alt"><?php echo Gdn::Translate('Link for Menu Item'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
        
             <td class="Alt">
<?php echo Gdn::Translate('Links 1-3  will not appear on mobile devices'); ?>
            </td>
        
        
        </tr>    
        <tr>
            <td>1- 
                <?php
                echo $this->Form->TextBox('Plugins.AddMenuitem.Name1');
                ?>
            </td>
            <td class="Alt">
                <?php
		echo $this->Form->TextBox('Plugins.AddMenuitem.Link1', array('class'=>'LinkInput','size'=>"80"));
		?>
            </td>
        </tr>

        <tr>
            <td>2- 
                <?php
                echo $this->Form->TextBox('Plugins.AddMenuitem.Name2');
                ?>
            </td>
            <td>
                <?php
		echo $this->Form->TextBox('Plugins.AddMenuitem.Link2',array('class'=>'LinkInput','size'=>"80"));
		?>
            </td>
        </tr>
        <tr>
            <td>3- 
                <?php
                echo $this->Form->TextBox('Plugins.AddMenuitem.Name3');
                ?>
            </td>
            <td class="Alt">
                <?php
		echo $this->Form->TextBox('Plugins.AddMenuitem.Link3',array('class'=>'LinkInput','size'=>"80"));
		?>
            </td>
        </tr>
 
   <tr>
        
             <td class="Alt">
<?php echo Gdn::Translate('Links 4-5  will appear on mobile devices as well as non-mobile devices'); ?>
            </td>
        
        
        </tr>  
             
           <tr>
            <td>4- 
                <?php
                echo $this->Form->TextBox('Plugins.AddMenuitem.Name4');
                ?>
            </td>
            <td class="Alt">
                <?php
		echo $this->Form->TextBox('Plugins.AddMenuitem.Link4',array('class'=>'LinkInput','size'=>"80"));
		?>
            </td>
        </tr>
        
           <tr>
            <td>5- 
                <?php
                echo $this->Form->TextBox('Plugins.AddMenuitem.Name5');
                ?>
            </td>
            <td class="Alt">
                <?php
		echo $this->Form->TextBox('Plugins.AddMenuitem.Link5',array('class'=>'LinkInput','size'=>"80"));
		?>
            </td>
        </tr>
</tbody>        
</table>
<br />
<?php echo $this->Form->Close('Save');?>



