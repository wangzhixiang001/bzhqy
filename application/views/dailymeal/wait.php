<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>我要订餐</title>
    <link href="style/weui/weui.min.css" rel="stylesheet" />
    <link href="style/weui/example.css" rel="stylesheet" />
</head>
<body>
<div class="container js_container">
    <div class="page"><br>
    <div class="weui_msg">
        <div class="weui_icon_area"><img style="border-style: solid;border-width:5px;border-color: transparent;width:60%;" src="style/dailymeal/img/foodyes.jpg"></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">报饭已截止，记得按时打饭</h2>
                <p class="weui_msg_desc">今天是<?php echo date('Y年m月d日');?> 星期<?php $arr = array('日','一','二','三','四','五','六');echo $arr[date('w')];?></p>
                <p class="weui_msg_desc">你是公司第<?php echo $pm;?>位报饭的人，击败了今天<?php echo round(($all-$pm)/$all*100);?>%的用户</p>
            </div>
            <div class="weui_extra_area">
                <a href="javascript:actionSheet.show();">查看更多</a>
            </div>
        </div>
    </div>
</div>
<div id="actionSheet_wrap">
    <div class="weui_mask_transition" id="mask" onclick="actionSheet.hide();"></div>
    <div class="weui_actionsheet" id="weui_actionsheet">
        <div class="weui_actionsheet_menu">
            <div class="weui_actionsheet_cell" onclick="window.location.href='<?php echo base_url();?>index.php/dailymeal/user/vf'">今日报饭</div>
            <div class="weui_actionsheet_cell" onclick="window.location.href='<?php echo base_url();?>index.php/dailymeal/user/msg'"><a>留言板</a></div>
        </div>
        <div class="weui_actionsheet_action">
            <div class="weui_actionsheet_cell" onclick="actionSheet.hide();">关闭</div>
        </div>
    </div>
</div>
<script src="http://cdnjs.gtimg.com/cdnjs/libs/zepto/1.1.4/zepto.min.js"></script>
<script>
var actionSheet={
	show:function(){
		$('#mask').show();
		$('#weui_actionsheet').addClass('weui_actionsheet_toggle');
		$('#mask').addClass('weui_fade_toggle antFade');
	},
	hide:function(){
		$('#weui_actionsheet').removeClass('weui_actionsheet_toggle');
		$('#mask').removeClass('weui_fade_toggle antFade');
		setTimeout(function(){
			$('#mask').hide();
		},400);
	}
}
</script>
</body>
</html>