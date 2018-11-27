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
			<div class="content-container"><div id="toolbar" class="toolbar" data-module="/admin/studentuser"><?php \Common\Common\Auth::get()->showTollbar('admin', 'studentuser', 'index') ?><form class="form-horizontal">
		<input type="text" class="hidden" name="data[id]">
		<div class="control-group span6">
			<label class="control-label">姓名</label>
			<div class="controls">
				<input type="text" name="data[name]" data-search="true" placeholder="最多16个字符" maxlength="16">
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">学院</label>
			<div class="controls">
				<select name="data[academic_id]" data-search="true">
					<option value="">全部</option>
					<?php if(is_array($academic)): foreach($academic as $key=>$item): ?><option value="<?php echo ($item["id"]); ?>"><?php echo ($item["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">学号</label>
			<div class="controls">
				<input type="text" name="data[school_number]" data-search="true" placeholder="最多8个字符" maxlength="8">
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">性别</label>
			<div class="controls">
				<select name="data[sex]" data-search="true">
					<option value="">全部</option>
					<option value="1">男</option>
					<option value="2">女</option>
				</select>
			</div>
		</div>
		<div class="control-group span6">
			<label class="control-label">手机号</label>
			<div class="controls">
				<input type="text" name="data[mobile]" data-search="true" placeholder="最多16个字符" maxlength="16">
			</div>
		</div>
	</form></div>

<!-- 表格 -->
<table id="table" data-toggle="gridview" class="table table-hover" data-url="<?php echo __CONTROLLER__; ?>" data-toolbar="#toolbar" data-page-list="[1, 10, 25, 50, All]">
    <thead>
		<tr>
			<th data-width="40" data-align="center" data-checkbox="true"></th>
			<th data-width="150" data-field="school_number">学号</th>
			<th data-width="85" data-field="name">姓名</th>
			<th data-width="50" data-field="sex" data-formatter="fomat_status">性别</th>
			<th data-width="100"  data-field="mobile" >手机号</th>
			<th data-width="100"  data-field="a_name">学院</th>
			<th data-width="100" data-field="m_a_time">志愿总时长</th>
		</tr>
	</thead>
</table>
<script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/ueditor/ueditor.all.min.js"></script>
<div style="display:none" id="editor"></div>
<script type="text/javascript">
//格式化数据
function fomat_status(sex, row, index){
	if(sex == 1){
		return '男';
	}else if(sex == 2){
		return '女';
	}else{
		return '未知';
	}
}
$('#table').on("export", function(e, gridview ,params){
	var limit = $('.page-size').html()
	var offset = $('.pagination .active a').html()
    var url = '<?php echo __CONTROLLER__; ?>/export';
    var array = $('#toolbar form').serializeArray();
    console.log(array);
    for(var i=0; i<array.length; i++){
        url += (i == 0 ? '?' : '&') + array[i].name + '=' + array[i].value;
    }
    offset = limit*(offset-1)
    url+='&offset='+offset+'&limit='+limit
    window.open(url,'_self');
    return false;
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