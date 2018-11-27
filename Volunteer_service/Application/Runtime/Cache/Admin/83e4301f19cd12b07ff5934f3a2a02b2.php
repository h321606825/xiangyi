<?php if (!defined('THINK_PATH')) exit();?><form id="editModal" method="post" action="<?php echo __ACTION__; ?>" data-validate="true" data-submit="ajax" class="form-horizontal">
	<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3><?php echo ($data['id'] ? '修改' : '添加'); ?>菜单</h3>
	  </div>
	  <div class="modal-body">
	  	<input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
		<div class="control-group">
			<label class="control-label">菜单分组</label>
			<div class="controls">
				<select name="gid" id="menu_group">
					<option value="0">无</option>
					<?php $menuGroups = \Common\Common\Auth::get()->getMenuGroups(); foreach($menuGroups as $item){ echo '<option value="'.$item['id'].'"'.($data['gid']==$item['id']?' selected="selected"':'').'>'.$item['title'].'</option>'; } ?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label must">父级菜单</label>
			<div class="controls">
				<select name="pid" id="plist" class="required" data-selected="<?php echo ($data['pid']); ?>">
					<option value="0">一级菜单</option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label must">菜单名称</label>
			<div class="controls">
				<input type="text" name="title" value="<?php echo ($data["title"]); ?>" placeholder="最多8个字符" maxlength="8" class="required" style="width:120px">
				<select name="status" style="width:80px">
					<option value="1" <?php echo ($data['status'] == 1 ? 'selected' : ''); ?>>显示</option>
					<option value="2" <?php echo ($data['status'] == 2 ? 'selected' : ''); ?>>隐藏</option>
					<option value="0" <?php echo ($data['status'] == 0 ? 'selected' : ''); ?>>禁用</option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">模块</label>
			<div class="controls">
				<input type="text" value="<?php echo ($data["module"]); ?>" name="module" placeholder="module" style="width:50px;" />
				<input type="text" value="<?php echo ($data["controller"]); ?>" name="controller" placeholder="controller" style="width:60px;" />
				<input type="text" value="<?php echo ($data["action"]); ?>" name="action" placeholder="action" style="width:60px;" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">参数</label>
			<div class="controls">
				<input type="text" name="params" value="<?php echo ($data["params"]); ?>" placeholder="?id=123&page=1" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">图标</label>
			<div class="controls">
				<input type="text" name="icon" value="<?php echo ($data["icon"]); ?>" placeholder="样式名称" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">排序</label>
			<div class="controls">
				<input type="text" name="sort" value="<?php echo ((isset($data["sort"]) && ($data["sort"] !== ""))?($data["sort"]):'0'); ?>" class="digits" placeholder="数字越大越靠前" />
			</div>
		</div>
		<?php if(empty($data['id'])): ?><div class="control-group">
			<label class="control-label">自动生成</label>
			<div class="controls">
				<label class="checkbox"><input type="checkbox" name="create_btn" value="1">按钮</label>
			</div>
		</div><?php endif; ?>
	  </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	  	<button type="submit" class="btn btn-primary" aria-hidden="true">保存</button>
	  </div>
  </div>
</form>
<script type="text/javascript">
(function(){
	var list = <?php echo json_encode($list);?>,
		$plist = $('#plist');
	$('#menu_group').on('change', function(){
		var options = '<option value="0">一级菜单</option>',
		    gid = parseInt(this.value),
		    selected = $plist.data('selected');
		for(var i=0; i<list.length; i++){
			if(list[i].gid != gid){
				continue;
			}
			options += '<option value="'+list[i].id+'"'+(list[i].id == selected ? ' selected' : '')+'>'+ list[i].split + list[i].title+'</option>';
		}
		$plist.html(options);
		return false;
	}).trigger('change');
})();
</script>