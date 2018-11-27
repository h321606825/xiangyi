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
			<div class="content-container"><div id="toolbar" class="toolbar" data-module="/admin/shenhestudent"><?php \Common\Common\Auth::get()->showTollbar('admin', 'shenhestudent', 'index') ?></div>
<!-- 表格 -->
<style>
.btn_wish {
    padding: 7px 20px;
    background: #23659D;
    border-radius: 5px;
    color: #fff;
    text-align: center;
    cursor: pointer;
    width:35px;
}
.btn_style {
    padding:8px 15px;
    background: #23659D;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
.btn_wish_gray {
    background: #C6C6C6;
}

.btn_wish_red {
    background: red;
}

.popups {
    display: none;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 3px;
    position: fixed;
    top: 50%;
    left: 50%;
    z-index: 100;
    margin-left: -150px;
    margin-top:-125px;
    padding: 20px;
}

.title_wish {
    font-weight: bold;
    font-size: 14px;
    padding-bottom: 20px;
}

.clearfixed:after {
    clear: both;
    content: ".";
    display: block;
    height: 0;
    line-height: 0;
    visibility: hidden;
    zoom: 1;
}

.clearfixed>.li {
    display: block;
    float: left;
}
.btn_cancel {
    margin-right: 20px;
    margin-left: 50px;
}
.default {
    height: 25px!important;
    margin:0!important;
    margin-right: 10px!important;
    width:150px;
}
.stu {
    padding:20px 0;
    font-size:14px;
}
.openclose {
    padding-top:20px;
}
.s_name {
    width:65px;
}
.s_con {
    max-width:160px;
}
.conwidth {
    max-width:200px;
}
</style>
<table id="table" data-toggle="gridview" class="table table-hover" data-url="<?php echo __CONTROLLER__; ?>" data-toolbar="#toolbar" data-page-list="[1, 10, 25, 50, All]">
    <thead>
        <tr>
            <th data-width="40" data-align="center" data-checkbox="true"></th>
            <th data-width="50" data-field="level">心愿类型</th>
            <th data-width="200" data-field="content" data-formatter="fomat_content">心愿内容</th>
            <th data-width="100" data-field="end_time">心愿截止时间</th>
            <th data-width="50" data-field="t_name">发布人</th>
            <th data-width="100" data-field="t_mobile">发布人联系方式</th>
            <th data-width="100" data-field="status" data-formatter="fomat_status">待认领状态</th>
            <th data-width="100" data-field="tpye" data-formatter="fomat_tpye">审核</th>
        </tr>
    </thead>
</table>
<div class="popups">
    <div class="title_wish">确认通过</div>
    <div class="stu">
        
    </div>
    <div class="clearfixed openclose">
        <div class="li">
            <div class="detdrmine btn_style" onclick="wish_clo()">取消</div>
        </div>
        <div class="li">
            <div class="btn_cancel btn_style" onclick="wish_close()">不通过</div>
        </div>
        <div class="li">
            <div class="detdrmine btn_style" onclick="wish_determine()">确定通过</div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/ueditor/ueditor.all.min.js"></script>
<div style="display:none" id="editor"></div>
<script type="text/javascript">
//格式化数据
function fomat_status(status, row, index) {
    if (status == 1) {
        return '<div style="color:#23659D;">未认领</div>';
    } else {
        return '<div style="color:#777777;">已认领</div>';
    }
}
function fomat_content(con, row, index) {
    return '<div class="conwidth">'+con+'</div>'
}
function fomat_tpye(tpye, row, index) {
    //console.log(tpye);
    if (tpye == 2) {
        return '<div class="btn_wish btn_wish_gray">审核</div>';
    }  else if (tpye == 1) {
        return '<div class="btn_wish" onclick="fenpei(this)">审核</div>';
    }
}
$('#table').on("export", function(e, gridview, params) {
    var limit = $('.page-size').html()
    var offset = $('.pagination .active a').html()
    var url = '<?php echo __CONTROLLER__; ?>/export';
    var array = $('#toolbar form').serializeArray();
    offset = limit * (offset - 1)
    url += '?offset=' + offset + '&limit=' + limit
    window.open(url, '_self');
    return false;
});

function fenpei(e){
    var type = $(e).attr('data-type')
    
    var id = $(e).parents('tr').attr('data-uniqueid')
    $('.popups').attr('id',id)
    $('.popups').show()
    
    
}
function wish_clo(){
    $('.popups').hide();
}
function wish_close(){
    //
     var id = $('.popups').attr('id');
    $.ajax({
            url:'/admin/shenhestudent/shenhe',
            type:'post',
            data:{
                id:id,
                flag:2
            },
            success:function(res){
                alertMsg('成功审核')
                wish_clo()
            }
        })
}
// function search_student(){
//     var val = $('.search_student').val()
//     $.ajax({
//         url:'/admin/wish/search_student',
//         type:'post',
//         data:{
//             val:val
//         },
//         success:function(res){
//             if (res.name != undefined) {
//                 var name = res.name 
//                 var id = res.id 
//                 var mobile = res.mobile
//                 var html = ''
//                 html +='<div class="clearfixed">';
//                 html +='<div class="li s_name">学生姓名:</div>';
//                 html +='<div class="li s_con studen_name">'+name+'</div>';
//                 html +='</div>';
//                 html +='<div class="clearfixed">';
//                 html +='<div class="li s_name">学生电话:</div>';
//                 html +='<div class="li s_con studen_phone">'+mobile+'</div>';
//                 html +='</div>'
//                 $('.stu').html(html)
//                 $('.stu').attr('s_id',id)
//             }else{
//                 alertMsg("查无此学生，请核对后再搜索");
//             }
//         }
//     })
// }
function wish_determine(){
    //var s_id = $('.stu').attr('s_id')
    var id = $('.popups').attr('id')
    //console.log(id);
    if(id){
        $.ajax({
            url:'/admin/shenhestudent/shenhe',
            type:'post',
            data:{
                id:id,
                flag:1
            },
            success:function(res){
                alertMsg('审核成功')
                wish_clo()
            }
        })
    }else{
        alertMsg('审核失败')
    }
    
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