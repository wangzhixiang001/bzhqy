<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <base href="<?php echo base_url();?>" />
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>意见建议</title>
    <link href="style/dailymeal/css/style.css" rel="stylesheet" />
	<link href="style/weui/weui.min.css" rel="stylesheet" />
	<link href="style/weui/example.css" rel="stylesheet" />
</head>
<body>
<div class="container js_container">
	<div class="page">
		<div class="hd weui_msg">
        <h1 class="page_title">留言板</h1>
        <p class="weui_msg_desc"><?php echo date('Y年m月d日');?> 星期<?php $arr = array('日','一','二','三','四','五','六');echo $arr[date('w')];?></p>
		</div>
		<div class="bd">
			<div class="weui_cells" style="margin-top:0px;">
				<ul class="my-q-an-list">
				<?php foreach ($list as $val){ ?>
					<li>
						<img class="avatar" src="<?php echo $val->avatar;?>64">
						<h3><?php echo $val->name;?></h3>
						<p><?php echo $val->msg;?></p>
						<div class="q-info">
							<span><?php echo $val->msgtime;?></span>
						</div>
					</li>
				<?php } ?>
				</ul>
			</div>
			<?php if(count($list)==20){ ?>
			<div class="bd spacing" style="margin:5px 0px 5px 0px;" id="lm">
				<a href="javascript:loadmore();" class="weui_btn weui_btn_default">点击查看更多</a>
			</div>
			<?php } ?>
		</div>
		<!-- <a href="javascript:$('#dialog2').show();" class="more_box"></a> -->
