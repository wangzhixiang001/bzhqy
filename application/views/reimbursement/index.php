<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>报销申请</title>
    <link rel="stylesheet" href="/bzhqy/style/dist/lib/weui.min.css">
    <link rel="stylesheet" href="/bzhqy/style/dist/css/jquery-weui.css">
</head>
<style>
    .black{
        color: #000000;
    }
</style>
<body>
<?php echo form_open('/Reimbursement/index/toExamine', 'id="myForm"'); ?>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="customert" class="weui-label">客户名称<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__ft" id="customert"><?php echo isset($detail['cus_name'])?$detail['cus_name']:'请选择'; ?></div>
            <input class="weui-input" id="customert_h" type="hidden"  readonly name="customer_id" value="<?php echo isset($detail['customer_id'])?$detail['customer_id']:null; ?>"/>
            <input class="weui-input"  type="hidden"   name="userid" value="<?php echo $userid;?>" />
            <?php if (!empty($detail)){ ?>
                <input class="weui-input"  type="hidden"   name="id" value="<?php echo $detail['id'];?>" />
            <?php } ?>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="products" class="weui-label">合同或项目名称<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <div id="products_box">
                <div class="weui-cell__ft" id="products"><?php echo isset($detail['pmpname'])?$detail['pmpname']:'请选择'; ?></div>
            </div>
            <input class="weui-input" id="products_h" type="hidden" value="<?php echo isset($detail['product_id'])?$detail['product_id']:null; ?>" readonly="" name="product_id" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="type" class="weui-label">报销类型<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__ft" id="type"><?php echo isset($detail['typename'])?$detail['typename']:'请选择'; ?></div>
            <input class="weui-input" id="type_h" type="hidden" value="<?php echo isset($detail['type'])?$detail['type']:null; ?>" name="type" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="is_online" class="weui-label">线上或线下<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__ft" id="is_online"><?php echo isset($detail['is_online_name'])?$detail['is_online_name']:'请选择'; ?></div>
            <input class="weui-input" id="is_online_h" type="hidden" value="<?php echo isset($detail['is_online'])?$detail['is_online']:null; ?>" name="is_online" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">报销事由<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <input class="weui-input weui-cell__ft" type="text"  placeholder="请输入" id="cause" name="cause" value="<?php echo isset($detail['cause'])?$detail['cause']:null; ?>"/>
        </div>
    </div>

    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label for="date" class="weui-label">发生时间<span style="color: red">*</span></label></div>
            <div class="weui-cell__bd">
                <input class="weui-input weui-cell__ft" id="date" type="text" placeholder="请选择" name="happend_time" value="<?php echo isset($detail['happend_time'])?$detail['happend_time']:null; ?>">
            </div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">费用金额<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <input class="weui-input weui-cell__ft" type="number" pattern="[0-9]*" placeholder="请输入" id="money" name="money" value="<?php echo isset($detail['money'])?$detail['money']/100:null; ?>"/>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd"><label for="has_invoice" class="weui-label">有无票据<span style="color: red">*</span></label></div>
        <div class="weui-cell__bd">
            <div class="weui-cell__ft" id="has_invoice"><?php echo isset($detail['has_invoice_name'])?$detail['has_invoice_name']:'请选择'; ?></div>
            <input class="weui-input" id="has_invoice_h" type="hidden" value="<?php echo isset($detail['has_invoice'])?$detail['has_invoice']:null; ?>" name="has_invoice" />
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>总金额</p>
        </div>
        <div class="weui-cell__ft" id="money_txt"><?php echo isset($detail['money'])?$detail['money']/100:0; ?>元</div>
    </div>

    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>审批人<span style="color: red">*</span></p>
        </div>
        <div class="weui-cell__ft black" id="director"><?php echo isset($detail['director_name'])?$detail['director_name']:''; ?></div>
        <input type="hidden" name="director" value="<?php echo isset($detail['director'])?$detail['director']:''; ?>" id="director_id" />
    </div>

    <div class="weui-gallery" id="gallery">
        <span class="weui-gallery__img" id="galleryImg"></span>
        <div class="weui-gallery__opr">
            <a href="javascript:" class="weui-gallery__del">
                <i class="weui-icon-delete weui-icon_gallery-delete"></i>
            </a>
        </div>
    </div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <div class="weui-uploader">
                    <div class="weui-uploader__hd">
                        <p class="weui-uploader__title ">票据照片</p>
                    </div>
                    <div class="weui-uploader__bd">
                        <ul class="weui-uploader__files" id="uploaderFiles">
                            <?php if (isset($detail['photos']) && $detail['photos']){
                                foreach ($detail['photos'] as $v){ ?>
                                    <li class="weui-uploader__file" style="background-image:url('<?php echo base_url().$v;?>')"></li>
                            <?php }}?>
                        </ul>
                        <ul id="photos" hidden>
                            <?php if (isset($detail['photos']) && $detail['photos']){
                                foreach ($detail['photos'] as $v){ ?>
                                    <li><input type="hidden" name="photos[]" value="<?php echo $v;?>" class="photos"></li>
                                <?php }}?>
                        </ul>
                        <div class="weui-uploader__input-box">
                            <input id="uploaderInput" class="weui-uploader__input zjxfjs_file" type="file" accept="image/*" multiple="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="javascript:"  id="submitForm">提交</a>
    </div>
