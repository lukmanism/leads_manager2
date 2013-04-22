<div class="leads index">
	<h2><?php echo __('Leads'); ?></h2>

<?
if(!isset($_POST['submitloadreport'])):

    echo '<h1>Select report(s) to load</h1><fieldset><form enctype="multipart/form-data" method="post" action="" name="loadreport">';
    foreach ($campaigns as $c_id => $c_name):
        echo '<label><input type="checkbox" name="campaign_id[]" value="'.$c_id.'" />'.$c_name.'</label>';
    endforeach;
    echo '<div><input type="submit" class="submit" name="submitloadreport" value="Load Report(s)" /></div></form></fieldset>';
else:
    echo $this->Html->css('jquery.dataTables');
    echo $this->Html->script('jquery.dataTables.min');

	foreach ($campaign_id as $key => $value) {
		@$c_id .= $value.',';
	}
?>
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    var asInitVals = new Array();

    window.oTable = $('table.reports').dataTable({
        "bProcessing": true,
        "sAjaxSource": 'leads/ajax?campaign_id=<?=substr($c_id,0,-1);?>',// goes to /leads_manager/incomings/ajax page
        "bAutoWidth": false
    });

    function table2csv(oTable, exportmode, tableElm) {
        // alert('asdasd');
        var csv = '';
        var headers = [];
        var rows = [];
 
        // Get header names
        $(tableElm+' thead').find('th').each(function() {
            var $th = $(this);
            var text = $th.text();
            var header = '"' + text + '"';
            // headers.push(header); // original code
            if(text != "") headers.push(header); // actually datatables seems to copy my original headers so there ist an amount of TH cells which are empty
        });
        csv += headers.join(',') + "\n";
 
        // get table data
        if (exportmode == "full") { // total data
            var total = oTable.fnSettings().fnRecordsTotal()
            for(i = 0; i < total; i++) {
                var row = oTable.fnGetData(i);
                row = strip_tags(row);
                rows.push(row);
            }
        } else { // visible rows only
            $(tableElm+' tbody tr:visible').each(function(index) {
                var row = oTable.fnGetData(this);
                row = strip_tags(row);
                rows.push(row);
            })
        }
        csv += rows.join("\n");
 
        // if a csv div is already open, delete it
        if($('.csv-data').length) $('.csv-data').remove();
        // open a div with a download link
        $('.csv_dl').append('<div class="csv-data"><form enctype="multipart/form-data" method="post" action="leads/csv"><textarea class="form" name="csv" style="display:none;">'+csv+'</textarea><ul><li><input type="submit" class="submit" value="Download as file" /></li><li><a href="#" class="cancel button" >Cancel</a></li></ul></form></div>');
 
    }
 
    function strip_tags(html) {
        var tmp = document.createElement("div");
        tmp.innerHTML = html;
        return tmp.textContent||tmp.innerText;
    }

    // export only what is visible right now (filters & paginationapplied)
    $('#export_visible').click(function(event) {
        event.preventDefault();
        table2csv(oTable, 'visible', 'table.reports');
    });
 
    // export all table data
    $('#export_all').click(function(event) {
        event.preventDefault();
        table2csv(oTable, 'full', 'table.reports');
    });
 
    // export all table data
    $('.cancel').live('click', function(event) {
        event.preventDefault();
        $('.csv_dl').html('');
    });

});
</script>





<?php 
    // echo '<h1>Reports: '.$reportname.'</h1>';
    echo '<table class="reports"><tr><thead>';
    if(isset($campaignview[0])) {
        foreach ($campaignview[0] as $leads): 
            echo '<th>'.$leads.'</th>'; 
        endforeach;         
    }
    echo '</thead></tr></table>'; 
    $submenus = '<div class="actions sub-menus">
			<h3>CSV Export</h3>
            <ul>
                <li><a href="#" id="export_visible">Visible Leads</a></li>
                <li><a href="#" id="export_all">All Leads</a></li>
            </ul>
            <div class="csv_dl"></div>
        </div>';
endif;     
?>
</div>



<?=@$submenus;?>
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
        <li><a href="<?=$this->Html->url('/admin/acl', true);?>">ACL</a></li>
    </ul>
    <? endif; ?>
    <!-- Logout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>

</div>
