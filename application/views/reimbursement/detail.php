<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>报销申请详情</title>
    <link rel="stylesheet" href="/bzhqy/style/dist/lib/weui.min.css">
    <link rel="stylesheet" href="/bzhqy/style/dist/css/jquery-weui.css">
</head>
<style>
    .weui-cell__hd{
        color: #999988;
    }
</style>
<body>

<?php if ($type =='none'){?>
    <header class='demos-header'>
        <h1 class="demos-title">找不到了</h1>
    </header>
<?php }else{ ?>
<div class="weui-cells" style="margin-top: 0">
    <div class="weui-form-preview__hd" style="padding: 10px 15px;">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__value" style="font-size: 1.3em;text-align: left;"><img src="<?php echo $detail['avatar']; ?>" alt="" style="width:36px;margin-right:5px;display:block;float: left"><?php echo $detail['name']; ?>的报销</label>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="customert" class="weui-label">客户名称</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="customert"><?php echo $detail['cus_name']; ?></div>
        </div>

    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="products" class="weui-label">项目名称</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="products"><?php echo $detail['pmpname']; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="type" class="weui-label">报销类型</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="type"><?php echo $detail['typename']; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="is_online" class="weui-label">线上或线下</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="is_online"><?php echo $detail['is_online']; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">报销事由</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="cause"><?php echo $detail['cause']; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">发生时间</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="happend_time"><?php echo $detail['happend_time']; ?></div>
        </div>
    </div>


    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">费用金额</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="money"><?php echo $detail['money']/100 .'元'; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="has_invoice" class="weui-label">有无票据</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="has_invoice"><?php echo $detail['has_invoice']; ?></div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="money_txt" class="weui-label">总金额</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="money_txt"><?php echo $detail['money']/100 .'元'; ?></div>
        </div>
    </div>




    <div class="weui-gallery" id="gallery">
        <span class="weui-gallery__img" id="galleryImg"></span>
        <div class="weui-gallery__opr">
            <a href="javascript:" class="weui-gallery__del">
                <i class="weui-icon-delete weui-icon_gallery-delete"></i>
            </a>
        </div>
    </div>
    <?php if($detail['has_invoice'] == '有'){ ?>
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <div class="weui-uploader">
                        <div class="weui-uploader__hd">
                            <p class="weui-uploader__title">票据照片</p>
                        </div>
                        <div class="weui-uploader__bd">
                            <ul class="weui-uploader__files" id="uploaderFiles">
                                <?php foreach ($detail['photos'] as $p){ ?>
                                    <li class="weui-uploader__file" style="background-image:url('<?php echo base_url().$p?>')"></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">申请时间</label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__fl" id="ac_time"><?php echo $detail['ac_time']; ?></div>
        </div>
    </div>

    <?php if ($detail['status'] == -1){ ?>
        <a href="javascript:;" class="weui-btn weui-btn_disabled weui-btn_warn"  style="width: 70%">已驳回</a>
    <?php }?>

    <?php if ($detail['status'] == 0){ ?>
        <?php if ($type == 'director'){ ?>
            <div class="weui-flex">
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn weui-btn_primary" style="width: 80%" onclick="checkType(1)">同意</a>
                </div>
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn  weui-btn_warn" style="width: 80%" onclick="checkType(-1)">驳回</a>
                </div>
            </div>
        <?php }elseif ($type == 'self'){?>
            <div class="weui-flex">
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn  weui-btn_warn" style="width: 80%" onclick="deleteReimbursement()">撤销</a>
                </div>
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn weui-btn_primary" style="width: 80%" onclick="editReimbursement()">重新提交</a>
                </div>
            </div>
        <?php }?>
    <?php }?>

    <?php if ($detail['status'] == 1){ ?>
        <?php if ($type == 'final'){ ?>
            <div class="weui-flex">
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn weui-btn_primary" style="width: 80%" onclick="checkType(2)">同意</a>
                </div>
                <div class="weui-flex__item">
                    <a href="javascript:;" class="weui-btn  weui-btn_warn" style="width: 80%" onclick="checkType(-1)">驳回</a>
                </div>
            </div>
        <?php }else{?>
            <a href="javascript:;" class="weui-btn weui-btn_disabled weui-btn_primary"  style="width: 70%">审批中</a>
        <?php }?>

    <?php }?>

    <?php if ($detail['status'] == 2){ ?>
        <a href="javascript:;" class="weui-btn weui-btn_disabled weui-btn_primary"  style="width: 70%">已审批</a>
    <?php }?>


</div>
<?php } ?>

<script src="/bzhqy/style/dist/lib/jquery-2.1.4.js"></script>
<script src="/bzhqy/style/dist/lib/fastclick.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="/bzhqy/style/dist/js/jquery-weui.js"></script>
<script src="/bzhqy/style/mui/js/mui.min.js"></script>
<script>
    function checkType(type) {
        if (type == -1){
            $.confirm("确定驳回吗？", function() {
                $.showLoading('处理中');
                doExamine(type);
            }, function() {
                return false;
            });
        }else{
            $.showLoading('处理中')
            doExamine(type);
        }
    }

    function doExamine(type) {
        $.ajax({
            type: 'post',
            url:"<?php echo site_url('Reimbursement/index/doExamine'); ?>",
            dataType: 'json',
            data:{
              'type':type,
              'id[0]':<?php echo $detail['id']; ?>
            },
            success: function(data) {
                $.hideLoading()
                if (data.code==1){
                    $.toast(data.msg);
                    setTimeout(function () {
                        window.location.href = data.url;
                    },1000)
                }else{
                    $.toast(data.msg, 'cancel');
                }
            }
        });
    }

    function deleteReimbursement() {
        $.confirm("确定要撤销审批申请吗？", function() {
            $.showLoading('处理中');
            $.ajax({
                type: 'post',
                url:"<?php echo site_url('Reimbursement/index/delete'); ?>",
                dataType: 'json',
                data:{
                    'id':<?php echo $detail['id']; ?>
                },
                success: function(data) {
                    $.hideLoading()
                    if (data.code==1){
                        $.toast(data.msg);
                        setTimeout(function () {
                            window.location.href = data.url;
                        },1000)
                    }else{
                        $.toast(data.msg, 'cancel');
                    }
                }
            });
        }, function() {
            return false;
        });
    }

    function editReimbursement() {
        $.confirm("确定要撤销审批申请后重新提交吗？", function() {
            $.showLoading('处理中');
            setTimeout(function () {
                window.location.href = "<?php echo site_url('Reimbursement/index/index/'.$detail['id']); ?>";
            },1500)
        }, function() {
            return false;
        });
    }
</script>
</body>
</html>