<style>
.more_btn{right:10px;bottom:10px;}
.b1{-webkit-transform:translate(-100px,-20px);}
.b2{-webkit-transform:translate(-20px,-100px);}
td{padding:6px}
td.c{background-color:#dfdfdf;}
td img{width:100%;vertical-align: middle;}
#btn_group span{position:fixed;background-color:rgba(0, 0, 0, 0.5);padding:3px 9px;color:#fff;border-radius:18px;opacity:0;visibility: hidden;-webkit-transition: opacity 0.5s, visibility 0.5s;}
#btn_group span.s{visibility:visible;opacity:1;-webkit-transition:opacity 0.5s;}
</style>
        <div id="btn_group">
    		<div class="more_btn" onclick="$('#dialog2').show();"><img src="style/dailymeal/img/btn1.png"></div>
    		<div class="more_btn" onclick="$('#dialog3').show();"><img src="style/dailymeal/img/btn2.png"></div>
    		<div id="btn_m" class="more_btn"><img src="style/dailymeal/img/more1.png"></div>
    		<span style="right:100px;bottom:125px;">今日点评</span>
    		<span style="right:180px;bottom:45px;">留言建议</span>
		</div>
		
		<div class="weui_dialog_confirm antFade" id="dialog2" style="display:none;">
			<div class="weui_mask"></div>
			<div class="weui_dialog">
				<div class="weui_dialog_hd"><strong class="weui_dialog_title">我的建议</strong></div>
				<div class="weui_dialog_bd" style="padding: 0 10px;"><textarea placeholder="输入您建议"></textarea></div>
				<div class="weui_dialog_ft">
					<a href="javascript:$('#dialog2').hide();" class="weui_btn_dialog default">取消</a>
					<a href="javascript:send();" class="weui_btn_dialog primary">提交</a>
				</div>
			</div>
		</div>
		
		<div class="weui_dialog_confirm antFade" id="dialog3" style="display:none;">
			<div class="weui_mask"></div>
			<div class="weui_dialog">
				<div class="weui_dialog_hd"><strong class="weui_dialog_title">今日点评(结果保密哦)</strong></div>
				<div class="weui_dialog_bd" style="padding: 0 10px;">
				    <table style="width:100%;text-align:center;">
				        <tr id="b_c"><td data-eval="0"><img src="style/dailymeal/img/btn2.png"></td><td data-eval="1"><img src="style/dailymeal/img/btn4.png"></td><td data-eval="2"><img src="style/dailymeal/img/btn3.png"></td></tr>
				        <tr><td>强赞</td><td>还行</td><td>差评</td></tr>
				    </table>
				</div>
				<div class="weui_cells" style="margin:0">
    				<div class="weui_cell weui_cell_select">
                        <div class="weui_cell_bd weui_cell_primary">
                            <select class="weui_select" name="select1">
                                <option value="-1">选择一个评价</option>
                            </select>
                        </div>
                    </div>
                </div>
				<div class="weui_dialog_ft">
					<a href="javascript:$('#dialog3').hide();" class="weui_btn_dialog default">取消</a>
					<a href="javascript:sendeval();" class="weui_btn_dialog primary">提交</a>
				</div>
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
<script type="text/html" id="tpl_msg">
<div class="page slideIn">
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">发表成功</h2>
            <p class="weui_msg_desc">感谢您的反馈。</p>
        </div>
        <div class="weui_opr_area">
            <p class="weui_btn_area">
                <a href="javascript:window.location.reload();" class="weui_btn weui_btn_primary">确定</a>
                <a href="javascript:WeixinJSBridge.call('closeWindow');" class="weui_btn weui_btn_default">关闭</a>
            </p>
        </div>
    </div>
</div>
</script>
<script src="http://cdnjs.gtimg.com/cdnjs/libs/zepto/1.1.4/zepto.min.js"></script>
<script>
var page=1;
var cs=[['强赞，不解释！','味道超好！','食材非常丰富！'],['还不错哦~','味道好','分量足'],['不喜欢不喜欢>_<','味道不好','类型不喜欢','不够吃……']];
function loadmore(){
	$('#loadingToast').show();
	$.get('<?php echo base_url();?>index.php/dailymeal/user/more_msg/'+page,
		function(data){
		    $('#loadingToast').hide();
			if(data.length>0){
				for(var o in data){
				    $('.my-q-an-list').append('<li><img class="avatar" src="'+data[o].avatar+'64">\
						<h3>'+data[o].name+'</h3><p>'+data[o].msg+'</p>\
						<div class="q-info"><span>'+data[o].time+'</span></div></li>');
				}
			}
			if(data.length!=20){
				$('#lm').empty().append('<a class="weui_btn weui_btn_disabled weui_btn_default">留言已全部加载完毕</a>');
			}
	},'json');
}
function send(){
	var msg=$.trim($('textarea').val());
	if(msg.length<1){
		alert('请输入您的建议。')
		return;
	}
	if(msg.length>140){
		alert('字数过长，请不要超过140字。')
		return;
	}
	$('#dialog2').hide();
	$('#loadingToast').show();
	$.post('<?php echo base_url();?>index.php/dailymeal/user/msg_ajax',{msg: msg},
		function(data){
		    $('#loadingToast').hide();
			if(data==1){
				$('.container').append($('#tpl_msg').html());
			}else{
				alert('您今天已经提交过建议了。');
				$('#dialog2').hide();
			}
	});
}
function btnact(){
	$('#btn_m').children().toggleClass('d');
	$('#btn_group').children().eq(0).toggleClass('b1').next().toggleClass('b2');
	$('#btn_group').find('span').toggleClass('s');
}
var eval=-1;
$(function(){
	btnact();
	setTimeout(function(){btnact()},1500);
	$('.more_btn').on('click','img',function(){
		btnact();
	});
	$('#b_c').on('click','td',function(){
		if(this.className=='c') return;
		$(this).parent().find('.c').removeClass('c')
		eval=$(this).addClass('c').data('eval');
		var sel='';
		for(var i=0;i<cs[eval].length;i++){
			sel+='<option value="'+i+'">'+cs[eval][i]+'</option>';
		}
		$('select').html(sel);
	});
});
function sendeval(){
	var myDate = new Date();
	if(myDate.getHours()<12){
		alert('别着急，吃完饭再给评价呗。');
		return;
	}
	if(eval==-1){
		alert('请选择您的评价。');
		return;
	}
	$('#loadingToast').show();
	$.post('<?php echo base_url();?>index.php/dailymeal/user/eval_ajax',{eval: eval,sel: $('select').val()},
		function(data){
		  $('#loadingToast').hide();
			if(data==1){
				$('.container').append($('#tpl_msg').html());
			}else if(data==0){
				alert('您今天已经提交过评价了。');
				$('#dialog3').hide();
			}else{
				alert('您今天没有报饭哦。');
				$('#dialog3').hide();
			}
	});
}
</script>
</body>
</html>