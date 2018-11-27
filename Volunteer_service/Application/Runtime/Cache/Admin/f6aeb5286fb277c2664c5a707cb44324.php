<?php if (!defined('THINK_PATH')) exit();?><div id="btn_toolbar" class="toolbar" data-module="<?php echo __CONTROLLER__; ?>">
	<div class="btn-group">
		<button type="button" data-name="add" class="btn btn-default" data-event-type="custom"
		data-event-value="" data-target="">
			<i class="icon-plus"></i>
			添加
		</button>
		<button type="button" data-name="edit" class="btn btn-default" data-event-type="view"
		data-event-value="" data-target="modal">
			<i class="icon-edit"></i>
			修改
		</button>
		<button type="button" data-name="delete" class="btn btn-default"
		data-event-type="default" data-event-value="" data-target="">
			<i class="icon-trash"></i>
			删除
		</button>
	</div>
	<div class="btn-group">
		<button type="button" data-name="sortToolbar" class="btn btn-default"
		data-event-type="custom" data-event-value="" data-target="modal">
			<i class="icon-file"></i>
			保存排序
		</button>
		<button type="button" data-name="autoCreate" class="btn btn-default" data-event-type="custom"
		data-event-value="" data-target="modal">
			<i class=""></i>
			自动生成
		</button>
	</div>
</div>
<table id="btn_gridview" data-toggle="gridview" data-height="210" class="table table-hover" data-pagination="false"
	data-toolbar="#btn_toolbar" data-method="post" data-url="<?php echo __ACTION__; ?>?menu=<?php echo ($menu_id); ?>" data-side-pagination="client">
	<thead>
		<tr>
			<th data-width="40" data-align="center" data-checkbox="true"></th>
			<th data-width="50" data-field="sort" data-formatter="format_sort">排序</th>
			<th data-field="title">按钮名称</th>
			<th data-field="name">按钮标识</th>
			<th data-field="visible" data-formatter="fomat_visible">是否显示</th>
			<th data-field="access" data-formatter="fomat_access">权限</th>
			<th data-field="event_type">事件类型</th>
			<th data-field="event_value">事件值</th>
			<th data-field="target">执行方式</th>
			<th data-field="icon" data-formatter="fomat_icon" data-align="center">图标</th>
			<th data-field="groups" data-align="center">分组</th>
			<th data-field="remark">说明</th>
		</tr>
	</thead>
</table>

<script type="text/javascript">
$btn_gridview = $('#btn_gridview').on('add', function(e, gridview){
	// 添加按钮
	var menu_id = $(this).data('data-menu');
	if(menu_id == null ){
		return alertMsg('请先勾选要编辑的菜单！');
	}
	gridview.loadModal('<?php echo __CONTROLLER__; ?>/add?menu_id='+menu_id);
}).on('autoCreate', function(){ // 自动生成按钮
	var menu_id = $(this).data('data-menu');
	if(menu_id == null ){
		return alertMsg('请先勾选要编辑的菜单！');
	}
	$.ajax({
		url: '<?php echo __CONTROLLER__; ?>/autoCreate?menu_id='+menu_id,
		dataType: 'json',
		type: 'post',
		success: function(){
			$btn_gridview.gridView('refresh');
		}
	});
});

function fomat_icon(icon, row, index){
	return '<i class="'+icon+'"></i>';
}
function fomat_access(val, row, index){
	val +='';
	switch(val){
		case '-1':
			return '禁止访问';
		case '1':
			return '无限制';
		default:
			return '系统默认';
	}
}
function fomat_visible(val, row, index){
	return val == 1 ? '显示' : '不显示';
}
</script>