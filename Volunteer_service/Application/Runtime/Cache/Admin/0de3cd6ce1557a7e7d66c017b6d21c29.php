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
			<div class="content-container"><form method="post" action="<?php echo __ACTION__; ?>" enctype="multipart/form-data" data-validate="true" data-submit="ajax" class="form-horizontal edit-form">
  <div class="form-group">
  	  <div class="form-title">微信公众平台</div>
	  <div class="form-item">
		  <div class="control-group">
		    <label class="control-label must">应用ID</label>
		    <div class="controls">
		      <input class="required" type="text" name="appid" maxlength="25" value="<?php echo ($data["appid"]); ?>" placeholder="最多25个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label must">应用密钥</label>
		    <div class="controls">
		      <input class="required" type="text" name="secret" maxlength="32" value="<?php echo ($data["secret"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label must">令牌</label>
		    <div class="controls">
		      <input class="required" type="text" name="token" maxlength="32" value="<?php echo ($data["token"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label">消息加解密密钥</label>
		    <div class="controls">
		      <input type="text" name="encoding_aes_key" maxlength="64" value="<?php echo ($data["encoding_aes_key"]); ?>" placeholder="最多64个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label must">原始id</label>
		    <div class="controls">
		      <input class="required" type="text" name="original_id" maxlength="16" value="<?php echo ($data["original_id"]); ?>" placeholder="最多16个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label">微信名称</label>
		    <div class="controls">
		      <input class="required" type="text" name="name" maxlength="32" value="<?php echo ($data["name"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
	  </div>
	  <div class="form-item">
		  <div class="control-group">
		  	<label class="control-label">关注二维码</label>
		  	<div class="controls">
				<input class="hide" type="text" name="qrcode" value="<?php echo ($data["qrcode"]); ?>" readonly="readonly">
				<img id="qrcode" src="<?php echo ($data["qrcode"]); ?>" class="img-polaroid btn-up" alt="微信关注二维码" style="width: 120px; height: 120px;">
			</div>
		  </div>
		  <div class="control-group">
		    <label class="control-label">微信号</label>
		    <div class="controls">
		      <input type="text" name="wxid" maxlength="32" value="<?php echo ($data["wxid"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label">登录邮箱</label>
		    <div class="controls">
		      <input type="text" name="login_email" maxlength="64" value="<?php echo ($data["login_email"]); ?>" placeholder="最多64个字符">
		    </div>
		  </div>
		  <?php if(empty($data['login_pwd'])): ?><div class="control-group">
			    <label class="control-label">登录密码</label>
			    <div class="controls">
			      <input type="password" name="login_pwd" maxlength="32" value="" placeholder="最多32个字符">
			    </div>
			  </div>
		  <?php else: ?>
			  <div class="control-group">
			    <label class="control-label">登录密码</label>
			    <div class="controls">
				    <label style="padding-top:5px;">
				    <a id="edit_login_pwd">编辑</a>
				    </label>
			    </div>
			    <div class="controls hide">
			      <input type="password" name="edit_login_pwd" maxlength="32" value="" placeholder="最多32个字符">
			    </div>
			  </div><?php endif; ?>
	  </div>
	</div>
  <div class="form-group">
  	  <div class="form-title">微信商户平台</div>
	  <div class="form-item">
		  <div class="control-group">
		    <label class="control-label must">商户号</label>
		    <div class="controls">
		      <input type="text" name="mch_id" maxlength="32" value="<?php echo ($data["mch_id"]); ?>" placeholder="最多32个字符" required="required">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label">子商户号</label>
		    <div class="controls">
		      <input type="text" name="sub_mch_id" maxlength="32" value="<?php echo ($data["sub_mch_id"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
		  <div class="control-group">
		    <label class="control-label must">API密钥</label>
		    <div class="controls">
		      <input type="text" name="mch_key" maxlength="32" value="<?php echo ($data["mch_key"]); ?>" placeholder="最多32个字符" required="required">
		    </div>
		  </div>
	  </div>
	  <div class="form-item">
		  <div class="control-group">
		    <label class="control-label must">商户名称</label>
		    <div class="controls">
		      <input type="text" name="mch_name" maxlength="32" value="<?php echo ($data["mch_name"]); ?>" placeholder="最多32个字符" required="required">
		    </div>
		  </div>
		   <div class="control-group">
		    <label class="control-label">登录账号</label>
		    <div class="controls">
		      <input type="text" name="mchaccess" maxlength="32" value="<?php echo ($data["mchaccess"]); ?>" placeholder="最多32个字符">
		    </div>
		  </div>
		  <?php if(empty($data['mchpwd'])): ?><div class="control-group">
			    <label class="control-label">登录密码</label>
			    <div class="controls">
			      <input type="password" name="mchpwd" maxlength="32" value="" placeholder="最多32个字符">
			    </div>
			  </div>
		  <?php else: ?>
		  	  <div class="control-group">
			    <label class="control-label">登录密码</label>
			    <div class="controls">
				    <label style="padding-top:5px;">
				    	<a id="edit_mchpwd">编辑</a>
				    </label>
			    </div>
			    <div class="controls hide">
			      <input type="password" name="edit_mchpwd" maxlength="32" value="" placeholder="最多32个字符">
			    </div>
			  </div><?php endif; ?>	  
	  </div>
	</div>
	
	<div class="form-actions">
	  <button type="submit" class="btn btn-primary">保存</button>
	  <button type="button" class="btn">取消</button>
	</div>
</form>
<script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/ueditor/ueditor.all.min.js"></script>
<div style="display:none" id="editor"></div>
<script>
$(function(){
	var editor = UE.getEditor('editor',{isShow: false})
	$('#qrcode').on('click', function(){
		var $img = $(this);
		var $input_url = $img.prev();
		editor.removeListener('beforeInsertImage');
		editor.addListener('beforeInsertImage', function (t, list) {
			$input_url.val(list[0]['src']);
			$img.attr('src', list[0]['src']);
        });
		
		editor.getDialog("insertimage").open();
	});
	
	//编辑微信登录密码
	$('#edit_login_pwd').on('click',function(){
		$('#edit_login_pwd').parent('label').parent('div').hide();
		$('#edit_login_pwd').parent('label').parent('div').next('div').show();
	});
	
	//编辑商户平台登录密码
	$('#edit_mchpwd').on('click',function(){
		$('#edit_mchpwd').parent('label').parent('div').hide();
		$('#edit_mchpwd').parent('label').parent('div').next('div').show();
	})
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