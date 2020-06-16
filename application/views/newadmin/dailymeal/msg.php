<?php 
include "application/views/newadmin/_header.php";
$begin=date('Y-m-1');
$end=date('Y-m-d');
?>
<title>所有预约</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 午餐报饭 <span class="c-gray en">&gt;</span> 意见建议 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin;?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end;?>">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th>员工编号</th>
					<th>姓名</th>
					<th>留言内容</th>
					<th>留言时间</th>
				</tr>
			</thead>
			<tbody>
				<tr class="text-c">
					<td colspan="4">正在加载……</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript">
var begin="<?php echo $begin;?>";
var end="<?php echo $end;?>";
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/dailymeal/admin/ajaxmsglist",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
        }
    },
    "columns": [
        { "data": "userid" },
        { "data": "name" },
        { "data": "msg" },
        { "data": "time" }
    ]
});
function filter(){
	begin=$('#logmin').val();
	end=$('#logmax').val();
	table.ajax.reload();
}
function reset(){
	begin='<?php echo $begin;?>';
	end='<?php echo $end;?>';
	$('#logmin').val(begin);
	$('#logmax').val(end);
	table.ajax.reload();
}
</script> 
</body>
</html>