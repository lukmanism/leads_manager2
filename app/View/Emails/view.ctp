<div class="emails view">
<h2><?php  echo __('Email'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($email['Email']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Model'); ?></dt>
		<dd>
			<?php
			$model = json_decode($email['Email']['model']); 
			echo ucfirst($model->model); 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('From'); ?></dt>
		<dd>
			<?php echo $this->Html->link($email['User']['username'], array('controller' => 'users', 'action' => 'view', $email['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('To'); ?></dt>
		<dd>
			<?php 
			echo str_replace(',', ', ', $email['Email']['to']); 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Cc'); ?></dt>
		<dd>
			<?php 
			echo str_replace(',', ', ', $email['Email']['cc']); 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Subject'); ?></dt>
		<dd>
			<?php echo h($email['Email']['subject']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Body'); ?></dt>
		<dd>
			<?php 
			echo str_replace("\n", '</br>', $email['Email']['body']); 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Footer'); ?></dt>
		<dd>
			<?php 
			echo str_replace("\n", '</br>', $email['Email']['footer']); 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Published'); ?></dt>
		<dd>
			<?php
			echo ($email['Email']['published'] == 1)? 'True': 'False'; 
			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($email['Email']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Emails'), array('action' => 'index')); ?> </li>
	</ul>
</div>
