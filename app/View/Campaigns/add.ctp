<div class="campaigns form"><!-- 
    TO DO 
    1) reformat rules before save - cancel
    2) clean up rule formatting
    -empty rules need to be clear
    -delete email format rule


-->
<h1>Add Campaign</h1>

<?php


//TO DO 
#1) reformat rules before save - cancel
#2) clean up rule formatting


echo $this->Form->create('Campaign');
echo $this->Form->input('name');
echo $this->Form->input('alias');
echo $this->Form->input('external');
echo $this->Form->input('note');

if($user['Group']['name'] == 'administrators'){
    $options = array();
    foreach ($userlist as $key => $user) {
        $options[$user['User']['id']] = $user['User']['username'];
    }
    echo $this->Form->input('user_id', array(
        'options' => $options,
        'empty' => 'Please Select...'
    ));
} else {
	echo $this->Form->hidden('user_id',array('value' => $user['id'], 'type' => 'text'));
}



echo $this->Form->input('method', array(
    'options' => array('Ajax Post', 'Form Post'),
	'empty' => 'Please Select...'
));

?>
<fieldset id="buildyourform"><legend>Form Fields</legend></fieldset>
<input type="button" value="Add a field" class="add" id="add" />


<script type="text/javascript">
$("#add").click(function() {
    var intId = $("#buildyourform h3").length + 1;
    var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
    var fName = $("<div class=\"row first\"><label>Field Name</label><input type=\"text\" class=\"fieldname\" name=\"data[Campaign][rules]["+intId+"][fieldname]\" /></div>");
    var fType = $("<div class=\"row\"><h3 class=\"number\">"+intId+"</h3><label>Required?</label><input class=\"required"+intId+"\" type=\"checkbox\" name=\"data[Campaign][rules]["+intId+"][required]\" value=\"notEmpty\"></div><div class=\"row\"><label>Field Type</label><select class=\"fieldtype\" name=\"data[Campaign][rules]["+intId+"][fieldtype]\" id=\"select" + intId + "\"><option value=\"alphaNumeric\">Alpha Numeric</option><option value=\"blank\">Blank</option><option value=\"custom\">Custom</option><option value=\"email\">Email</option><option value=\"text\" selected=\"selected\">Text</option><option value=\"phone\">Phone</option><option value=\"postal\">Postal</option></select></div><span class=\"properties\" id=\"row" + intId + "\"></span>");
    var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" />");
    removeButton.click(function() {
        $(this).parent().remove();
    });
    fieldWrapper.append(fName);
    fieldWrapper.append(fType);
    fieldWrapper.append(removeButton);
    $("#buildyourform").append(fieldWrapper);
});

$('.fieldtype').live('change', function(){

    $('.required'+propid).removeAttr('disabled');
    $('.required'+propid).prop('checked', false); 

    var propid = $(this).attr('id');
    propid = propid.replace('select','');
    // console.log(propid);

    $('#row'+propid).html('');

    var selected = $(this).val();
    var name = $(this).attr('name');

    var properties;

    var email = "<div class=\"row\"><label>No Duplicates?</label><input type=\"checkbox\" name=\"data[Campaign][rules]["+propid+"][fieldprop]\" value=\"emailDuplicate\"></div>";
    var custom = "<div class=\"row\"><label>Custom Rules<input type=\"text\" name=\"data[Campaign][rules]["+propid+"][fieldprop]\" value=\"\"></label></div>";
    var phone = "<div class=\"row\"><label>Phone Property</label><select class=\"phone\" name=\"data[Campaign][rules]["+propid+"][fieldprop]\"><option value=\"us\">United States</option><option value=\"others\">Others</option></select></div>";
    var postal = "<div class=\"row\"><label>Postal Property</label><select class=\"postal\" name=\"data[Campaign][rules]["+propid+"][fieldprop]\"><option value=\"us\">United States</option><option value=\"others\">Others</option></select></div>";

    switch(selected){
        case 'email':
            properties = email;  
            $('.required'+propid).prop('checked', true);       
            $('.required'+propid).attr('disabled', 'disabled');
        break;
        case 'custom':
            properties = custom;
        break;
        case 'phone':
            properties = phone;
        break;
        case 'postal':
            properties = postal;
        break;
    }
    $('#row'+propid).append(properties);
});

</script>

<?php 
echo $this->Form->end('Save Campaign');
?>
</div>








<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?> </li>
	</ul>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>
