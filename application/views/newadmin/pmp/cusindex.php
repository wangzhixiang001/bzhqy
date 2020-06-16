<?php
include "application/views/newadmin/_header.php";
$begin = date('2017-01-01');
$end = date('Y-m-d');
?>
<title>项目管理</title>
<style>
	.l{
		padding-left: 10px;
	}
</style>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 项目管理 <span class="c-gray en">&gt;</span>客户列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
<!-- 	<div class="text-c">
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin; ?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end; ?>">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div> -->
	<div class="cl pd-5 bg-1 bk-gray mt-20">
	<span class="l">
	 	<a class="btn btn-success radius" data-title="新增项目" onclick="edit()" href="javascript:;"><i class="Hui-iconfont">&#xe665;</i> 新增客户</a>
	 </span>
	  <!-- <span class="l">
	 	<a class="btn btn-primary radius" data-title="导出当前excel" onclick="outexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出当前excel</a>
	 </span> -->
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th>ID</th>
				    <th>客户名称</th>
					<th>创建日期</th>
					<!-- <th>操作</th> -->
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
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript">
var begin="<?php echo $begin; ?>";
var end="<?php echo $end; ?>";
$(document).ready(function(){
	layer.config({
	    extend: 'extend/layer.ext.js'
	});
});
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url(); ?>index.php/pmp/pmpcus/ajaxpmp",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
        }
    },
    "columns": [
    	{ "data": "id" },
        { "data": "cus_name" },
        { "data": "ctime" },
        // { "render": function(a,a,row) { return '<button class="btn btn-primary size-S radius" onclick="edit('+row.id+',\''+row.cus_name+'\')">编辑</button>&nbsp;<button class="btn btn-primary size-S radius" onclick="ajax_del('+row.id+')">删除</button>'; } }
    ]
});
function filter(){
	begin=$('#logmin').val();
	end=$('#logmax').val();
	table.ajax.reload();
}
function reset(){
	begin='<?php echo $begin; ?>';
	end='<?php echo $end; ?>';
	$('#logmin').val(begin);
	$('#logmax').val(end);
	table.ajax.reload();
}
function outexcel(){
	window.location.href="<?php echo base_url(); ?>index.php/dailymeal/admin/out?begin="+begin+"&end="+end;
}
//编辑
function edit(id=0,cus_name=''){
	if (id== 0) {var title = '新增客户'}else{var title= '编辑客户'}
	layer.prompt({
		value: cus_name,
	    title: title
	}, function(cusname){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmpcus/ajax_edit/'+id,
            data:{'id':id,'cus_name':cusname},
            type:"Post",
            dataType:"json",
            success:function(data){
               layer.close(ld);
               if(data=='1'){
				layer.alert('添加成功', {icon: 1});
				table.ajax.reload();
				}else{
					layer.alert('添加失败，请重新添加!', {icon: 2});
				}
            },
            error:function(data){

            }
        });

	});

}
//删除
function ajax_del(id) {
	layer.confirm('是否要删除?', {icon: 3, title:'删除'}, function(data){
    var ld = layer.load(1, { shade: [0.1,'#000'] });
     $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmpcus/ajax_del/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(data){
               layer.close(ld);
               if(data=='1'){
				layer.alert('删除成功', {icon: 1});
				table.ajax.reload();
				}else{
					layer.alert('删除失败!', {icon: 2});
				}
            },
            error:function(data){

            }
        });

});
}
</script>
</body>
</html>