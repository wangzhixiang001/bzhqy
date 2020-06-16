<?php 
include "application/views/newadmin/_header.php";
?>
<title>绩效考核-账号</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 绩效考核 <span class="c-gray en">&gt;</span> 账号 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 
	    部门:
        <span class="select-box inline">
            <select name = "departid" id="departid" class="select">
                <option value="">请选择</option>
                <?php foreach($departs as $key=>$depart) { ?>
                 <option value = "<?php echo $key;?>"><?php echo $depart;?></option>
                <?php } ?>
            </select>
        </span>
        <input type="text" id="name" placeholder ="请输入账号 搜索" class="input-text" style="width:150px;">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
	   <span class="l">
    	   <a class="btn btn-primary radius" data-title="" onclick="syn_all()" href="javascript:;"><i class="Hui-iconfont">&#xe62b;</i>一键添加账号</a>
	   </span>
    </div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
				    <th>账号</th>
					<th>归属部门</th>
                    <th>使用情况</th>
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
var departid="";
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/performance/admin/uListAjax",
        "type": "POST",
        "data": function ( d ) {
            d.departid = departid;
            d.name = name;
        }
    },
    "columns": [
        { "data": "name" },

        { "data": "department" },

        { "data": "status_text" },
    ]
});
function filter(){
	departid=$('#departid').val();
    name=$('#name').val();
	table.ajax.reload();
}
function reset(){
    departid ="";
    name ="";
	$('#departid').val(departid);
    $('#name').val(name);
	table.ajax.reload();
}
function syn_all(){
    layer.confirm('点击确认将会重新生成新的账号数据，将数据初始化', {icon: 3, title:'确认要生成新的账号数据吗？'}, function(index){
        var ld = layer.load(1, { shade: [0.1,'#000'] });
        layer.close(index);
        var url="<?php echo site_url('performance/admin/addAjax'); ?>";
        $.post(url,"",function(data){
            table.ajax.reload();
            layer.close(ld);
        });
    });
}

</script> 
</body>
</html>