
<div class="acl index">
	<?php
echo $this->element('design/header');
?>

<?php
echo $this->element('Aros/links');
?>

<?php
if(count($missing_aros['roles']) > 0)
{
	echo '<h3>' . __d('acl', 'Roles without corresponding Aro') . '</h3>';
	
	$list = array();
	foreach($missing_aros['roles'] as $missing_aro)
	{
		$list[] = $missing_aro[$role_model_name][$role_display_field];
	}
	
	echo $this->Html->nestedList($list);
}
?>

<?php
if(count($missing_aros['users']) > 0)
{
	echo '<h3>' . __d('acl', 'Users without corresponding Aro') . '</h3>';
	
	$list = array();
	foreach($missing_aros['users'] as $missing_aro)
	{
		$list[] = $missing_aro[$user_model_name][$user_display_field];
	}
	
	echo $this->Html->nestedList($list);
}
?>

<?php
if(count($missing_aros['roles']) > 0 || count($missing_aros['users']) > 0)
{
	echo '<p>';
	echo $this->Html->link(__d('acl', 'Build'), '/admin/acl/aros/check/run');
	echo '</p>';
}
else
{
	echo '<p>';
	echo __d('acl', 'There is no missing ARO.');
	echo '</p>';
}
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