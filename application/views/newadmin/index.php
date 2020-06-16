<!DOCTYPE HTML>
<html>
<head>
<base href="<?php echo base_url(); ?>" />
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
<link href="style/newadmin/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
<link href="style/newadmin/lib/Hui-iconfont/1.0.6/iconfont.css" rel="stylesheet" type="text/css" />
<link href="style/newadmin/skin/default/skin.css" rel="stylesheet" type="text/css" id="skin" />
<link href="style/newadmin/css/style.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script type="text/javascript" src="style/newadmin/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>后台管理</title>
</head>
<body>
<header class="Hui-header cl">
 <a class="Hui-logo l" title="北之海企业号后台管理 v2.0" href="index.php/newadmin/home">北之海企业号后台管理</a>
 <a class="Hui-logo-m l" href="index.php/newadmin/home" title="H-ui.admin" style=" width:90px;">北之海</a>
 <span class="Hui-subtitle l">V2.0</span>
	<ul class="Hui-userbar">
		<li>您好：</li>
		<li class="dropDown dropDown_hover"><a class="dropDown_A"><?php echo $this->session->userdata('name'); ?> <i class="Hui-iconfont">&#xe6d5;</i></a>
			<ul class="dropDown-menu radius box-shadow">
				<li><a href="javascript:creatIframe('index.php/newadmin/home/changepsw','修改密码')">修改密码</a></li>
				<li><a href="index.php/newadmin/home/loginout">退出</a></li>
			</ul>
		</li>
		<li id="Hui-skin" class="dropDown right dropDown_hover"><a href="javascript:;" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
			<ul class="dropDown-menu radius box-shadow">
				<li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
				<li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
				<li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
				<li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
				<li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
				<li><a href="javascript:;" data-val="orange" title="绿色">橙色</a></li>
			</ul>
		</li>
	</ul>
	<a href="javascript:;" class="Hui-nav-toggle Hui-iconfont" aria-hidden="false">&#xe667;</a> </header>
<aside class="Hui-aside">
	<input runat="server" id="divScrollValue" type="hidden" value="" />
	<div class="menu_dropdown bk_2">
		<!-- <dl id="menu-article">
			<dt><i class="Hui-iconfont">&#xe60a;</i> 用户管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a _href="index.php/usermanage/userlist/index" data-title="用户列表" href="javascript:void(0)">用户列表</a></li>
				</ul>
			</dd>
		</dl> -->
		<dl id="menu-article">
			<dt><i class="Hui-iconfont">&#xe66a;</i> 用户信息<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a _href="index.php/dailymeal/admin/userlist" data-title="用户信息" href="javascript:void(0)">用户信息</a></li>
					<!-- <li><a _href="index.php/dailymeal/admin/userlist" data-title="次数统计" href="javascript:void(0)">次数统计</a></li> -->
					<li><a _href="index.php/dailymeal/admin/index" data-title="打卡记录" href="javascript:void(0)">打卡记录</a></li>
				</ul>
			</dd>
		</dl>
		<!-- <dl id="menu-article">
			<dt><i class="Hui-iconfont">&#xe66a;</i> 工资查询<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a _href="index.php/salary/login" data-title="工资详情" href="javascript:void(0)">工资详情</a></li>
				</ul>
			</dd>
		</dl> -->
		<!-- <dl id="menu-article">
			<dt><i class="Hui-iconfont">&#xe66a;</i> 项目报销<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
			<dd>
				<ul>
					<li><a _href="index.php/pmp/pmplist" data-title="项目管理" href="javascript:void(0)">项目管理</a></li>
					<li><a _href="index.php/pmp/pmpcus" data-title="客户管理" href="javascript:void(0)">客户管理</a></li>
					<li><a _href="index.php/pmp/pmpexp/index" data-title="报销记录" href="javascript:void(0)">报销记录</a></li>
					<li><a _href="index.php/pmp/pmpexp/one_apply" data-title="个人报销汇总" href="javascript:void(0)">个人报销汇总</a></li>


				</ul>
			</dd>
		</dl> -->
		<!-- <dl id="menu-article">
            <dt><i class="Hui-iconfont">&#xe66a;</i> 绩效考核<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a _href="index.php/performance/admin/index" data-title="部门管理" href="javascript:void(0)">部门管理</a></li>
                    <li><a _href="index.php/performance/admin/joinlist" data-title="人员管理" href="javascript:void(0)">人员管理</a></li>
                    <li><a _href="index.php/performance/admin/account" data-title="账号管理" href="javascript:void(0)">账号管理</a></li>
                    <li><a _href="index.php/performance/admin/rlist" data-title="考核记录" href="javascript:void(0)">考核记录</a></li>
                </ul>
            </dd>
        </dl> -->
	</div>
</aside>
<div class="dislpayArrow"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<div id="Hui-tabNav" class="Hui-tabNav">
		<div class="Hui-tabNav-wp">
			<ul id="min_title_list" class="acrossTab cl">
				<li class="active"><span title="欢迎" data-href="welcome.html">首页</span><em></em></li>
			</ul>
		</div>
		<div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
	</div>
	<div id="iframe_box" class="Hui-article">
		<div class="show_iframe">
			<div style="display:none" class="loading"></div>
			<iframe scrolling="yes" frameborder="0" src="style/newadmin/welcome.html"></iframe>
		</div>
	</div>
</section>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
</body>
</html>