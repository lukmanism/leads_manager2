<?
    echo $this->Html->script('jquery.TableCSVExport');
?>
<div class="logs index">
    <h2><?php echo __('Leads'); ?></h2>
<?
if(!isset($_GET['cid'])): # Select Report
    echo '<h1>Select report(s) to load</h1><fieldset><form method="get" action="" name="loadreport">';
    foreach ($campaigns as $c_id => $c_name):
        echo '<label><input type="checkbox" class="select_campaign" value="'.$c_id.'" />'.$c_name.'</label>';
    endforeach;
    echo '<input type="hidden" class="ccid" name="cid" value="" />';
    echo '<div><input type="submit" class="csubmit" value="Load Report(s)" /></div></form></fieldset>';

else: # Report Results
?>

    <table cellpadding="0" cellspacing="0" id="leadsreport">
    <tr>
    <th class="form_block">
        <?php echo '<span>'.$this->Paginator->sort('id').'</span>'.search_form('id', ''); ?>
    </th>
<?
    foreach ($cheader[0] as $value) {
        echo '<th class="form_block"><span>'.str_replace('_',' ',$value).'</span>'.search_form($value, '', 'lead').'</th>';
    }
?>
    <th class="form_block">
        <?php echo '<span>'.$this->Paginator->sort('Campaign').'</span>'.search_form('campaign', $campaigns); ?>
    </th>
    <th class="form_block">
        <?php echo '<span>'.$this->Paginator->sort('IP').'</span>'.search_form('ip', ''); ?>
    </th>
    <th class="form_block">
        <?php echo '<span>'.$this->Paginator->sort('Page Src').'</span>'.search_form('source', ''); ?>
    </th>
    <th class="form_block">
        <?php echo '<span>'.$this->Paginator->sort('Created').'</span>'.search_form('created', ''); ?>
    </th>
    </tr>
    <?php foreach ($leads as $lead): ?>
    <tr>
        <td><?php echo h($lead['Lead']['id']); ?>&nbsp;</td>
<?
    $leads = json_decode($lead['Lead']['lead']);
    foreach (@$leads as $leadkey => $leadval) {
        echo "<td>$leadval</td>";
    }
?>
        <td>
            <?php echo $this->Html->link($lead['Campaign']['name'], array('controller' => 'campaigns', 'action' => 'view', $lead['Campaign']['id'])); ?>
        </td>
        <td><?php echo h($lead['Lead']['ip']); ?>&nbsp;</td>
        <td><?php echo h($lead['Lead']['source']); ?>&nbsp;</td>
        <td><?php echo h($lead['Lead']['created']); ?>&nbsp;</td>
    </tr>
<?php endforeach; ?>
    </table>
    <p>
    <?php
    echo $this->Paginator->counter(array(
    'format' => __('Page {:page}/{:pages}, Records {:current}/{:count}')
    ));
    ?>  </p>
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
        <?
        if(isset($_GET['cid'])){
        ?>        
        <li><a href="#" class="csv_export">Export CSV</a></li>
        <ul class="hide">
            <li>
                <form method="post" action="leads/csv/" name="export_this" class="export_this">
                    <input type="hidden" class="csv" name="csv" value="" />
                    <input type="submit" class="csv_this" value="- This Page" />
                </form>
            </li>
            <!-- <li>
                <form method="post" action="leads/csv/" name="export_all" class="export_all">
                    <input type="hidden" class="csvcid" name="csvcid" value="<?=$_GET['cid']?>" />
                    <input type="hidden" class="csvheader" name="csvheader" value="" />
                    <input type="submit" class="csv_entire" value="- Entire Results" />
                </form>
            </li> -->
        </ul>
        <? 
        }
        if(isset($_GET['mod'])){
            echo '<li>';
            echo $this->Html->link(__('Reset Leads Filter'), array('controller' => 'leads', 'action' => '?cid='.$_GET['cid']));
            echo '</li>';
        }
        ?>
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
    <!-- Leadout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Logout</a></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.form_hover').hide();

        $('.form_block').live({
            mouseenter: function(){
                $('.form_hover', this).show();            
            },
            mouseleave: function(){
                $('.form_hover', this).hide();                
            }            
        });

        $('.loadsearchlead').submit( function() {
                var slead = $('.slead', this).val();
                var sfield = $('.sfield', this).val();
                var lead = '['+sfield+']['+slead+']';
                $('.lead', this).val(lead);
                // console.log(lead);
            }
        );

        $('.csubmit').on('click', function() {
            var cid = '';
            $('.select_campaign').each(function(i,e) {
                if ($(e).is(':checked')) {
                    var comma = cid.length===0?'':'.';
                    cid += (comma+e.value);
                }
            });
            $('.ccid').val(cid);
        });

        $('a.csv_export').live({
            click: function(){
                var data = jQuery('#leadsreport').TableCSVExport();
                var header = data.split("\n");
                $('.export_this .csv').val(data);
                $('.export_all .csvheader').val(header[0]);
                $('ul.hide').show();
                return false;
            }
        });

    });
</script>

<?
    function search_form($sfield, $svalue, $type = NULL) {
        $scid = $_GET['cid'];
        $form = '<div class="form_hover"><form name="loadsearch'.$type.'" class="loadsearch'.$type.'" action="" method="get">';

        if(isset($svalue) && is_array($svalue)) { # checkbox campaign
            foreach ($svalue as $c_id => $c_name){
                $checked = (isset($_GET['cid']) && $_GET['cid'] == $c_id)? 'checked' : '';
                $form .= '<label><input type="checkbox" class="select_campaign" value="'.$c_id.'" '.$checked.'/>'.$c_name.'</label>';
            }
            $form .= '<input type="hidden" class="ccid" name="cid" value="" /><input type="submit" class="csubmit" value="Load Report(s)" />';
        } elseif(isset($type)) { # text input for lead fields
            $form .= '<input type="text" value="" placeholder="Search" class="search s'.$type.'">';
            $form .= '<input type="hidden" class="sfield" value="'.$sfield.'" />';
            $form .= '<input type="hidden" class="lead" name="lead" value="'.$sfield.'" />';
            $form .= '<input type="hidden" class="cid" name="cid" value="'.$scid.'" />';
            $form .= '<input type="submit" class="ssubmit" value="Load Query" />';
        } else { # text input
            $form .= '<input type="text" value="" placeholder="Search" class="search" name="'.$sfield.'">';
            $form .= '<input type="hidden" class="cid" name="cid" value="'.$scid.'" />';
            $form .= '<input type="submit" class="submit" value="Load Query" />';
        }

        $form .= '<input type="hidden" class="" name="mod" value="q" /></form></div>';
        return $form;
    }

?>