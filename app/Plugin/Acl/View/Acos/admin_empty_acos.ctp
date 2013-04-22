
<div class="acl index">
  <?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Acos/links');
?>

<?php

    echo '<p>';
    echo __d('acl', 'This page allows you to clear all actions ACOs.');
    echo '</p>';
    
    echo '<p>&nbsp;</p>';
    
    if($actions_exist)
    {
        echo '<p>';
        echo __d('acl', 'Clicking the link will destroy all existing actions ACOs and associated permissions.');
        echo '</p>';
        
        echo '<p>';
        echo $this->Html->link($this->Html->image('/acl/img/design/cross.png') . ' ' . __d('acl', 'Clear ACOs'), '/admin/acl/acos/empty_acos/run', array('confirm' => __d('acl', 'Are you sure you want to destroy all existing ACOs ?'), 'escape' => false));
        echo '</p>';
    }
    else
    {
        echo '<p style="font-style:italic;">';
        echo __d('acl', 'There is no ACO node to delete');
        echo '</p>';
    }

echo $this->element('design/footer');
?>  
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('List Leads'), array('controller' => '../../leads', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Logs'), array('controller' => '../../logs', 'action' => 'index')); ?> </li>
    </ul>

    <? #if($curuser['Group']['name'] == 'administrators'): ?>
    <h3><?php echo __('Admin Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('List Users'), array('controller' => '../../users', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => '../../campaigns', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Groups'), array('controller' => '../../groups', 'action' => 'index')); ?> </li>
        <li><a href="<?=$this->Html->url('/admin/acl', true);?>">ACL</a></li>
    </ul>
    <? #endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('../../users/logout', true);?>">Logout</a></div>
</div>