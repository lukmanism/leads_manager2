<div class="emails form">
<?php echo $this->Form->create('Email'); ?>
	<fieldset>
		<legend><?php echo __('Add Email'); ?></legend>
	<?php

	echo $this->Form->input('Modelname', array(
		'label' => 'Load Component',
	    'options' => array('leads' => 'Leads', 'logs' => 'Logs')
		)
	);


	echo '<div class="input select">';
	echo $this->Form->label('Load Report(s)');
    foreach ($campaigns as $c_id => $c_name):
        echo '<label><input type="checkbox" class="select_campaign" value="'.$c_id.'" />'.$c_name.'</label>';
    endforeach;
	echo '</div>';	

	echo $this->Form->hidden('model');
	echo $this->Form->input('user_id', array(
		'label' => 'From',
	    'options' => array($users),
	    'empty' => '(choose one)'
		)
	); # From (Admin only)
	echo $this->Form->input('to');
	echo $this->Form->input('cc');
	echo $this->Form->input('subject', array(
			'after' => '<div><small>Options: {REPORTDATE}</small></div>'
		));
	echo $this->Form->input('body', array(
			'after' => '<div><small>Options: {REPORTDATE} {ATTACHMENT} {REPORTSUMMARY}</small></div>'
		));
	echo $this->Form->input('footer');
	echo $this->Form->input('published');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Menus'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Emails'), array('action' => 'index')); ?></li>
	</ul>
</div>



<script type="text/javascript">
	$('#EmailAddForm').submit(function(){
		var model = "";
		var model_name = "";
		var model_id = "";
		$('#EmailModelname option:selected').each(function(){
			model_name = $(this).val();
		});
        $('.select_campaign').each(function(i,e) {
            if ($(e).is(':checked')) {
                var comma = model_id.length===0?'':',';
                model_id += (comma+e.value);
            }
        });
        model = '{"model":"'+model_name+'","model_id":"'+model_id+'"}';
        $('#EmailModel').val(model);
	});
</script>
