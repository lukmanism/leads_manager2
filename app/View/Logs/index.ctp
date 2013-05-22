<div class="logs index">
	<h2><?php echo __('Logs'); ?></h2>
<?
if(!isset($_POST['submitloadreport'])):

    echo '<h1>Select report(s) to load</h1><fieldset><form enctype="multipart/form-data" method="post" action="" name="loadreport">';
    foreach ($campaigns as $c_id => $c_name):
        echo '<label><input type="checkbox" name="campaign_id[]" value="'.$c_id.'" />'.$c_name.'</label>';
    endforeach;
    echo '<div><input type="submit" class="submit" name="submitloadreport" value="Load Report(s)" /></div></form></fieldset>';
else:
?>	
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('leads_id'); ?></th>
			<th><?php echo $this->Paginator->sort('campaign_id'); ?></th>
			<th><?php echo $this->Paginator->sort('referer'); ?></th>
			<th><?php echo $this->Paginator->sort('ip'); ?></th>
			<th><?php echo $this->Paginator->sort('logs'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($logs as $log): ?>
	<tr>
		<td><?php echo h($log['Log']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($log['Leads']['id'], array('controller' => 'leads', 'action' => 'view', $log['Leads']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($log['Campaign']['name'], array('controller' => 'campaigns', 'action' => 'view', $log['Campaign']['id'])); ?>
		</td>
		<td><?php echo h($log['Log']['referer']); ?>&nbsp;</td>
		<td><?php echo h($log['Log']['ip']); ?>&nbsp;</td>
		<td><?php 
		echo $this->Text->truncate(
		    $log['Log']['logs'],
		    30,
		    array(
		        'ellipsis' => '...',
		        'exact' => false
		    )
		);

		?>&nbsp;</td>
		<td style="text-align: center;">
		<?php 
		$img = ($log['Log']['type'] == 'ERROR')? 'alert_small.gif': 'warning_small.png';
		echo '<img alt="'.$log['Log']['type'].'" src="./acl/img/design/'.$img.'">';
		?>
		</td>
		<td><?php echo h($log['Log']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $log['Log']['id'])); ?>
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
<?
endif;     
?>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Leads'), array('controller' => 'leads', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Logs'), array('controller' => 'logs', 'action' => 'index')); ?> </li>
	</ul>

	<? if($user['Group']['name'] == 'administrators'): ?>
    <h3><?php echo __('Admin Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('controller' => 'campaigns', 'action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('List Groups'), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('List Batch Emails'), array('controller' => 'emails', 'action' => 'index')); ?></li>
        <li><a href="<?=$this->Html->url('/admin/acl', true);?>">ACL</a></li>

    </ul>
	<? endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>
