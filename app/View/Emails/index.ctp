<div class="emails index">
	<h2><?php echo __('Batch Email Report'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('model'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id', 'From'); ?></th>
			<th><?php echo $this->Paginator->sort('to'); ?></th>
			<th><?php echo $this->Paginator->sort('subject'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('published'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($emails as $email): ?>
	<tr>
		<td><?php echo h($email['Email']['id']); ?>&nbsp;</td>
		<td><?php 
		$model = json_decode($email['Email']['model']); 
		echo ucfirst($model->model);
		?>&nbsp;</td>
		<td>
			<?php 
			echo $this->Html->link($email['User']['username'], array('controller' => 'users', 'action' => 'view', $email['User']['id'])); 
			?>
		</td>
		<td><?php 
		echo $this->Text->truncate(
		    $email['Email']['to'],
		    30,
		    array(
		        'ellipsis' => '...',
		        'exact' => true
		    )
		);
		?>&nbsp;</td>
		<td><?php 
		echo $this->Text->truncate(
		    $email['Email']['subject'],
		    30,
		    array(
		        'ellipsis' => '...',
		        'exact' => false
		    )
		);
		?>&nbsp;</td>
		<td><?php echo h($email['Email']['created']); ?>&nbsp;</td>
		<td style="text-align: center;">
		<?php 
		$img = ($email['Email']['published'] == 1)? 'tick': 'cross';
		echo '<img alt="" src="./acl/img/design/'.$img.'.png">';
		?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $email['Email']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $email['Email']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $email['Email']['id']), null, __('Are you sure you want to delete # %s?', $email['Email']['id'])); ?>
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
	<h3><?php echo __('Batch Email Edit'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New'), array('action' => 'add')); ?></li>
	</ul>
	<h3><?php echo __('Menus'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Leads'), array('controller' => 'leads', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Logs'), array('controller' => 'logs', 'action' => 'index')); ?> </li>
	</ul>

    <? 
<?php if($curuser['Group']['name'] == 'administrators'): ?>
    <h3><?php echo __('Administrator'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => 'campaigns', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List Batch Emails'), array('controller' => 'emails', 'action' => 'index')); ?></li>
        <li><a href="<?php echo $this->Html->url('/admin/acl', true);?>">ACL</a></li>
    </ul>
    <? 
<?php endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?php echo $this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>

