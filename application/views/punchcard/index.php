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
    <div class="page">
    <div class="weui_msg"  style="padding-top:8%;">
        <div class="weui_icon_area" style="margin-bottom:5%;"><img style="border-style: solid;border-width:5px;border-color: transparent;width:34%;" src="style/dailymeal/img/timg.jpeg"></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">打卡完成</h2>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a href="<?php echo base_url();?>index.php/dailymeal/PunchCard/dateList" class="weui_btn weui_btn_primary">打卡记录</a>
                   
            </div>
           
            <!-- <div class="weui_extra_area">
                <a href="javascript:actionSheet.show();">查看更多</a>
            </div> -->
        </div>
    </div>
</div>
<!-- loading toast -->
<div id="loadingToast" class="weui_loading_toast" style="display:none;">
    <div class="weui_mask_transparent" style="background-color:rgba(0,0,0,0.10);"></div>
    <div class="weui_toast">
        <div class="weui_loading">
            <div class="weui_loading_leaf weui_loading_leaf_0"></div>
            <div class="weui_loading_leaf weui_loading_leaf_1"></div>
            <div class="weui_loading_leaf weui_loading_leaf_2"></div>
            <div class="weui_loading_leaf weui_loading_leaf_3"></div>
            <div class="weui_loading_leaf weui_loading_leaf_4"></div>
            <div class="weui_loading_leaf weui_loading_leaf_5"></div>
            <div class="weui_loading_leaf weui_loading_leaf_6"></div>
            <div class="weui_loading_leaf weui_loading_leaf_7"></div>
            <div class="weui_loading_leaf weui_loading_leaf_8"></div>
            <div class="weui_loading_leaf weui_loading_leaf_9"></div>
            <div class="weui_loading_leaf weui_loading_leaf_10"></div>
            <div class="weui_loading_leaf weui_loading_leaf_11"></div>
        </div>
        <p class="weui_toast_content">正在处理</p>
    </div>
</div>

<div id="toast" style="display:none;">
    <div class="weui_mask_transparent"></div>
    <div class="weui_toast antFade">
        <i class="weui_icon_toast"></i>
        <p class="weui_toast_content">已完成</p>
    </div>
</div>

<div id="actionSheet_wrap">
    <div class="weui_mask_transition" id="mask" onclick="actionSheet.hide();"></div>
    <div class="weui_actionsheet" id="weui_actionsheet">
        <div class="weui_actionsheet_menu">
            <div class="weui_actionsheet_cell" onclick="window.location.href='<?php echo base_url();?>index.php/dailymeal/PunchCard/dateList'">打卡记录</div>
            
        </div>
        <div class="weui_actionsheet_action">
            <div class="weui_actionsheet_cell" onclick="actionSheet.hide();">关闭</div>
        </div>
    </div>
</div>
<script type="text/html" id="tpl_msg">
<div class="page slideIn">
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">操作成功</h2>
            <p class="weui_msg_desc">报饭成功，记得准时吃饭哦。</p>
        </div>
        <div class="weui_opr_area">
            <p class="weui_btn_area">
                <a href="javascript:window.location.reload();" class="weui_btn weui_btn_primary">确定</a>
                <a href="javascript:WeixinJSBridge.call('closeWindow');" class="weui_btn weui_btn_default">关闭</a>
            </p>
        </div>
        <div class="weui_extra_area">
            <a href="javascript:actionSheet.show();">查看更多</a>
        </div>
    </div>
</div>
</script>
<script src="http://cdnjs.gtimg.com/cdnjs/libs/zepto/1.1.4/zepto.min.js"></script>
<script>


function timeout(){
	$('#bfbtn').hide().next().show();
	$('#txdj').hide();
}
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
var tixing={
	tx:0,
	show:function(){
		if(this.tx) return;
		$('#dialog1').show();
	},
	send:function(){
		var _this=this;
		$('#dialog1').hide();
		$('#loadingToast').show();
		$.get('<?php echo base_url();?>index.php/dailymeal/user/send_msg/'+(($('.weui_switch').attr('checked'))?0:1),
			function(data){
			    $('#loadingToast').hide();
			    if(data>0){
				    $('#txdj').addClass('weui_btn_disabled');
				    _this.tx=1;
			    }
				if(data==1){
					$('#toast').show();
					setTimeout(function(){
						$('#toast').hide();
					},2000);
				}else if(data==2)
					alert('今天已经有很多人发提醒了，感谢您的好意~');
				else alert('提醒消息发送失败');
		});
		$('#mask').removeClass('weui_fade_toggle antFade');
		setTimeout(function(){
			$('#mask').hide();
		},400);
	}
}
</script>
</body>
</html>