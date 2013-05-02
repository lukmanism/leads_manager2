<div class="logs index">
    <h2><?php echo __('Leads'); ?></h2>
<?
if(!isset($_GET['cid'])):
    echo '<h1>Select report(s) to load</h1><fieldset><form method="get" action="" name="loadreport">';
    foreach ($campaigns as $c_id => $c_name):
        echo '<label><input type="checkbox" class="campaign" value="'.$c_id.'" />'.$c_name.'</label>';
    endforeach;
    echo '<input type="hidden" class="cid" name="cid" value="" />';
    echo '<div><input type="submit" class="submit" value="Load Report(s)" /></div></form></fieldset>';
?>
    <script type="text/javascript">
    $('.submit').on('click', function() {
        var cid = '';
        $('.campaign').each(function(i,e) {
            if ($(e).is(':checked')) {
                var comma = cid.length===0?'':'.';
                cid += (comma+e.value);
            }
        });
        $('.cid').val(cid);
    });
    </script>
<?

else:
?>  
    <table cellpadding="0" cellspacing="0">
    <tr>
    <th><?php echo $this->Paginator->sort('id'); ?></th>
<?
    foreach ($cheader[0] as $value) {
        echo '<th>'.str_replace('_',' ',$value).'</th>';
    }
?>
    <th><?php echo $this->Paginator->sort('Campaign'); ?></th>
    <th><?php echo $this->Paginator->sort('Email'); ?></th>
    <th><?php echo $this->Paginator->sort('IP'); ?></th>
    <th><?php echo $this->Paginator->sort('Created'); ?></th>
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
        <td><?php echo h($lead['Lead']['email']); ?>&nbsp;</td>
        <td><?php echo h($lead['Lead']['ip']); ?>&nbsp;</td>
        <td><?php echo h($lead['Lead']['created']); ?>&nbsp;</td>
    </tr>
<?php endforeach; ?>
    </table>
    <p>
    <?php
    echo $this->Paginator->counter(array(
    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
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
    <!-- Leadout -->
    <div class="logout"><a href="<?=$this->Html->url('/users/logout', true);?>">Leadout</a></div>
</div>
