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
    body, html {
        height: 100%;
        -webkit-tap-highlight-color: transparent;
    }
    .weui-form-preview__value{
        color: #000000;
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
<div class="weui-tab">
    <div class="weui-navbar" style="z-index: 9">
        <a class="weui-navbar__item weui-bar__item--on" href="#tab1" style="padding: 7px 0;">
            待处理<?php echo count($wait)>0?'·'.count($wait):'';?>
        </a>
        <a class="weui-navbar__item" href="#tab2" style="padding: 7px 0;">
            已处理
        </a>
    </div>
    <div class="weui-tab__bd"  style="padding-top: 37px;">
        <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active  weui-cells_checkbox">

            <?php if (count($wait)>0){;?>
                <div class="weui-form-preview__hd" style="padding: 0 15px;">
                    <div class="weui-form-preview__item">
                        <label class="weui-form-preview__value" style="font-size: 1.0em;text-align: left;"><?php echo $wait[0]['name']; ?>的报销</label>
                    </div>
                </div>
                <label class="weui-cell weui-check__label" for="s11">
                    <div class="weui-cell__hd">
                        <input type="checkbox" class="weui-check" name="checkbox1" id="s11">
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">
                        <p>全选</p>
                    </div>
                </label>
            <?php };?>


            <form action="<?php echo site_url('Reimbursement/index/doExamine'); ?>" id="checkBoxList">
            <?php foreach ($wait as $sv){ ?>
                    <div class="weui-form-preview">
                        <label class="weui-cell weui-check__label" for="s11_<?php echo $sv['id']; ?>">
                            <div class="weui-cell__hd">
                                <input type="checkbox" class="weui-check" name="id[]" id="s11_<?php echo $sv['id']; ?>"  value="<?php echo $sv['id']; ?>">
                                <i class="weui-icon-checked"></i>
                            </div>
                        </label>
                        <a href="<?php  echo site_url('Reimbursement/index/detail/'.$sv['id']); ?>" class="weui-form-preview__btn weui-form-preview__btn_primary">
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
                    <br>
            <?php } ?>
                <input type="hidden" name="final" value='1'>
            </form>
            <?php if (count($wait)>0){ ?>
                <div class="weui-tabbar">
                    <a href="javascript:;" class="weui-btn weui-btn_disabled weui-btn_primary" id="submitForm"   style="width:">批量通过</a>
                </div>
            <?php } ?>
        </div>

        <div id="tab2" class="weui-tab__bd-item">
            <?php foreach ($log as $k=>$v){ ?>
                <a class="weui-cell weui-cell_access" href="javascript:;" onclick="changeDepart('<?php echo $k;?>')">
                    <div class="weui-cell__bd">
                        <p><?php echo $k; ?>月报销</p>
                    </div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div id="d_<?php echo $k;?>" data-hidden="1" hidden>
                    <div class="weui-form-preview__hd">
                        <div class="weui-form-preview__item">
                            <label class="weui-form-preview__value" style="font-size: 1.0em;text-align: left;"><?php echo $v[0]['name']; ?>的报销</label>
                        </div>
                    </div>
                    <?php foreach ($v as $k =>$sv){ ?>
                        <div class="weui-form-preview">
                            <div class="weui-form-preview__bd">
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">客户名称</label>
                                    <span class="weui-form-preview__value"  style="text-align: left;color: #000000;float: left"><?php echo $sv['cus_name']; ?></span>
                                    <?php if ($sv['status']==0){ ?>
                                        <span class="status">未审批</span>
                                    <?php }elseif ($sv['status']==1){ ?>
                                        <span class="status">审批中</span>
                                    <?php }elseif ($sv['status']==2){ ?>
                                        <span class="status">已通过</span>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
<script src="/bzhqy/style/dist/lib/fastclick.js"></script>
<script>
    function changeDepart(id) {
        if ($('#d_'+id).data('hidden')==1){
            $("div[id^=d_]").hide();
            $("div[id^=d_]").data('hidden',1);
            $('#d_'+id).show();
            $('#d_'+id).data('hidden',0);
        }else{
            $("div[id^=d_]").hide();
            $("div[id^=d_]").data('hidden',1);
        }
    }
    $(function() {
        FastClick.attach(document.body);


        //实现全选反选
        $("#s11").on('click', function() {
            $("#checkBoxList :checkbox").prop("checked", $(this).prop('checked'));
            if ($(this).prop('checked')) {
                $('#submitForm').removeClass('weui-btn_disabled');
            }else{
                $('#submitForm').addClass('weui-btn_disabled');
            }
        })
        $("#checkBoxList :checkbox").on('click', function() {
            //当选中的长度等于checkbox的长度的时候,就让控制全选反选的checkbox设置为选中,否则就为未选中
            if($("#checkBoxList :checkbox").length === $("#checkBoxList :checked").length) {
                $("#s11").prop("checked", true);
            } else {
                $("#s11").prop("checked", false);
            }
            if ($("#checkBoxList :checked").length === 0) {
                $('#submitForm').addClass('weui-btn_disabled');
            }else{
                $('#submitForm').removeClass('weui-btn_disabled');
            }
        })

        //提交
        $('#submitForm').on('click',function () {
            //检测是否有选中
            if ($('#submitForm').hasClass('weui-btn_disabled')) {
                $.toast('请选择要处理项!', 'cancel');
                return false;
            }else {
                $.confirm("确定后,报销将批量通过", function() {
                    $('form').ajaxSubmit({
                        type: 'post',
                        dataType: 'json',
                        beforeSend:function () {
                            $.showLoading('处理中')
                        },
                        success: function(data) {
                            $.hideLoading()
                            if (data.code==1){
                                $.toast(data.msg);
                                window.location.href = data.url;
                            }else{
                                $.toast(data.msg, 'cancel');
                            }
                        }
                    });
                }, function() {
                    return false;
                });
            }
        })
    });

</script>
<script src="/bzhqy/style/dist/js/jquery-weui.js"></script>
<script src="/bzhqy/style/mui/js/mui.min.js"></script>
</body>
</html>