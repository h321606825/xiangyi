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
			<div class="content-container"><div id="toolbar" class="toolbar" data-module="/admin/role"><?php \Common\Common\Auth::get()->showTollbar('admin', 'role', 'index') ?></div>
<!-- 表格 -->
<table id="table" data-toggle="gridview" class="table table-hover" data-url="<?php echo __CONTROLLER__; ?>" data-toolbar="#toolbar" data-edit="true">
    <thead>
		<tr>
			<th data-width="40" data-align="center" data-checkbox="true"></th>
			<th data-width="200" data-field="name">角色名称</th>
			<th data-width="100" data-field="status" data-formatter="fomat_status">状态</th>
			<th data-width="200" data-field="remark">备注</th>
		</tr>
	</thead>
</table>

<script type="text/javascript">
//格式化数据
function fomat_status(status, row, index){
	if(status == 1){
		return '启用';
	}else if(status == 0){
		return '禁用';
	}
}

$(function(){
	$('#table').on('access_menu', function(e, gridview, params){
		if(gridview.currentRow == null){
			alertMsg('请先选择要授权的角色');
			return false;
		}

		params.data.id = gridview.currentRow.id;
	}).on('access_store', function(e, gridview, params){
		if(gridview.currentRow == null){
			alertMsg('请先选择要授权的角色');
			return false;
		}
		params.data.role_id = gridview.currentRow.id;
	}).on('delete', function(e, gridview, params){
		params.title = '风险提示';
		params.message = '<h5>已使用的角色建议您禁用掉！</h5>' + params.message;
	});
	
	win.getScript('//cdn.bootcss.com/jstree/3.3.1/jstree.min.js');
	win.getStyle('//cdn.bootcss.com/jstree/3.3.1/themes/default/style.min.css');
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