<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>部门列表</title>
    <link rel="stylesheet" href="/bzhqy/style/dist/lib/weui.min.css">
    <link rel="stylesheet" href="/bzhqy/style/dist/css/jquery-weui.css">
</head>
<body ontouchstart>
<div class="weui-search-bar" id="searchBar">
    <form class="weui-search-bar__form" onkeydown="if(event.keyCode==13) return false;">
        <div class="weui-search-bar__box">
            <i class="weui-icon-search"></i>
            <input type="search" class="weui-search-bar__input" id="searchInput"  placeholder="搜索" required="">
            <a href="javascript:clear()" class="weui-icon-clear" id="searchClear"></a>
        </div>
        <label class="weui-search-bar__label" id="searchText">
            <i class="weui-icon-search"></i>
            <span>搜索人名</span>
        </label>
    </form>
    <a href="javascript:cancel()" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
</div>

<div class="weui-cells">
    <?php foreach ($users as $k=> $u){ ?>
    <a class="weui-cell weui-cell_access personal" href="<?php echo site_url('Reimbursement/index/personal/'.$u['userid']); ?>" id="self_<?php echo $u['userid'];?>">
        <div class="weui-cell" style="width: 70%;">
            <div class="weui-cell__hd"><img src="<?php echo $u['avatar']; ?>" alt="" style="width:20px;margin-right:5px;display:block"></div>
            <div class="weui-cell__bd">
                <p><?php  echo $u['name']; ?></p>
            </div>
        </div>
        <span><?php  echo $u['total_money']/100; ?>元</span>
    </a>
    <?php } ?>
</div>

<script src="/bzhqy/style/dist/lib/jquery-2.1.4.js"></script>
<script src="/bzhqy/style/dist/lib/fastclick.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });

    $('#searchInput').bind('input propertychange', function() {
        $('.department').hide();
        var text = $("#searchInput").val();
        var reg = new RegExp("[\\u4E00-\\u9FFF]+", "g");
        if (reg.test(text)) {
            $('.personal').each(function () {
                var $self = $(this);
                var flag = $self.text().search(text)
                if (flag > -1) {
                    $self.show();

                } else {
                    $self.hide();

                }
            });
        }
    });

    function cancel(){
        $('.personal').show();

    }

    function clear(){
        $('#searchInput').val('');
        $('.personal').show();
    }

</script>
<script src="/bzhqy/style/dist/js/jquery-weui.js"></script>
<script src="/bzhqy/style/mui/js/mui.min.js"></script>
</body>
</html>