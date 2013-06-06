<div class="logs view">
<h2><?php  echo __('Log'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($log['Log']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Leads'); ?></dt>
		<dd>
			<?php echo $this->Html->link($log['Leads']['id'], array('controller' => 'leads', 'action' => 'view', $log['Leads']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Campaign'); ?></dt>
		<dd>
			<?php echo $this->Html->link($log['Campaign']['name'], array('controller' => 'campaigns', 'action' => 'view', $log['Campaign']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Referer'); ?></dt>
		<dd>
			<?php echo h($log['Log']['referer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ip'); ?></dt>
		<dd>
			<?php echo h($log['Log']['ip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Logs'); ?></dt>
		<dd>
			<?php echo h($log['Log']['logs']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($log['Log']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($log['Log']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Menus'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Logs'), array('controller' => 'logs', 'action' => 'index')); ?> </li>
	</ul>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>
