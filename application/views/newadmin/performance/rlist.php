<?php 
include "application/views/newadmin/_header.php";
?>
<title>绩效考核-统计</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 绩效考核 <span class="c-gray en">&gt;</span> 统计 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c"> 

        <input type="text" id="name" placeholder ="请输入员工编号、姓名 搜索" class="input-text" style="width:150px;">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 筛选</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
    <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
            <a class="btn btn-primary radius" data-title="导出当前excel" onclick="outexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出当前excel</a></span>
            <a class="btn btn-primary radius" data-title="导出当前excel" onclick="outdexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出明细excel</a></span>
    </div>
    <div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
				    <th>员工编号</th>
					<th>员工姓名</th>
                    <th>总人数</th>
                    <th>结果</th>
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
        "url": "<?php echo base_url();?>index.php/performance/admin/rListAjax",
        "type": "POST",
        "data": function ( d ) {
            d.departid = departid;
            d.name = name;
        }
    },
    "columns": [
        { "data": "userid" },

        { "data": "name" },
        { "data": "person_count" },

        { "render": function(a,a,row){
               var str='<table class="table">' +
                   '<tr>' +
                   '<td rowspan="2">员工</td>' +
                   '<td>专业能力成果</td>' +
                   '<td>'+row.y_one_nums +'人</td>' +
                   '<td>'+row.y_one_score_detail+row.y_one_score +'分</td>' +
                   '<td>'+row.y_one_avg+' 分</td>' +
                   '<td rowspan="2">'+row.y_zscore+'分</td>' +
                   '</tr>' +
                   '<tr>' +
                   '<td>职业素养表现</td>' +
                   '<td>'+row.y_two_nums +'人</td>' +
                   '<td>'+row.y_two_score_detail+row.y_two_score +'分</td>' +
                   '<td>'+row.y_two_avg+'分</td>' +
                   '</tr>' +
                   '<tr>' +
                   '<td rowspan="2">主管</td>' +
                   '<td>专业能力成果</td>' +
                   '<td>'+row.m_one_nums +'人</td>' +
                   '<td>'+row.m_one_score_detail+row.m_one_score +'分</td>' +
                   '<td>'+row.m_one_avg +'分</td>' +
                   '<td rowspan="2">'+row.m_zscore +'分</td>' +
                   '</tr>' +
                   '<tr>' +
                   '<td>职业素养表现</td>' +
                   '<td>'+row.m_two_nums +'人</td>' +
                   '<td>'+row.m_two_score_detail+row.m_two_score +'分</td>' +
                   '<td>'+row.m_two_avg +'分</td>' +
                   '</tr>' +
                   '</table>';
               return str;
            }
        },
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
function outexcel(){
    window.location.href="<?php echo site_url('performance/admin/out');?>";
}
function outdexcel(){
    window.location.href="<?php echo site_url('performance/admin/out_dateil');?>";
}

</script> 
</body>
</html>