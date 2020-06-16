<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>报销审批</title>
    <link rel="stylesheet" href="/bzhqy/style/dist/lib/weui.min.css">
    <link rel="stylesheet" href="/bzhqy/style/dist/css/jquery-weui.css">
</head>
<style>
    .weui-form-preview__value{
        color: #000000;
        text-align: left;
    }
    .weui-form-preview__hd:after{
        border-bottom: 0;
        text-align: left;
    }
    .status{
        border: 1px solid #999988;
        font-size: 1em;
        width: 4em;
        height: 1.5em;
        line-height: 1.5em;
        border-radius:0.5em;
    }
</style>
<body ontouchstart>
<div class="weui-search-bar" id="searchBar">
    <form class="weui-search-bar__form" action="<?php echo site_url('Reimbursement/index/examine'); ?>" method="post">
        <div class="weui-search-bar__box">
            <i class="weui-icon-search"></i>
            <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索人名,客户名称" required="" name="keywords">
            <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
        </div>
        <label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
            <i class="weui-icon-search"></i>
            <span><?php echo $keywords==null?'搜索人名,客户名称':$keywords; ?></span>
        </label>
    </form>
    <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
</div>
<div class="weui-tab">
    <div class="weui-navbar" style="z-index: 9">
        <a class="weui-navbar__item weui-bar__item--on" href="#tab1" style="padding: 7px 0;">
            待处理<?php
            $num = 0;
            foreach ($wait as $v){
                $num += count($v);
            }
            if ($num>0){
                echo '·'.$num;
            }
            ?>
        </a>
        <a class="weui-navbar__item" href="#tab2" style="padding: 7px 0;">
            已处理
        </a>
    </div>
    <div class="weui-tab__bd" style="padding-top: 37px;">
        <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
            <?php foreach ($wait as $name =>$v){ ?>
                <div class="weui-form-preview">
                    <div class="weui-form-preview__hd" style="padding: 0 15px;">
                        <div class="weui-form-preview__item">
                            <label class="weui-form-preview__value" style="font-size: 1.0em;"><?php echo $name; ?>的报销</label>
                        </div>
                    </div>
                    <?php foreach ($v as $k =>$sv){ ?>
                    <a href="<?php  echo site_url('Reimbursement/index/detail/'.$sv['id']); ?>">
                    <div class="weui-form-preview">
                        <div class="weui-form-preview__bd">
                            <div class="weui-form-preview__item">
                                <label class="weui-form-preview__label">客户名称</label>
                                <span class="weui-form-preview__value"><?php echo $sv['cus_name']; ?></span>
                            </div>
                            <div class="weui-form-preview__item">
                                <label class="weui-form-preview__label">项目名称</label>
                                <span class="weui-form-preview__value"><?php echo $sv['pmpname']; ?></span>
                            </div>
                            <div class="weui-form-preview__item">
                                <label class="weui-form-preview__label">报销事项</label>
                                <span class="weui-form-preview__value"><?php echo $sv['cause']; ?></span>
                            </div>
                            <div class="weui-form-preview__item">
                                <label class="weui-form-preview__label">报销费用</label>
                                <span class="weui-form-preview__value"><?php echo $sv['money']/100; ?>元</span>
                            </div>
                            <div class="weui-form-preview__item">
                                <label class="weui-form-preview__label">报销时间</label>
                                <span class="weui-form-preview__value"><?php echo $sv['ac_time']; ?></span>
                            </div>
                        </div>
                    </div>
                    </a>
                    <br>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <div id="tab2" class="weui-tab__bd-item">
            <?php foreach ($success as $name =>$v){ ?>
                <div class="weui-form-preview">
                    <div class="weui-form-preview__hd">
                        <div class="weui-form-preview__item">
                            <label class="weui-form-preview__value" style="font-size: 1.0em;float: left"><?php echo $name; ?>的报销</label>
                        </div>
                    </div>
                    <?php foreach ($v as $k =>$sv){ ?>
                        <div class="weui-form-preview">
                            <div class="weui-form-preview__bd">
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">客户名称</label>
                                    <span  style="text-align: left;color: #000000;float: left"><?php echo $sv['cus_name']; ?></span>
                                    <?php if ($sv['status']==0){ ?>
                                        <span class="status">未审批</span>
                                    <?php }elseif ($sv['status']==1){ ?>
                                        <span class="status">审批中</span>
                                    <?php }elseif ($sv['status']==2){ ?>
                                        <span class="status">已审批</span>
                                    <?php }else{ ?>
                                        <span class="status">已驳回</span>
                                    <?php } ?>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">项目名称</label>
                                    <span class="weui-form-preview__value"><?php echo $sv['pmpname']; ?></span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">报销事项</label>
                                    <span class="weui-form-preview__value"><?php echo $sv['cause']; ?></span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">报销费用</label>
                                    <span class="weui-form-preview__value"><?php echo $sv['money']/100; ?>元</span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">报销时间</label>
                                    <span class="weui-form-preview__value"><?php echo $sv['ac_time']; ?></span>
                                </div>
                            </div>

                        </div>
                        <br>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script src="/bzhqy/style/dist/lib/jquery-2.1.4.js"></script>
<script src="/bzhqy/style/dist/lib/fastclick.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });

</script>
<script src="/bzhqy/style/dist/js/jquery-weui.js"></script>
<script src="/bzhqy/style/mui/js/mui.min.js"></script>
</body>
</html>