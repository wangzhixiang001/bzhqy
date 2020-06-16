<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to CodeIgniter</title>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
        <script>
        wx.config({
            debug: true,
            appId: '<?php echo $jssdk["appId"]; ?>',
            timestamp:<?php echo $jssdk["timestamp"]; ?>,
            nonceStr: '<?php echo $jssdk["nonceStr"]; ?>',
            signature: '<?php echo $jssdk["signature"]; ?>',
            jsApiList: [
                'hideMenuItems',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                "onMenuShareWechat"
            ]
        });
        wx.ready(function () {
            wx.checkJsApi({
                jsApiList: ['hideMenuItems',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareWechat','onMenuShareQZone'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                success: function(res) {
                    alert(rel)
                }
            });

            wx.hideMenuItems({
                menuList: [
                    'menuItem:copyUrl', // 复制链接
                    'menuItem:openWithQQBrowser', // 在QQ浏览器中打开
                    'menuItem:openWithSafari',//在Safari中打开
                    'menuItem:readMode', //阅读模式
                    'menuItem:share:email'
                ]
            });
            wx.onMenuShareTimeline({
                title: '亲，快帮我抢个小米手环！',
                link: '<?php echo base_url(); ?>index.php/Home/test',
                imgUrl: '',
                 success: function (res) {
                    alert('ddf')
                    },
                cancel: function () {
                    alert('fds')
                    }


            });
            wx.onMenuShareQZone({
                    title: 'sdsad', // 分享标题
                    desc: '', // 分享描述
                    link: '', // 分享链接
                    imgUrl: '', // 分享图标
                    success: function () {
                    alert('fds')
                    },
                    cancel: function () {
                    alert('fds')
                    }
                    });
            wx.onMenuShareAppMessage({
                title: '亲，快帮我抢个小米手环！',
                desc: '点击链接，为我助力，帮我拿免费得小米手环！',
                link: '<?php echo base_url(); ?>index.php/Home/test',
                imgUrl: '',
                success: function (res) {
                    alert(rel)
                    },
                cancel: function () {
                    alert('fds')
                    }
            });

        });
        </script>
    <style type="text/css">

    ::selection { background-color: #E13300; color: white; }
    ::-moz-selection { background-color: #E13300; color: white; }

    body {
        background-color: #fff;
        margin: 40px;
        font: 13px/20px normal Helvetica, Arial, sans-serif;
        color: #4F5155;
    }

    a {
        color: #003399;
        background-color: transparent;
        font-weight: normal;
    }

    h1 {
        color: #444;
        background-color: transparent;
        border-bottom: 1px solid #D0D0D0;
        font-size: 19px;
        font-weight: normal;
        margin: 0 0 14px 0;
        padding: 14px 15px 10px 15px;
    }

    code {
        font-family: Consolas, Monaco, Courier New, Courier, monospace;
        font-size: 12px;
        background-color: #f9f9f9;
        border: 1px solid #D0D0D0;
        color: #002166;
        display: block;
        margin: 14px 0 14px 0;
        padding: 12px 10px 12px 10px;
    }

    #body {
        margin: 0 15px 0 15px;
    }

    p.footer {
        text-align: right;
        font-size: 11px;
        border-top: 1px solid #D0D0D0;
        line-height: 32px;
        padding: 0 10px 0 10px;
        margin: 20px 0 0 0;
    }

    #container {
        margin: 10px;
        border: 1px solid #D0D0D0;
        box-shadow: 0 0 8px #D0D0D0;
    }
    </style>
</head>
<body>

<div id="container">
    <h1>测试转发</h1>

    <div id="body">
        <p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

        <p>If you would like to edit this page you'll find it located at:</p>
        <code>application/views/welcome_message.php</code>

        <p>The corresponding controller for this page is found at:</p>
        <code>application/controllers/Welcome.php</code>

        <p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
    </div>

    <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo (ENVIRONMENT === 'development') ? 'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
</div>

</body>
</html>
