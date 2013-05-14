<div class="campaigns form">
<?php echo $this->Form->create('Campaign'); ?>
	<fieldset>
		<legend><?php echo __('Edit Campaign'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('alias');
		echo $this->Form->input('external');
        
 if($user['Group']['name'] == 'administrators'){
    $options = array();
    foreach ($userlist as $key => $user) {
        $options[$user['User']['id']] = $user['User']['username'];
    }
    echo $this->Form->input('user_id', array(
        'options' => $options,
        'empty' => 'Please Select...'
    ));
}
    $method = ($campaigns['Campaign']['method'] == 1)?1:0;
    echo $this->Form->input('method', array(
        'options' => array('Ajax Post', 'Form Post'),
        'selected' => $method,
        'empty' => 'Please Select...'
    ));
		echo $this->Form->input('note');
	?>
	</fieldset>
<fieldset id="buildyourform"><legend>Form Fields</legend>

<?php
    $rules = json_decode($campaigns['Campaign']['rules'],true); 
    $x = 1;
    foreach ($rules as $key1 => $value1) {
        echo '<div id="field'.$x.'" class="fieldwrapper"><h3 class="number">'.$x.'</h3>';
        echo '<div class="row first"><label>Field Name</label><input type="text" class="fieldname" name="data[Campaign][rules]['.$x.'][fieldname]" value="'.$key1.'" /></div>';

        $rule = @$value1[0]['rule'];

        echo required($rule,$x);
        echo fieldType($rule[0],$x);

        echo '<span id="row'.$x.'" class="properties">';
        if(is_bool(@$rule[1])) {
            echo fieldDuplicate($rule[0],$rule[1],$x);
        } elseif (strlen(@$rule[2])>=2) {
            echo fieldProp($rule[0],$rule[2],$x);
        }
        echo '</span>';

        echo '<input type="button" value="-" class="remove"></div>';
        $x++;
    }
    echo $this->Form->input('id', array('type' => 'hidden'));
?>


</fieldset>
<input type="button" value="Add a field" class="add" id="add" />

<?php
function fieldType($val,$x){
    $options = array(
        "text"          => "Text",
        "alphaNumeric"  => "Alpha Numeric",
        "blank"         => "Blank",
        "custom"        => "Custom",
        "email"         => "Email",
        "phone"         => "Phone",
        "postal"        => "Postal",
        "trackid"       => "Track ID"
    );

    $select ='<div class="row"><label>Field Type</label><select class="fieldtype" name="data[Campaign][rules]['.$x.'][fieldtype]" id="select'.$x.'">';

    foreach ($options as $value => $opt) {
        $select .='<option value="'.$value.'" ';

        if($value == $val) {
            $select .='selected="selected" ';            
        } elseif($value == ''){
            $select .='selected="selected" ';       
        } 

        $select .= ">$opt</option>";
    }
    $select .= "</select></div>";

    return $select;
}

function fieldProp($type,$val,$x) {
    $properties = '<div class="row"><label>'.ucfirst($type).' Property</label><select name="data[Campaign][rules]['.$x.'][fieldprop]" class="'.$type.'">';
    if($val == 'us') {
        $properties .= '<option value="us" selected="selected">United States</option><option value="others">Others</option>';
    } else {
        $properties .= '<option value="us">United States</option><option value="others" selected="selected">Others</option>';     
    }

    $properties .= '</select></div>';
    return $properties;
}

function fieldDuplicate($type,$val,$x) {
    $properties = '<div class="row"><label>No Duplicates?</label><input type="checkbox" value="emailDuplicate" name="data[Campaign][rules]['.$x.'][fieldprop]" ';
    if($val == 1) {
        $properties .= 'checked="yes" ';
    }
    $properties .= '></div>';
    return $properties;
}

function required($val,$x) {
    if(!empty($val)){
        $required = '<div class="row"><label>Required?</label><input class="required'.$x.'" type="checkbox" name="data[Campaign][rules]['.$x.'][required]" value="notEmpty" checked="checked" ></div>';
    } else {
        $required = '<div class="row"><label>Required?</label><input class="required'.$x.'" type="checkbox" name="data[Campaign][rules]['.$x.'][required]" value="notEmpty" ></div>';
    }
    return $required;
}


function inputType($val,$x){
    $email = '<div class="row"><label>No Duplicates?</label><input type="checkbox" name="data[Campaign][rules]['.$x.'][fieldprop]" value="emailDuplicate"></div>';
    $custom = '<div class="row"><label>Custom Rules<input type="text" name="data[Campaign][rules]['.$x.'][fieldprop]" value=""></label></div>';
    $phone = '<div class="row"><label>Phone Property</label><select class="phone" name="data[Campaign][rules]['.$x.'][fieldprop]"><option value="us">United States</option><option value="others">Others</option></select></div>';
    $postal = '<div class="row"><label>Postal Property</label><select class="postal" name="data[Campaign][rules]['.$x.'][fieldprop]"><option value="us">United States</option><option value="others">Others</option></select></div>';

    switch($val){
        case 'email':
            $properties = $email;
        break;
        case 'custom':
            $properties = $custom;
        break;
        case 'phone':
            $properties = $phone;
        break;
        case 'postal':
            $properties = $postal;
        break;
        default:
            $properties = '';
        break;
    }
    return $properties;   
}
?>

<script type="text/javascript">
$("#add").click(function() {
    var intId = $("#buildyourform h3").length + 1;
    var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
    var fName = $("<div class=\"row first\"><label>Field Name</label><input type=\"text\" class=\"fieldname\" name=\"data[Campaign][rules]["+intId+"][fieldname]\" /></div>");
    var fType = $("<div class=\"row\"><h3 class=\"number\">"+intId+"</h3><label>Required?</label><input class=\"required"+intId+"\" type=\"checkbox\" name=\"data[Campaign][rules]["+intId+"][required]\" value=\"notEmpty\"></div><div class=\"row\"><label>Field Type</label><select class=\"fieldtype\" name=\"data[Campaign][rules]["+intId+"][fieldtype]\" id=\"select" + intId + "\"><option value=\"alphaNumeric\">Alpha Numeric</option><option value=\"blank\">Blank</option><option value=\"custom\">Custom</option><option value=\"email\">Email</option><option value=\"text\" selected=\"selected\">Text</option><option value=\"phone\">Phone</option><option value=\"postal\">Postal</option><option value=\"trackid\">Track ID</option></select></div><span class=\"properties\" id=\"row" + intId + "\"></span>");
    var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" />");
    removeButton.click(function() {
        $(this).parent().remove();
    });
    fieldWrapper.append(fName);
    fieldWrapper.append(fType);
    fieldWrapper.append(removeButton);
    $("#buildyourform").append(fieldWrapper);
});

$('.remove').click(function() {
    $(this).parent().remove();
});

$('.fieldtype').live('change', function(){
    $('.required'+propid).removeAttr('disabled');
    $('.required'+propid).prop('checked', false); 

    var propid = $(this).attr('id');
    propid = propid.replace('select','');

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
        case 'trackid':
            properties = '';
        break;
    }
    $('#row'+propid).append(properties);
});

</script>



<?php echo $this->Form->end(__('Submit')); ?>

</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
        <li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?> </li>
    </ul>
	</ul>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>
