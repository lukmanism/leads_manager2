
<?php
    echo $this->Html->css('jquery.snippet.min');
    echo $this->Html->script('jquery.snippet.min');
?>
<script type="text/javascript" >
    $(document).ready(function(){
    $("pre.htmlCode").snippet("html",{style:"neon"});
    });
</script>

<div class="campaigns view">
<h2><?php  echo __('Campaign'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Form Setting'); ?></dt>
		<dd>
<div class="snippet">
<pre class="htmlCode">
&lt;form name="<?=$campaign['Campaign']['alias'];?>" method="POST" action="http://leads.e-storm.com/leads/incoming?campaign=<?=$campaign['Campaign']['id'];?>"&gt;<?php
    $rules = json_decode($campaign['Campaign']['rules'],true); 
    $x = 1;
    foreach ($rules as $key1 => $value1) {

echo '
	&lt;div class="row  '.$key1.'"&gt;
		&lt;label for="'.$key1.'"&gt;'.$key1.'&lt;/label&gt;
		&lt;input type="text" class="" name="'.$key1.'" value="" /&gt;
	&lt;/div&gt;';
        $rule = @$value1[0]['rule'];
        $x++;
    }
    echo $this->Form->input('id', array('type' => 'hidden'));
?>

	&lt;div class="row submit"&gt;
		&lt;input type="submit" value="Submit" class="submit"&gt;
	&lt;/div&gt;
&lt;/form&gt;
</pre>
</div>

			&nbsp;
		</dd>
		<dt><?php echo __('Note'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['note']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['created']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Campaign'), array('action' => 'edit', $campaign['Campaign']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Campaign'), array('action' => 'delete', $campaign['Campaign']['id']), null, __('Are you sure you want to delete # %s?', $campaign['Campaign']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Campaign'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Leads'), array('controller' => 'leads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Lead'), array('controller' => 'leads', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Logs'), array('controller' => 'logs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Log'), array('controller' => 'logs', 'action' => 'add')); ?> </li>
	</ul>
</div>

