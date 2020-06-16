<!DOCTYPE HTML>
<html>
<head>
<base href="<?php echo base_url();?>" />
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="style/newadmin/lib/html5.js"></script>
<script type="text/javascript" src="style/newadmin/lib/respond.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/PIE_IE678.js"></script>
<![endif]-->
<link href="style/newadmin/css/H-ui.min.css" rel="stylesheet" type="text/css" />
<link href="style/newadmin/css/H-ui.login.css" rel="stylesheet" type="text/css" />
<link href="style/newadmin/css/style.css" rel="stylesheet" type="text/css" />
<link href="style/newadmin/lib/Hui-iconfont/1.0.6/iconfont.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script type="text/javascript" src="style/newadmin/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>后台登录</title>
</head>
<body>
<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"><h1>北之海企业号后台管理</h1></div>
<div class="loginWraper">
<img class="bgimg" src="style/newadmin/images/admin-login-bg.jpg">
  <div id="loginform" class="loginBox">
    <form class="form form-horizontal" action="index.php/newadmin/auth/login" onsubmit="return login()" method="post">
	<br>
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-8">
          <input id="user" name="userid" type="text" placeholder="账户" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-8">
          <input id="pwd" name="password" type="password" placeholder="密码" class="input-text size-L">
        </div>
      </div>
      <div class="row">
        <div class="formControls col-8 col-offset-3">
          <p id="tip"><?php if(isset($bad)) echo $bad?'账号或密码错误！':'当前账号被限制登录！';?></p>
        </div>
      </div>
      <div class="row">
        <div class="formControls col-8 col-offset-3">
          <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
		  &nbsp;&nbsp;&nbsp;&nbsp;
          <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="footer">Copyright 北之海广告有限公司 v2.0</div>
<script type="text/javascript">
String.prototype.trim = function() {
  return this.replace(/^\s*([\S\s]*?)\s*$/, '$1');
}
function login(){
	var userid=document.getElementById('user').value.trim();
	if(userid==''){
		document.getElementById('tip').innerHTML='请输入账号！';
		return false;
	}else document.getElementById('tip').innerHTML='';
	var psw=document.getElementById('pwd').value.trim();
	if(psw==''){
		document.getElementById('tip').innerHTML='请输入密码！';
		return false;
	}else document.getElementById('tip').innerHTML='';
	return true;
}
</script>
</body>
</html>