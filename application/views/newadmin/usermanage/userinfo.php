<?php 
include "application/views/newadmin/_header.php";
?>
<title>用户详情</title>
</head>
<body>
<div class="cl pd-20" style=" background-color:#5bacb6">
  <img class="avatar radius size-XL l" src="<?php echo $avatar;?>64">
  <dl style="margin-left:80px; color:#fff">
    <dt><span class="f-18"><?php echo $name;?></span> <span class="pl-10 f-12">职务：<?php echo $position?$position:'未设置';?></span></dt>
    <dd class="pt-10 f-12" style="margin-left:0">部门：<?php echo $department;?></dd>
  </dl>
</div>
<div class="pd-20">
  <table class="table">
    <tbody>
      <tr>
        <th class="text-r" width="80">性别：</th>
        <td><?php $sex=array('未设置','男','女');echo $sex[$gender];?></td>
      </tr>
      <tr>
        <th class="text-r">手机：</th>
        <td><?php echo $mobile;?></td>
      </tr>
      <tr>
        <th class="text-r">邮箱：</th>
        <td><?php echo $email;?></td>
      </tr>
      <tr>
        <th class="text-r">微信号：</th>
        <td><?php echo $weixinid;?></td>
      </tr>
      <tr>
        <th class="text-r">关注状态：</th>
        <td><?php echo $status?'已关注':'未关注';?></td>
      </tr>
    </tbody>
  </table>
</div>
</body>
</html>