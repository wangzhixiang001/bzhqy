<?php 
include "application/views/newadmin/_header.php";
$begin=date('Y-m-1');
$end=date('Y-m-d');
?>
<title>所有预约</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 午餐报饭 <span class="c-gray en">&gt;</span> 报饭记录 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin;?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end;?>">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a class="btn btn-primary radius" data-title="导出当前excel" onclick="outexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出当前excel</a></span></div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
				    <th>日期</th>
					<th>员工编号</th>
					<th>姓名</th>
					<th>报饭状态</th>
					<th>报饭时间</th>
					<th>取消时间</th>
                    <th>就餐状态</th>
                    <th>就餐时间</th>
					<th>点评</th>
					<th>留言</th>
				</tr>
			</thead>
			<tbody>
				<tr class="text-c">
					<td colspan="8">正在加载……</td>
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
var cs=[['强赞，不解释！','味道超好！','食材非常丰富！'],['还不错哦~','味道好','分量足'],['不喜欢不喜欢>_<','味道不好','类型不喜欢','不够吃……']];
var ev=['好评','中评','差评'];
var begin="<?php echo $begin;?>";
var end="<?php echo $end;?>";
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/dailymeal/admin/ajaxlist",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
        }
    },
    "columns": [
        { "render": function(a,a,row){return row.time.substr(0,10) } },
        { "data": "userid" },
        { "data": "name" },
        { "render": function(a,a,row){if(row.type==1 & row.status !=2) return '已报饭';else if(row.type==9) return '未报饭';else  if(row.type==0) return '已取消';else  if(row.type==1 & row.status ==2) return '已报饭(补签)'; } },
        { "render": function(a,a,row){if(row.type==1) return row.time.substr(11);else return ''; } },
        { "render": function(a,a,row){if(row.type==0) return row.time.substr(11);else return ''; } },
        { "render": function(a,a,row){if(row.status==0) return '未就餐';else return '已就餐'; } },
        { "render": function(a,a,row){if(row.status!=0) return row.eat_time.substr(11);else return ''; } },
        { "render": function(a,a,row){if(row.eval!=0) return ev[row.eval.substr(1,1)]+'('+cs[row.eval.substr(1,1)][row.eval.substr(2,1)]+')';else return ''; } },
        { "render": function(a,a,row){if(row.msg=='') return '';else return row.msg+'&nbsp;('+row.msgtime.substr(11)+')';} }
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
function outexcel(){
	window.location.href="<?php echo base_url();?>index.php/dailymeal/admin/out?begin="+begin+"&end="+end;
}
</script> 
</body>
</html>