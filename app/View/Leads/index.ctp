<?
    echo $this->Html->css('resources/css/ext-all');
    echo $this->Html->script('ext-all');
    // echo $this->Html->script('ext');
    echo $this->Html->script('src/ux/exporter/downloadify.min');
    echo $this->Html->script('src/ux/exporter/swfobject');
?>
<script type="text/javascript">
    Ext.Loader.setConfig({enabled: true});
    Ext.require([
        'Ext.grid.*',
        'Ext.data.*',
        'Ext.ux.grid.FiltersFeature',
        'Ext.toolbar.Paging',
        'Ext.ux.ajax.JsonSimlet'
    ]);

    Ext.define('Product', { extend: 'Ext.data.Model', 
        uses: [
        'Ext.ux.exporter.Exporter'
    ],
        fields: [<?
        foreach ($schema as $key => $value) {
            echo "{ name: '$key', type: '$value[type]' },";
        } ?>]
    });

    Ext.onReady(function(){
        Ext.QuickTips.init();
        var url = { remote: 'leads/ajax?model=leads' }; // loadmodel
        var store = Ext.create('Ext.data.JsonStore', { autoDestroy: true, model: 'Product', proxy: { type: 'ajax', url: (url.remote), reader: { type: 'json', root: 'data', idProperty: 'id', totalProperty: 'total'}}, remoteSort: false, sorters: [{ property: 'id', direction: 'DESC'}], pageSize: 20 });
        var filters = { ftype: 'filters', encode: false, local: false, filters: [{ type: 'numeric', dataIndex: 'id'}] };
        var createColumns = function (finish, start) {
            var columns = [<?
            foreach ($schema as $key => $value) {
                echo "{ dataIndex: '$key', text: '".ucfirst($key)."', filterable: true, width: 50},";
            }
            ?>];
            return columns.slice(start || 0, finish);
        };   

        var grid = Ext.create('Ext.grid.Panel', { border: false, store: store, columns: createColumns(100), loadMask: true, features: [filters], dockedItems: [Ext.create('Ext.toolbar.Paging', { dock: 'bottom', store: store, displayInfo: true, displayMsg: '{0} - {1} of <b>{2}</b> records' })], emptyText: 'No Matching Records', renderTo: 'renders', height: 600, width: '100%', layout: 'fit', items: grid});
        grid.child('pagingtoolbar').add(['->', 
            { text: 'Clear Filter Data', handler: function () { grid.filters.clearFilters();}}
            ]);
        store.load();
    });
</script>
<div class="logs index">
    <h2><?php echo __('Leads'); ?></h2>
    <div id="renders"></div>

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
