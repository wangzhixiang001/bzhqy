<?php 
include "application/views/newadmin/_header.php";
?>
<script type="text/javascript" src="style/comm/js/common.js"></script>
<title>查询登陆</title>
</head>
<body>
    <form action="" method="post" class="form form-horizontal" id="demoform-1">
        <div class="row cl">
        	<label class="form-label col-xs-3 col-sm-3">登陆账号：</label>
        	<div class="formControls col-xs-3 col-sm-3">
        		<input id='username' type="text" class="input-text" autocomplete="off" placeholder="帐号">
        	</div>
        </div>
        <div class="row cl">
        	<label class="form-label col-xs-3 col-sm-3">密码框：</label>
        	<div class="formControls col-xs-3 col-sm-3">
        		<input id='psw' type="password" class="input-text" autocomplete="off" placeholder="密码">
        	</div>
        </div>
        <div class="row cl">
        	<label class="form-label col-xs-3 col-sm-3">手机验证码：</label>
        	<div class="formControls col-xs-3 col-sm-2">
        		<input id='telcode' type="text" class="input-text" autocomplete="off" placeholder="获取验证码">
        	</div>
        	<input  id ='codebtn' onclick='getcode()' class="btn btn-primary radius" type="button" value="获取验证码">
        </div>
        
    	<div class="row cl">
    		<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
    			<input onclick='login()' class="btn btn-primary radius" type="button" value="提交">
    		</div>
    	</div>
    </form>
				
	<script>
	function login()
	{
		var data = {username:$("#username").val(),psw:$("#psw").val(),code:$("#telcode").val()};
		var url = '<?php echo site_url('salary/login/check')?>';
		h.post(url,data,sucess);
	}
	function sucess(data)
	{
		if(data.status=='ok')
		{
			window.location.href=data.url;
		}
		else
		{
			alert(data.msg);
		}
	}
	function getcode()
	{
		var url = '<?php echo site_url('salary/login/send')?>';
		h.post(url,{},sendResult);
	}
	function sendResult(data)
	{
		if(data.status=='ok')
		{
			$("#codebtn").val('发送成功')
			$("#codebtn").attr("readonly","readonly");
		}
		else
			alert('发送失败')
	}
	</script>			
</body>
</html>