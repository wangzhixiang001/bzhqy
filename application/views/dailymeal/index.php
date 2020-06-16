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
        <div class="weui_icon_area" style="margin-bottom:5%;"><img style="border-style: solid;border-width:5px;border-color: transparent;width:50%;" src="style/dailymeal/img/food.jpg"></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">你需要报饭吗？</h2>
                <p class="weui_msg_desc">今天是<?php echo date('Y年m月d日');?> 星期<?php $arr = array('日','一','二','三','四','五','六');echo $arr[date('w')];?></p>
                <p class="weui_msg_desc">距离截止剩余:<span id="htime">--小时--分钟--秒</span></p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a id="bfbtn" href="javascript:bao();" class="weui_btn weui_btn_primary">我要报饭！</a>
                    <a href="javascript:;" class="weui_btn weui_btn_disabled weui_btn_default" style="display:none;">报饭已截止</a>
<?php if($cantp<3&&time()){ ?><a id="txdj" href="javascript:tixing.show();" class="weui_btn" style="background-color:#00B0FF;">提醒大家~</a><?php } ?>
                </p>
            </div>
            <div class="weui_extra_area">
                <a href="javascript:actionSheet.show();">查看更多</a>
            </div>
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

<div class="weui_dialog_confirm antFade" id="dialog1" style="display:none;">
    <div class="weui_mask"></div>
    <div class="weui_dialog">
        <div class="weui_dialog_hd"><strong class="weui_dialog_title">提醒</strong></div>
        <div class="weui_dialog_bd">您要发消息提醒其他人吗？
            <div class="weui_cell weui_cell_switch">
                <div class="weui_cell_hd weui_cell_primary">匿名提醒</div>
                <div class="weui_cell_ft">
                    <input class="weui_switch" type="checkbox">
                </div>
            </div>
        </div>
        <div class="weui_dialog_ft">
            <a href="javascript:$('#dialog1').hide();" class="weui_btn_dialog default">取消</a>
            <a href="javascript:tixing.send();" class="weui_btn_dialog primary">确定</a>
        </div>
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
            <div class="weui_actionsheet_cell" onclick="window.location.href='<?php echo base_url();?>index.php/dailymeal/user/vf'">今日报饭</div>
            <div class="weui_actionsheet_cell" onclick="window.location.href='<?php echo base_url();?>index.php/dailymeal/user/msg'"><a>留言板</a></div>
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
function bao(){
	$('#loadingToast').show();
	$.get('<?php echo base_url();?>index.php/dailymeal/user/bf_ajax',
		function(data){
			if(data==1){
				$('#loadingToast').hide();
				$('.container').append($('#tpl_msg').html());
			}
	});
}
var timelost={
	time:<?php echo $end;?>,
	ts:setInterval(function(){
		if(timelost.time<1){
			clearInterval(timelost.ts);
			timeout();
		}else{
			var sysj=--timelost.time;
			var h=parseInt(sysj/3600);sysj-=3600*h;
			var i=parseInt(sysj/60);sysj-=60*i;
			var ttt=h+'小时'+i+'分钟'+sysj+'秒';
			$('#htime').html(ttt);
		}
	},1000)
};
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