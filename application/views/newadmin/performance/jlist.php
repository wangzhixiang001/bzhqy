<?php 
include "application/views/newadmin/_header.php";
?>
<title>绩效考核-账号</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 绩效考核 <span class="c-gray en">&gt;</span> 账号 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c">
        <input type="text" id="name" placeholder ="请输入员工编号或姓名 搜索" class="input-text" style="width:150px;">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
	   <span class="l">
    	   <a class="btn btn-primary radius" data-title="将现有员工的数据与微信企业号同步，不会移除企业号中删除的员工数据" onclick="syn_all()" href="javascript:;"><i class="Hui-iconfont">&#xe62b;</i>一键同步用户</a>
	   </span>
    </div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
                    <th>员工编号</th>
                    <th>姓名</th>
                    <th>部门</th>
                    <th>职位</th>
                    <th>电话</th>
                    <th>状态</th>
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
        "url": "<?php echo base_url();?>index.php/performance/admin/jListAjax",
        "type": "POST",
        "data": function ( d ) {
            d.name = name;
        }
    },
    "columns": [
        { "data": "userid" },

        { "data": "name" },

        { "data": "department" },
        { "data": "position" },
        { "data": "mobile" },

        { "data": "is_join_text" },
        { "render": function(a,a,row){
                var msg = row.is_join ==0?"参加考核":"不参加考核";
                var is_join = row.is_join ==0 ? 1:0;
                var ret ='<button class="btn btn-primary size-S radius" onclick="edit(\''+row.userid+'\','+is_join+')">'+msg+'</button>';
                var msg = row.manage ==0?"标记总监":"取消总监标记";
                var manage = row.manage ==0 ? 1:0;
                ret += '&nbsp;<button class="btn btn-primary size-S radius" onclick="etagg(\''+row.userid+'\','+manage+')">'+msg+'</button>';
                return ret;
            }
        },
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
function syn_all(){
    layer.confirm('点击确认将会更新所有员工数据，会自动添加缺少的员工数据，但不会删除企业号中不存在的数据。', {icon: 3, title:'确认要更新所有员工数据吗？'}, function(index){
        var ld = layer.load(1, { shade: [0.1,'#000'] });
        layer.close(index);
        $.get('<?php echo base_url();?>index.php/performance/admin/ajax_all', function(data){
            layer.close(ld);
            if(data=='1'){
                layer.alert('更新成功', {icon: 1});
                table.ajax.reload();
            }else{
                layer.alert('更新失败，从企业号获取数据失败', {icon: 2});
            }
        });
    });
}

function edit(id,is_join){
    var url="<?php echo site_url("performance/Admin/setStatus");?>";
    var param ={
        userid:id,
        is_join:is_join,
    };
   $.post(url,param,function(data){
       data = eval('('+data+')');
       if(data.code == 0){
           layer.alert('设置成功', {icon: 1});
           table.ajax.reload();
       }else{
           layer.alert('设置失败', {icon: 2});
       }
   });
}

function etagg(id,manage){
    var url="<?php echo site_url("performance/Admin/setManage");?>";
    var param ={
        userid:id,
        manage:manage,
    };
    $.post(url,param,function(data){
        data = eval('('+data+')');
        if(data.code == 0){
            layer.alert('设置成功', {icon: 1});
            table.ajax.reload();
        }else{
            layer.alert('设置失败', {icon: 2});
        }
    });
}

</script> 
</body>
</html>