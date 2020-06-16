<?php
include "application/views/newadmin/_header.php";
?>
</head>
<body>
<div class="page-container">
    <form action="" method="post" class="form form-horizontal" id="form-article-add" style="margin-left:50px" onsubmit='return false;'>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>部门名称：</label>

            <div class="formControls col-xs-8 col-sm-3">
                <?php echo $info['name']; ?>
            </div>

        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>考核部门</label>
            <div class="formControls col-xs-8 col-sm-3">
                <div class="skin-minimal">

                    <?php foreach($departs as $key =>$v){ ?>
                        <div class="check-box">
                            <input type="checkbox" name = "brother_department[]" id="checkbox-<?php echo $key; ?>" value ="<?php echo $key; ?>" <?php if(in_array($key,$info['brother_department'])){echo "checked" ; } ?>>
                            <label for="checkbox-<?php echo $key; ?>">&nbsp;<?php echo $v; ?></label>
                        </div>
                        <br/>
                    <?php } ?>

                </div>

            </div>
        </div>

        <input type="hidden" class="input-text" value="<?php echo $info['id'] ?>"  id="id" name="id">

        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button onClick="article_save_submit();" class="btn btn-primary radius size-S" type="submit"> 保存</button>
            </div>
        </div>

    </form>
    <script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
    <script type="application/javascript">
        function article_save_submit(){
            var param =  $('#form-article-add').serialize();
            var url = "<?php echo site_url("performance/admin/editAjax");?>";
            $.post(url,param,function(data){
                data = eval('('+data+')');
                if(data.code == 0){
                    layer.alert(data.msg,{icon: 1},function(){
                        closeParantLayer();
                    })
                }else{
                    layer.alert(data.msg,{icon: 0});
                }
            });
            return false;
        }
        function closeParantLayer(){
            parent.location.reload()
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index);
        }

    </script>
</div>
</body>
</html>