<?php
// var_dump($campaigns);


?>
<div class="campaigns index">
	<h2><?php echo __('Campaigns'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('alias'); ?></th>
			<th><?php echo $this->Paginator->sort('external'); ?></th>
			<th><?php echo $this->Paginator->sort('method'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($campaigns as $campaign): ?>
	<tr>
		<td><?php echo h($campaign['Campaign']['id']); ?>&nbsp;</td>
		<td><?php echo h($campaign['Campaign']['name']); ?>&nbsp;</td>
		<td><?php echo h($campaign['Campaign']['alias']); ?>&nbsp;</td>
		<td><?php echo h($campaign['Campaign']['external']); ?>&nbsp;</td>
		<td><?php 
		echo (h($campaign['Campaign']['method'])==0) ? 'Ajax Post' : 'Form Post'; 
		?>&nbsp;</td>
		<td>
			<?php 
			echo $this->Html->link(
			h($campaign['User']['username']), 
			array('controller' => 'users', 'action' => 'view', 
			$campaign['User']['id'])
			); ?>
		</td>
		<td><?php echo h($campaign['Campaign']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $campaign['Campaign']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $campaign['Campaign']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $campaign['Campaign']['id']), null, __('Are you sure you want to delete # %s?', $campaign['Campaign']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">

	<h3>Campaign Edit</h3>
	<ul>
		<li><?php echo $this->Html->link(__('New'), array('action' => 'add')); ?></li>
	</ul>

	<h3><?php echo __('Menus'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Leads'), array('controller' => 'leads', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Logs'), array('controller' => 'logs', 'action' => 'index')); ?> </li>
	</ul>

	<?php if($user['Group']['name'] == 'administrators'): ?>
    <h3><?php echo __('Administrator'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => 'campaigns', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List Batch Emails'), array('controller' => 'emails', 'action' => 'index')); ?></li>
        <li><a href="<?php echo $this->Html->url('/admin/acl', true);?>">ACL</a></li>

    </ul>
	<?php endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?php echo $this->Html->url('/users/logout', true);?>">Logout</a></div>

</div>


