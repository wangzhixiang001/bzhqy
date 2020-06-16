<?php 
include "application/views/newadmin/_header.php";
?>
<title>修改密码</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 修改密码 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<form action="index.php/newadmin/home/changePsw" method="post" class="form form-horizontal" id="form-article-add">
		<div id="tab-system" class="HuiTab">
			<div class="tabCon" style="display: block;">
				<div class="row cl">
					<label class="form-label col-2">原密码：</label>
					<div class="formControls col-5">
						<input type="password" name="oldpsw" id="" class="input-text" datatype="*2-16" nullmsg="原密码不能为空">
					</div>
					<div class="col-4"><?php if(isset($tip1)){ ?><span class="Validform_checktip Validform_wrong">原密码不正确</span><?php }else{?><span class="Validform_checktip"></span><?php } ?></div>
				</div>
				<div class="row cl">
					<label class="form-label col-2">新密码：</label>
					<div class="formControls col-5">
						<input type="password" id="" name="newpsw" class="input-text" datatype="*2-16" nullmsg="新密码不能为空">
					</div>
					<div class="col-4"><span class="Validform_checktip"></span></div>
				</div>
				<div class="row cl">
					<label class="form-label col-2">重复新密码：</label>
					<div class="formControls col-5">
						<input type="password" id="" name="newpsw2" class="input-text" datatype="*2-16" recheck="newpsw">
					</div>
					<div class="col-4"><span class="Validform_checktip"></span></div>
				</div>
			</div>
		</div>
		<div class="row cl">
			<div class="col-5 col-offset-2">
				<button onClick="article_save_submit();" class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>&nbsp;&nbsp;
				<button class="btn btn-default radius" type="reset"><i class="Hui-iconfont">&#xe66b;</i>取消</button>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script> 
<script type="text/javascript" src="style/newadmin/lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript">
$(function(){
	$("#form-article-add").Validform({
		tiptype:2,
		callback:function(form){
			form[0].submit();
		}
	});
});
</script>
</body>
</html>