<?
    echo $this->Html->css('resources/css/ext-all');
    echo $this->Html->script('ext-all-dev');
?>
<script type="text/javascript">
    Ext.Loader.setConfig({enabled: true});
    Ext.require([
        'Ext.grid.*',
        'Ext.data.*',
        'Ext.ux.grid.FiltersFeature',
        'Ext.toolbar.Paging',
        'Ext.ux.ajax.JsonSimlet',
        'Ext.ux.ajax.SimManager'
    ]);

    Ext.define('Product', { extend: 'Ext.data.Model', fields: [<?
        foreach ($schema as $key => $value) {
            echo "{ name: '$key', type: '$value[type]' },";
        } ?>]
    });

    Ext.onReady(function(){
        Ext.ux.ajax.SimManager.init({ delay: 300, defaultSimlet: null }).register({ 'myData': { data: [ ['small', 'small'], ['medium', 'medium'], ['large', 'large'], ['extra large', 'extra large']], stype: 'json' } });
        var optionsStore = Ext.create('Ext.data.Store', { fields: ['id', 'text'], proxy: { type: 'ajax', url: 'myData', reader: 'array' } });
        Ext.QuickTips.init();
        var url = { remote: 'campaigns/ajax?model=campaigns' }; // loadmodel
        var encode = false;
        var local = false;
        var store = Ext.create('Ext.data.JsonStore', {  autoDestroy: true, model: 'Product', proxy: { type: 'ajax', url: (url.remote), reader: { type: 'json', root: 'data', idProperty: 'id', totalProperty: 'total'}}, remoteSort: false, sorters: [{ property: 'id', direction: 'DESC'}], pageSize: 50 });
        var filters = { ftype: 'filters', encode: encode, local: local, filters: [{ type: 'numeric', dataIndex: 'id'}] };
        var createColumns = function (finish, start) {
            var columns = [<?
            foreach ($schema as $key => $value) {
                echo "{ dataIndex: '$key', text: '".ucfirst($key)."', filterable: true},";
            }
            ?>];
            return columns.slice(start || 0, finish);
        };        
        var grid = Ext.create('Ext.grid.Panel', { border: false, store: store, columns: createColumns(50), loadMask: true, features: [filters], dockedItems: [Ext.create('Ext.toolbar.Paging', { dock: 'bottom', store: store })], emptyText: 'No Matching Records', renderTo: 'renders'});
        grid.child('pagingtoolbar').add(['->', { text: 'Clear Filter Data', handler: function () { grid.filters.clearFilters();}}]);
        store.load();
    });
</script>
<div class="campaigns index">
	<h2><?php echo __('Campaigns'); ?></h2>
	<div id="renders"></div>



</div>
<div class="actions">

	<h3>Campaign Edit</h3>
	<ul>
		<li><?php echo $this->Html->link(__('New'), array('action' => 'add')); ?></li>
	</ul>

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


