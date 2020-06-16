<?php 
include "application/views/newadmin/_header.php";
$year=(int)date('Y');
$month=(int)date('m');
?>
<title>工资查询</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 午餐报饭 <span class="c-gray en">&gt;</span> 次数统计 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 
	   <span class="select-box" style="width: 100px;">
          <select class="select" size="1" id="s_year">
          <?php for($i=$year;$i>=2016;$i--){ ?>
            <option value="<?php echo $i;?>" selected><?php echo $i;?></option>
          <?php } ?>
          </select>
        </span> 年
        <span class="select-box" style="width: 70px;">
          <select class="select" size="1" id="s_month">
          <?php for($i=1;$i<=12;$i++){ ?>
            <option value="<?php echo $i;?>"<?php if($month==$i) echo " selected";?>><?php echo $i;?></option>
          <?php } ?>
          </select>
        </span> 月份
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> 
	   <span class="l">
    	   <a class="btn btn-primary radius" data-title="批量导入" onclick="fileupload.show()" href="javascript:;"><i class="Hui-iconfont">&#xe642;</i> 批量导入</a>
    	   <!-- <a class="btn btn-primary radius" data-title="导出当前excel" onclick="outexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出当前excel</a> -->
	   </span>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th>员工编号</th>
					<th>姓名</th>
					<th>所在部门</th>
					<th>基础额度</th>
					<th>统筹额度</th>
					<th>餐费</th>
					<th>请假</th>
					<th>处罚</th>
					<th>奖励</th>
					<th>实发工资</th>
					<th>导入时间</th>
				</tr>
			</thead>
			<tbody>
				<tr class="text-c">
					<td colspan="11">正在加载……</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- 批量添加 -->
<div id="layer_upload" style="display:none">
    <form id="upload_1" class="form form-horizontal pd-20" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="index.php/salary/admin/upload">
        <div class="row cl">
            <label class="col-3 form-label">请选择日期</label>
            <div class="col-8">
                <span class="select-box" style="width: 100px;">
                  <select class="select" size="1" id="year">
                  <?php for($i=$year;$i>=2016;$i--){ ?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                  <?php } ?>
                  </select>
                </span> 年
                <span class="select-box" style="width: 70px;">
                  <select class="select" size="1" id="month">
                  <?php for($i=1;$i<=12;$i++){ ?>
                    <option value="<?php echo $i;?>"<?php if($month==$i) echo " selected";?>><?php echo $i;?></option>
                  <?php } ?>
                  </select>
                </span> 月份
            </div>
        </div>
        <div class="row cl">
            <label class="col-3 form-label">请选择文件</label>
            <div class="col-8">
                <span  class="btn-upload">
                    <input style="width:200px" class="input-text upload-url radius" type="text" name="uploadfile-1" id="uploadfile-1" readonly>
                    <a href="javascript:void();" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe645;</i> 浏览文件</a>
                    <input id="file" type="file" name="file" accept=".xls,.xlsx" class="input-file">
                </span >
            </div>
        </div>
        <div class="row cl">
            <div class="col-offset-3 col-8">
                <a class="text-muted" href="style/upload/工资导入模板.xlsx">点击下载导入模板。</a>
            </div>
        </div>
        <div class="row cl">
            <div class="col-offset-3 col-8">
                <button type="button" class="btn btn-primary radius" onclick="fileupload.begin()">保存</button>
                <button type="button" class="btn radius" onclick="layer.closeAll();">取消</button>
            </div>
        </div>
    </form>
    <div id="upload_2" class="form form-horizontal pd-20" style="display:none">
        <div class="row cl">
            <label class="col-md-12">处理状态：<span id="do_info"></span></label>
        </div>
        <div class="row cl">
            <div class="col-md-12">
                <div class="progress"><div class="progress-bar"><span class="sr-only" style="width:0%"></span></div></div>
            </div>
        </div>
        <div class="row cl">
            <label class="col-md-12">上传完毕后，系统需要一定时间进行处理，请耐心等待。</label>
        </div>
    </div>
</div>

<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script> 
<script src="http://cdn.bootcss.com/jquery.form/3.51/jquery.form.min.js"></script>
<script type="text/javascript">
var year="<?php echo $year;?>";
var month="<?php echo $month;?>";
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/salary/admin/ajaxlist",
        "type": "POST",
        "data": function ( d ) {
            d.year = year;
            d.month = month;
        }
    },
    "columns": [
        { "data": "userid" },
        { "data": "name" },
        { "data": "department" },
        { "data": "basic" },
        { "data": "overall" },
        { "data": "food" },
        { "data": "leave" },
        { "data": "punish" },
        { "data": "reward" },
        { "data": "all" },
        { "data": "time" }
    ]
});
function filter(){
	year=$('#s_year').val();
	month=$('#s_month').val();
	table.ajax.reload();
}

function outexcel(){
	window.location.href="<?php echo base_url();?>index.php/dailymeal/admin/outnum?year="+year+"&month="+month;
}

var fileupload={
	state:0,
	show:function(){
		layer.open({
		    type: 1,
		    closeBtn: 0,
		    title:'批量导入新引导员',
		    area:'500px',
		    content: $('#layer_upload'), //捕获的元素
		    end: function(){
		    	$('#layer_upload').find('input').val('');
		    }
		});
	},
	begin:function(){
		var filename=$('#file').val();
		if(filename==''){
			layer.alert('请先选择您要导入的xlsx文件！', {icon: 0});
			return;
		}
		var k=filename.substring(filename.length-4);
		if(k!='xlsx'&&k!='.xls'){
			layer.alert('仅支持xls|xlsx格式文件导入，建议您下载模板按照模板填写导入！', {icon: 0});
			return;
		}
		$('#upload_1').hide().next().show();
		$('#upload_1').ajaxSubmit({
			dataType: 'json',
			success: function(data){
				if(data.code==1){
					fileupload.readfile(data.msg);
				}else{
					$('#upload_1').show().next().hide();
					$('.btn-upload input').val('');
					layer.msg(data.msg, {icon: 0});
				}
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var jindu=percentComplete + '%';
				$('.sr-only').width(jindu);
				$('#do_info').html('已上传'+jindu);
		    }
		});
	},
	readfile:function(filename){
		$('#do_info').html('上传完毕，开始导入文件');
		var year=$('#year').val();
		var month=$('#month').val();
		$.post('index.php/salary/admin/addBatch',{
		    filename:filename,
		    year:year,
		    month:month
	    },function(data){
			$('.btn-upload input').val('');
	        if(data.code==1){
	        	layer.alert(data.msg,{icon: 1},function(index){
	        		$('#upload_1').show().next().hide();
	        		layer.closeAll();
        		}); 
	        }else{
	        	$('#upload_1').show().next().hide();
				layer.msg(data.msg, {icon: 0});
	        }
		},'json');
	}
}
</script> 
</body>
</html>