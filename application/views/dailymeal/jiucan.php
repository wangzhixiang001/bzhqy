<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <base href="<?php echo base_url();?>" />
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>就餐</title>
    <link href="style/weui/weui.min.css" rel="stylesheet" />
    <link href="style/weui/example.css" rel="stylesheet" />
</head>
<body>
<div class="container js_container">
    <div class="page slideIn">
        <div class="weui_msg">
            <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">操作成功</h2>
                <?php if ($status == 1){ ?>
                    <p class="weui_msg_desc">吃会你的卡路里</p>
                <?php }elseif ($status == 2){ ?>
                    <p class="weui_msg_desc">补签成功,吃会你的卡路里</p>
                <?php } ?>

            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a href="javascript:WeixinJSBridge.call('closeWindow');" class="weui_btn weui_btn_default">关闭</a>
                </p>
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

</body>
</html>