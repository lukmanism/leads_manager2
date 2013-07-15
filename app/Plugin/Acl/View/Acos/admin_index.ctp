<div class="acl index">
	<?php 
echo $this->element('design/header');
?>

<?php 
echo $this->element('Acos/links');
?>

<?php
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
        <li><a href="<?php echo $this->Html->url('/admin/acl', true);?>">ACL</a></li>
    </ul>
    <? #endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?php echo $this->Html->url('../../../users/logout', true);?>">Logout</a></div>
</div>
