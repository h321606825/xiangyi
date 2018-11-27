<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no"/>
    <title><?php echo C('WEBSITE_NAME');?></title>
    <?php if(!preg_match('/(MSIE 7.0)/', $_SERVER['HTTP_USER_AGENT'])){ echo '<link href="//cdn.bootcss.com/pace/1.0.2/themes/orange/pace-theme-flash.css" rel="stylesheet">'; echo '<script src="//cdn.bootcss.com/pace/1.0.2/pace.min.js"></script>'; } ?>
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="//cdn.bootcss.com/font-awesome/3.2.1/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/css/admin.css" />
    <script src="//cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
	<!-- 顶部 -->
	<div class="sys-header">
		<div class="inner">
			<div class="system-name"><?php echo session('user.shop_name'); ?></div>
			<div class="box_message">
				<ul class="top_tool">
					<li class="set_all"><a href="javascript:win.modal('/admin/index/modifyPwd')"><i class="icon-lock icon-white"></i> 修改密码</a></li>
					<li class="exit_soft"><a href="/admin/login/out"><i class="icon-off icon-white"></i> 退出</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="body">
		<div class="col-side">
	    	<?php \Common\Common\Auth::get()->showMenuHtml() ?>
    	</div>
	    <div class="col-main">
	    	<div class="navbar menu-group">
			    <ul class="nav">
	    		  <?php echo \Common\Common\Auth::get()->showMenuGroup() ?>
			    </ul>
			</div>
			<div class="content-container"><div id="toolbar" class="toolbar" data-module="/admin/user"><?php \Common\Common\Auth::get()->showTollbar('admin', 'user', 'index') ?><form class="form-horizontal">
		<input type="text" class="hidden" name="data[id]">
		<div class="control-group span6">
			<label class="control-label">姓名</label>
			<div class="controls">
				<input type="text" name="data[nick]" data-search="true" placeholder="最多8个字符" maxlength="8">
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">登陆账号</label>
			<div class="controls">
				<input type="text" name="data[username]" data-search="true" placeholder="最多16个字符" maxlength="16">
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">角色</label>
			<div class="controls">
				<select name="data[role]" data-search="true">
					<option value="">全部</option>
					<?php if(is_array($role_list)): foreach($role_list as $key=>$item): ?><option value="<?php echo ($item["id"]); ?>"><?php echo ($item["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</div>
		</div>
		<!-- <div class="control-group span6">
			<label class="control-label">店铺</label>
			<div class="controls">
				<select name="data[shop_id]" data-search="true">
					<option value="">全部</option>
					<?php if(is_array($shop)): foreach($shop as $key=>$item): ?><option value="<?php echo ($item["id"]); ?>"><?php echo ($item["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</div>
		</div> -->
	</form></div>

<!-- 表格 -->
<table id="table" data-toggle="gridview" class="table table-hover" data-url="<?php echo __CONTROLLER__; ?>" data-toolbar="#toolbar" data-side-pagination="client">
    <thead>
		<tr>
			<th data-width="40" data-align="center" data-checkbox="true"></th>
			<th data-width="200" data-field="shop_name">店铺</th>
			<th data-width="150" data-field="nick">姓名</th>
			<th data-width="150" data-field="username">登陆账号</th>
			<th data-width="100"  data-field="status" data-formatter="fomat_status">状态</th>
			<th data-field="role_name">角色</th>
		</tr>
	</thead>
</table>

<script type="text/javascript">
//格式化数据
function fomat_status(val, row, index){
	if(val == 1){
		return '启用';
	}else if(val == 0){
		return '禁用';
	}else{
		return '未知';
	}
}

$(function(){
	$('#table').on('role', function(e, gridview, params){
		if(gridview.currentRow == null){
			alertMsg('请先选择要授权的用户');
			return false;
		}
		params.data.id = gridview.currentRow.id;
	}).on('password', function(e, gridview, params){
		if(gridview.currentRow == null){
			alertMsg('请先选择要修改密码的用户');
			return false;
		}
		params.data.id = gridview.currentRow.id;
	});
});
</script>
</div>
	    </div>
	</div>
	<div class="footer">
		<div class="copyright">
			<div class="ft-copyright"></div>
		</div>
	</div>
	<div class="back-to-top">
	    <a href="javascript:;" class="js-back-to-top"><i class="icon-chevron-up"></i>返回顶部</a>
	</div>
	<script src="/js/common.js"></script>
	<script src="/js/gridview.js?time=20170106"></script>
	<script src="//cdn.bootcss.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
</body>
</html>