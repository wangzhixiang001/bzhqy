<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>错误</title>
    <link href="style/weui/weui.min.css" rel="stylesheet" />
    <link href="style/weui/example.css" rel="stylesheet" />
</head>
<body>
<div class="container js_container">
    <div class="page">
    <div class="weui_msg"  style="padding-top:8%;">
        <div class="weui_icon_area" style="margin-bottom:5%;"><img style="border-style: solid;border-width:5px;border-color: transparent;width:50%;" src="style/dailymeal/img/foodno.jpg"></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">错误</h2>
                <p class="weui_msg_desc"><?php echo $msg; ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>