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
			<div class="content-container"><style>
.gridview2{margin-top:10px;}
</style>
<div id="toolbar" class="toolbar" data-module="/admin/menu"><?php \Common\Common\Auth::get()->showTollbar('admin', 'menu', 'index') ?></div>

<!-- 表格 -->
<table id="table" data-toggle="gridview" class="table" data-url="<?php echo __CONTROLLER__; ?>" data-toolbar="#toolbar" data-show-columns="true" 
data-side-pagination="client" data-page-size="6" data-page-list="[6, 10, 25, 50, All]">
    <thead>
		<tr>
			<th data-width="40" data-checkbox="true"></th>
			<th data-width="50" data-field="sort" data-formatter="format_sort">排序</th>
			<th data-width="200" data-field="title">名称</th>
			<th data-width="80" data-field="gid" data-formatter="formatter_group">分组</th>
			<th data-width="70" data-field="level" data-visible="false">级别</th>
			<th data-width="100" data-field="status" data-formatter="fomat_status">状态</th>
			<th data-width="100" data-field="icon">样式</th>
			<th data-field="url">链接地址</th>
		</tr>
	</thead>
</table>

<!-- 按钮表格 -->
<div class="gridview2"></div>

<script type="text/javascript">
	var toolbarUrl = '<?php echo __MODULE__; ?>/toolbar?menu=', $menuTable, $btn_gridview;
	$(function() {
		$menuTable = $('#table')
				.on(
						'clickRow',
						function(e, row, $element) {
							if ($btn_gridview == null) {
								var $gridview2 = $('.gridview2');
								$gridview2.load(toolbarUrl + row.id,
										function() {
											win.init($gridview2);
											$btn_gridview = $('#btn_gridview')
													.gridView();

											$menuTable.gridView('resetView');
											$('#current_menu').val(row.id);
											$btn_gridview.data('data-menu',
													row.id);
										});
								return;
							} else if ($btn_gridview.data('data-menu') == row.id) {
								return;
							}

							$btn_gridview.data('data-menu', row.id);
							$btn_gridview.data('bootstrap.table').options.url = toolbarUrl
									+ row.id;
							$btn_gridview.bootstrapTable('refresh');
						}).on('deleted', function(e, ajaxData, status) {
					// 菜单删除移除按钮
					if (status != 'success') {
						return;
					}

					if ($btn_gridview != undefined) {
						var menu_id = $btn_gridview.data('data-menu');
						$.each(ajaxData.deletedRows, function(i, item) {
							if (menu_id == item.id) {
								$btn_gridview.bootstrapTable('load', {
									total : 0,
									rows : []
								});
								return false;
							}
						});
					}
				}).on('cache', function() {
					alertConfirm({
						content : '确定更新缓存吗？',
						ok : function() {
							$.ajax({
								url : '<?php echo __CONTROLLER__; ?>/cache',
								type : 'post',
								dataType : 'json'
							});
						}
					});
				}).on('saveSort', function(e, gridview, params) {
					var list = {};
					$('#table tbody .sort').each(function(i, input) {
						list[input.dataset.id] = input.value;
					});

					alertConfirm({
						content : '<h4>确定保存排序吗？</h4>数字越大越靠前',
						ok : function() {
							$.ajax({
								url : '<?php echo __CONTROLLER__; ?>/saveSort',
								data : {
									list : list
								},
								dataType : "json",
								type : 'post',
								error : function() {
									alertMsg('排序失败！');
								}
							});
						}
					});
				});
	});

	// 格式化数据
	function format_sort(sort, row, index) {
		return '<input type="text" class="sort" data-id="'+row.id+'" style="width:20px; margin:0;text-align:center;padding: 0 3px;" value="'+sort+'" />';
	}

	//格式化数据
	function fomat_status(status, row, index) {
		if (status == 1) {
			return '显示';
		} else if (status == 2) {
			return '隐藏';
		} else if (status == 0) {
			return '禁用';
		}
	}
	
	function formatter_group(val, row, index){
		// var group = {1:'功能列表', 2: '基础信息'};
		var group = {1:'功能列表'};
		return group[val] || '无';
	}
</script></div>
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