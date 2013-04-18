<div class="leads index">
	<h2><?php echo __('Leads'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('lead'); ?></th>
			<th><?php echo $this->Paginator->sort('campaign_id'); ?></th>
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th><?php echo $this->Paginator->sort('ip'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($leads as $lead): ?>
	<tr>
		<td><?php echo h($lead['Lead']['id']); ?>&nbsp;</td>
		<td><?php echo h($lead['Lead']['lead']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($lead['Campaign']['name'], array('controller' => 'campaigns', 'action' => 'view', $lead['Campaign']['id'])); ?>
		</td>
		<td><?php echo h($lead['Lead']['email']); ?>&nbsp;</td>
		<td><?php echo h($lead['Lead']['ip']); ?>&nbsp;</td>
		<td><?php echo h($lead['Lead']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $lead['Lead']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $lead['Lead']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $lead['Lead']['id']), null, __('Are you sure you want to delete # %s?', $lead['Lead']['id'])); ?>
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
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => 'campaigns', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Campaign'), array('controller' => 'campaigns', 'action' => 'add')); ?> </li>
		<li><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></li>
	</ul>
</div>
