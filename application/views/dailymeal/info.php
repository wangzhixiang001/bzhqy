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
<style type="text/css">
table{
	margin:auto;border-style: solid;
    border-width: 2px;
	border-color: #ECECEC;
    background-color: #fff;
	width:80%;
}
table .d{background:url(style/dailymeal/img/b.png) no-repeat center;}
.datetitle{
	color:#fff;background-color: #3cc51f;
}
th,td{padding:5px 8px 5px 8px;}
</style>
</head>
<body>
<div class="container js_container">
    <div class="page">
        <div class="weui_msg">
            <div class="weui_icon_area">
                
                    <h2 class="weui_msg_title">本月报饭记录</h2>
                <table>
                    <tr class="datetitle"><th colspan="7"><?php echo date('Y-m');?></th></tr>
                    <tr>
                        <th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th>
                    </tr>
<?php 
$day='';
$begindate=date("Y-n-1");
$wk=date("w", strtotime($begindate));
$end=(int)date('d', strtotime("$begindate +1 month -1 day"));
?>
                    <?php for($i=0;$i<6;$i++){ ?>
                    <tr>
                        <?php for($y=0;$y<7;$y++){ if($day==''&&$wk==$y) $day=1;else if($day==$end+1){$day='';$wk=-1;}?>
                        <td<?php if($day!=''&&in_array($day, $bao)){ ?> class="d"<?php } ?>><?php echo $day;if($day!='') $day++;?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </table><br>
                <div class="weui_text_area">
                    <p class="weui_msg_desc">本月共报饭<?php echo count($bao);?>次</p>
                </div>
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
        <p class="weui_toast_content">正在查询</p>
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
var datechange={
	thisyear:2016,
	thismonth:7,
	
}
</script>
</body>
</html>