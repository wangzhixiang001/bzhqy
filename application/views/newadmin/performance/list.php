<?php 
include "application/views/newadmin/_header.php";
?>
<title>绩效考核-部门</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 绩效考核 <span class="c-gray en">&gt;</span> 部门 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 
	    部门名称：
		<input type="text"  id="name" class="input-text" style="width:120px;" value="">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
				    <th>部门名称</th>
					<th>上下游部门</th>
                    <th>操作</th>
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
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript">
var name="";
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/performance/admin/departmentListAjax",
        "type": "POST",
        "data": function ( d ) {
            d.name = name;
        }
    },
    "columns": [
        { "data": "name" },
        { "data": "brother_department" },
        { "render": function(a,a,row){ return '<button class="btn btn-primary size-S radius" onclick="edit('+row.id+')">考核部门</button>'} }
    ]
});
function filter(){
	name=$('#name').val();
	table.ajax.reload();
}
function reset(){
    name ="";
	$('#name').val(name);
	table.ajax.reload();
}

function edit(id) {
    layer.open({
        type: 2,
        title :'考核部门',
        area: ['600px', '700px'],
        content: "<?php echo base_url();?>index.php/performance/admin/edit_depart?id="+id //这里content是一个普通的String
    });
}

function outexcel(){
	window.location.href="<?php echo base_url();?>index.php/dailymeal/admin/out?begin="+begin+"&end="+end;
}
</script> 
</body>
</html>