</div>
</form>

<script src="/bzhqy/style/dist/lib/jquery-2.1.4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
<script src="/bzhqy/style/dist/lib/fastclick.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="/bzhqy/style/dist/js/jquery-weui.js"></script>
<script src="/bzhqy/style/mui/js/mui.min.js"></script>

<script>
    //客户
    $("#customert").picker({
        cols: [
            {
                textAlign: 'center',
                values: <?php echo $customer_id_list; ?>,
                displayValues: <?php echo $customer_list; ?>
            },
        ],
        onChange:function(p,v,dv){
            $("#customert").html(dv);
            $("#customert").addClass('black');
            $("#customert_h").val(v);

        },
        onClose:function () {
            $.ajax({
                type: 'post',
                url: "<?php echo site_url('Reimbursement/index/ajaxGetProjects'); ?>",
                dataType: 'json',
                data: {
                    "id":$("#customert_h").val(),
                },
                success: function (data) {
                    //清空项目选择框重新绑定以动态赋值
                    $('#products_box').empty();
                    $('#products_box').html("<div class='weui-cell__ft' id='products'>请选择</div>");
                    //项目选择
                    $("#products").picker({
                        cols: [
                            {
                                textAlign: 'center',
                                values: data.msg.products_ids,
                                displayValues:data.msg.products
                            },
                        ],
                        onChange:function(p,v,dv){
                            $("#products").html(dv);
                            $("#products").addClass('black');
                            $("#products_h").val(v);
                        },
                        onClose:function () {
                            $.ajax({
                                type: 'post',
                                url: "<?php echo site_url('Reimbursement/index/ajaxGetDirector'); ?>",
                                dataType: 'json',
                                data: {
                                    "product_id":$("#products_h").val()
                                },
                                success: function (data) {
                                    $('#director').html(data.msg.name);
                                    $('#director_id').val(data.msg.userid);
                                }
                            });
                        },
                    });
                }
            });
        },
    });


    //报销类型
    $("#type").picker({
        cols: [
            {
                textAlign: 'center',
                values:<?php echo $type_ids; ?>,
                displayValues:<?php echo $type_list; ?>,
            },
        ],
        onChange:function(p,v,dv){
            $("#type").html(dv);
            $("#type").addClass('black');
            $("#type_h").val(v);
        }
    });

    //线上线下
    $("#is_online").picker({
        cols: [
            {
                input:'#test',
                textAlign: 'center',
                values:[1,2],
                displayValues:['线上','线下']
            }
        ],
        onChange:function(p,v,dv){
            $("#is_online").html(dv);
            $("#is_online").addClass('black');
            $("#is_online_h").val(v);
        }
    });

    //有无票据
    $("#has_invoice").picker({
        cols: [
            {
                textAlign: 'center',
                values:[1,2],
                displayValues:['无票据','有票据']
            },
        ],
        onChange:function(p,v,dv){
            $("#has_invoice").html(dv);
            $("#has_invoice").addClass('black');
            $("#has_invoice_h").val(v);
        }
    });
    
    //金额
    $("#money").on('change',function () {
        $("#total_money").val($("#money").val());
        $("#money_txt").html($("#money").val());
        $("#money_txt").addClass('black');
        $("#total_money").addClass('black');
    })


    //日期
    $("#date").calendar({
        onChange: function (p, values, displayValues) {
           $("#date").addClass('black');
        }
    });

    //票据照片
    mui.init();
    $(function() {
        var tmpl = '<li class="weui-uploader__file" style="background-image:url(#url#)"></li>',
            input_tmpl = '<li><input type="hidden" name="photos[]" value="#url#" class="photos"></li>',
            $gallery = $("#gallery"),
            $galleryImg = $("#galleryImg"),
            photos = $("#photos"),
            $uploaderInput = $("#uploaderInput"),
            $uploaderFiles = $("#uploaderFiles");

        $uploaderInput.on("change", function(e) {
            var src, url = window.URL || window.webkitURL || window.mozURL,
                files = e.target.files;
            for(var i = 0, len = files.length; i < len; ++i) {
                var file = files[i];

                var reader = new FileReader();

                if(url) {
                    src = url.createObjectURL(file);
                } else {
                    src = e.target.result;
                }
                $uploaderFiles.append($(tmpl.replace('#url#', src)));

                reader.onload = function (e) {
                    photos.append($(input_tmpl.replace('#url#', e.target.result)));
                };
                reader.readAsDataURL(file);
            }
        });
        var index; //第几张图片
        $uploaderFiles.on("click", "li", function() {
            index = $(this).index();
            $galleryImg.attr("style", this.getAttribute("style"));
            $gallery.fadeIn(100);
        });
        $gallery.on("click", function() {
            $gallery.fadeOut(100);
        });
        //删除图片
        $(".weui-gallery__del").click(function() {
            $uploaderFiles.find("li").eq(index).remove();
            photos.find("li").eq(index).remove();
        });
    });

    //验证
    function checkParams(){
        if($("input[name='customer_id']").val().length==0){
            $.toast('请选择客户!', 'cancel');
            return false;
        }

        if($("input[name='product_id']").val().length==0){
            $.toast('请选择项目!', 'cancel');
            return false;
        }

        if($("input[name='type']").val().length==0){
            $.toast('请选择报销类型!', 'cancel');
            return false;
        }

        if($("input[name='is_online']").val().length==0){
            $.toast('请选择线上线下!', 'cancel');
            return false;
        }

        if($("input[name='cause']").val().length==0){
            $.toast('请输入报销事由!', 'cancel');
            return false;
        }

        if($("input[name='happend_time']").val().length==0){
            $.toast('请选择发生时间!', 'cancel');
            return false;
        }

        if($("input[name='money']").val().length==0){
            $.toast('请输入费用金额!', 'cancel');
            return false;
        }

        if($("input[name='has_invoice']").val().length==0){
            $.toast('请选择有无票据!', 'cancel');
            return false;
        }else if ($("input[name='has_invoice']").val() ==2 && $(".photos").length ==0) {
            $.toast('请上传票据照片!', 'cancel');
            return false;
        }

        $.showLoading('处理中')

    }

    //提交
    $('#submitForm').on('click',function () {
        $('form').ajaxSubmit({
            type: 'post',
            dataType: 'json',
            beforeSend:function () {
                return checkParams();
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
    })

</script>
</body>
</html>