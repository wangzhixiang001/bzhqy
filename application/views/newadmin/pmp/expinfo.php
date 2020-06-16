<?php
include "application/views/newadmin/_header.php";
?>
<script type="text/javascript" src="style/comm/js/common.js"></script>
<title>付款密码修改</title>
</head>
<body>
    <form action="" method="post" class="form form-horizontal" id="demoform-1">
        <div class="row cl">
          <label class="form-label col-xs-3 col-sm-3">原始密码:</label>
          <div class="formControls col-xs-3 col-sm-3">
            <input id='oldpsw' type="password" class="input-text" autocomplete="off" placeholder="原始密码">
          </div>
        </div>
        <div class="row cl">
          <label class="form-label col-xs-3 col-sm-3">新密码：</label>
          <div class="formControls col-xs-3 col-sm-3">
            <input id='newpsw' type="password" class="input-text" autocomplete="off" placeholder="新密码">
          </div>
        </div>

      <div class="row cl">
        <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
          <input onclick='edit_pass()' class="btn btn-primary radius" type="button" value="提交">
        </div>
      </div>
    </form>
</body>
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
  <script>
  function edit_pass()
  {
    var data = {oldpsw:$("#oldpsw").val(),newpsw:$("#newpsw").val()};
    var url = '<?php echo site_url('pmp/pay_user/ajax_edit') ?>';
    if ($("#oldpsw").val() == '' || $("#newpsw").val()=='') {layer.alert('密码不能为空!', {icon: 2});}
    $.ajax({
            url:url,
            data:data,
            type:"Post",
            dataType:"json",
            success:function(data){

               if(data.code=='1'){
                  layer.alert(data.msg, {icon: 1});
                  $("#oldpsw").val('');$("#newpsw").val('');
                  }else{
                    layer.alert(data.msg, {icon: 2});
                     $("#oldpsw").val('');$("#newpsw").val('');
                  }
            },
            error:function(data){

            }
        });

  }

  </script>
</html>