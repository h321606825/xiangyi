<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	<title>登录</title>
	<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="/css/admin.login.css">
	<script src="//cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/jquery-validate/1.15.0/jquery.validate.min.js"></script>
</head>
<body>
	<div class="container">
	    <div class="header" style="text-align: center; font-size:24px; color:#489EFF;">
	    	相益志愿者服务系统
	    </div>
	    <div id="error_msg">
	    	<?php if(!empty($error)): ?><div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> <?php echo ($error); ?></div><?php endif; ?>
	    </div>
	    <div class="main main-login clearfix">
	        <form id="login_form" action="<?php echo __ACTION__; ?>?redirect=<?php echo ($_GET['redirect']); ?>" class="form-horizontal">
	                <div class="control-group">
	                    <label class="control-label">登录账号：</label>
	                    <div class="controls">
	                        <input tabindex="1" type="text" name="username" required="required" data-msg-required="请输入登录账号" autofocus="autofocus">
	                    </div>
	                </div>
	                <div class="control-group">
	                    <label class="control-label">登录密码：</label>
	                    <div class="controls">
	                        <input tabindex="2" type="password" name="password" required="required" data-msg-required="请输入登录密码">
	                    </div>
	                </div>
	                
	                <!-- <div class="control-group">
	                    <div class="controls">
	                        <label class="auto-login">
	                            <input type="checkbox" name="auto_login" tabindex="3">
	                            三天内自动登录
	                        </label>
	                        <a class="lost-pw" href="javascript:;" target="_blank" tabindex="-1">忘记密码?</a>
	                    </div>
	                </div> -->
	                <div class="control-group">
	                    <div class="controls">
	                        <button type="submit" tabindex="4" class="btn btn-large btn-primary login-btn" data-loading-text="正在登录...">登录</button>
	                    </div>
	                </div>
	        </form>
	        <div class="side-wrap">
	            <div class="login-download-wsc-icon">
	            	<img src="/img/logo.jpg" alt="">
	            </div>
	            <h4 class="side-desc">相益志愿者</h4>
	        </div>
	    </div>
	</div>
	
	<script type="text/javascript">
	var $login_form = $('#login_form');
	var $submit = $login_form.find('.btn-primary');
	$login_form.validate({
        errorClass: 'help-block',
        errorElement: "span",
        highlight: function (element, errorClass, validClass) {
        	$(element).parents('.control-group:first').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
        	$(element).parents('.control-group:first').removeClass('error');
        },
        submitHandler: function (form) {
        	$submit.attr('disabled', 'disabled').html('正在登录...');
        	var data = $login_form.serialize();
        	var url = $login_form.attr("action");
        	$.ajax({
        		url: url,
        		type: 'post',
        		data: data,
        		dataType: 'json',
        		success: function(data){
        			if(data.status == 1){
        				// 登陆成功 页面跳转
						show_error('登录成功！');
						window.location.href = data.url;
					}else if(data.status == 0){ 
						// 登陆失败
						show_error(data.info);
						$submit.removeAttr('disabled').html('登录');
					}
        		},
        		error: function(){
        			$submit.removeAttr('disabled').html('登录');
        			show_error('系统繁忙，请稍后再试！');
        		}
        	});
        	return false;
        }
	});
	
	function show_error(msg){
		$('#error_msg').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong></strong> '+msg+'</div>');
	}
	</script>
	<script src="//cdn.bootcss.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
</body>
</html>