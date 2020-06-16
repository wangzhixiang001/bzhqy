<?php
include "application/views/newadmin/_header.php";
$begin = date('2017-01-01');
$end = date('Y-m-d');
?>
<title>个人报销汇总</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 项目报销 <span class="c-gray en">&gt;</span>个人报销汇总 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="cl pd-5 bg-1 bk-gray mt-20">
    <span class="l">
        <span class="cuti" data-title="已通过审核的金额"  href="javascript:;"> 当月已审核金额 </span> ¥<span class=" cuti "><?php echo $moth_pay / 100 ?> </span>
     </span>
     <span class="l" style="padding: 0 80px 0 80px;">
        <span class="cuti" data-title="未审核金额"  href="javascript:;"> 未审核金额 </span>¥<span class=" cuti" > <?php echo $no_pay / 100 ?> </span>
     </span>
     <span class="l">
        <span class="cuti" data-title="累计报销金额"  href="javascript:;"> 累计报销金额 </span>¥<span class=" cuti"> <?php echo $all_pay / 100 ?> </span>
     </span>
    </div>
	<div class="text-c">
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin; ?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end; ?>">

		<input type="text" id="search" placeholder ="请输入姓名/部门搜索" class="input-text" style="width:150px;">


		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20">
	<!-- <span class="l">
	 	<a class="btn btn-success radius" data-title="新增项目"  onclick="edit()" href="javascript:;"><i class="Hui-iconfont">&#xe665;</i> 新增项目</a>
	 </span> -->
	 <span class="l">
	 	<a class="btn btn-primary radius" data-title="导出当前excel" onclick="outexcel()" href="javascript:;"><i class="Hui-iconfont">&#xe640;</i> 导出当前excel</a>
	 </span>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th>ID</th>
                    <th>userid</th>
				    <th>姓名</th>
                    <th>部门</th>
				    <th>已审核报销</th>
				    <th>未审核报销</th>
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

<script type="text/javascript" src="style/newadmin/lib/jquery/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/jquery/select.js"></script>
<!-- <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script> -->
<script type="text/javascript" src="style/newadmin/lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>


<script type="text/javascript">
var type="";
var text="";

var begin="<?php echo $begin; ?>";
var end="<?php echo $end; ?>";
$(document).ready(function(){
	layer.config({
	    extend: 'extend/layer.ext.js'
	});
});
var table = $('.table-sort').DataTable({
	"searching": false,
	"ordering": true,
    "serverSide": true,
    "bAutoWidth": false,
	"order": [[3, "desc"]],
    "ajax": {
        "url": "<?php echo base_url(); ?>index.php/pmp/pmpexp/ajax_one_apply",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
            d.type=type;
            d.text=text;

        }
    },
    "columns": [
    	{ "data": "id" },
    	{ "data": "userid" },
    	{ "data": "uname" },
	    {"data": "dname"},
	    {
		    "render": function (a, a, row) {
			    return row.yes_c > 0 ? '<span class=" size-S radius" onclick="list_pay( ' + row.userid + ')"   style="color:#3BB4F2;cursor:pointer;">' + '¥ ' + row.yes_c + '</span>' : '¥ ' + row.yes_c
		    }
	    },

	    {
		    "render": function (a, a, row) {
			    return '¥ ' + row.no_c
		    }
	    },
       { "render": function(a,a,row) { return row.pays == 1 ?'<span class=" size-S radius" onclick="send_pay('+row.userid+','+row.id+','+row.yes_c+')" style="color:#3BB4F2;cursor:pointer;">去付款</span>' :'未审核' } },

        // { "render": function(a,a,row) { return '<button class="btn btn-primary size-S radius" onclick="edit('+row.id+',\''+row.cus_name+'\')">编辑</button>&nbsp;<button class="btn btn-danger size-S radius" onclick="ajax_del('+row.id+')">删除</button>'; } }
    ]
});

function filter(){
	begin=$('#logmin').val();
	end=$('#logmax').val();
	type=$('#type').val();
	text = $('#search').val();

	table.ajax.reload();
}
function reset(){
	begin='<?php echo $begin; ?>';
	end='<?php echo $end; ?>';
	$('#logmin').val(begin);
	$('#logmax').val(end);
	type=text="";
	$('#search').val('');

	table.ajax.reload();
}
function outexcel(){
	window.location.href="<?php echo base_url(); ?>index.php/pmp/pmpexp/out_one_apply?begin="+begin+"&end="+end+"&text="+text;
}
//支付
function send_pay(userid,id,yes_c) {
layer.prompt({
	    title: '请输入密码',
         formType: 1,
	}, function(pass){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pay_user/check_pass',
            data:{'pass':pass},
            type:"Post",
            dataType:"json",
            success:function(data){
               layer.close(ld);
               if(data.code=='1'){
			      $.ajax({
                    url:'<?php echo base_url(); ?>index.php/pmp/pmpexp/send_pay/'+userid+'/'+id,
                    data:{'yes_c':yes_c},
                    type:"Post",
                    dataType:"json",
                    success:function(res){
                        if(res=='1'){
                        layer.alert('付款成功', {icon: 1});
                        table.ajax.reload();
                        }else{
                                layer.alert('付款失败!', {icon: 2});
                                }
                            }

                });

				}else{
					layer.alert(data.msg, {icon: 2});
				}
            },
            error:function(data){

            }
        });

	});

}
//个人报销明细
function list_pay(userid) {
     $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pay_user/list_pay',
            data:{'userid':userid},
            type:"Post",
            dataType:"json",
            success:function(res){
                    var tr = new Array();
                    for (var i = 0; i < res.length; i++) {
	                    var td = '<tr class="text-c"><td>' + res[i].cus_name + '</td><td>' + res[i].pmpname + '</td><td>' + res[i].typename + '</td><td> ¥ ' + res[i].money / 100 + '</td><td>' + res[i].happend_time + '</td></tr>';
                            tr.push(td);
                        }

                   var str ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>客户名称</th><th>项目名称</th><th>消费类型</th><th>消费金额</th><th>消费时间</th></tr></thead><tbody>'+tr.join('')+'</tbody></table>';
                      layer.open({
                          type: 1,
                          title:'个人报销明细',
                          skin: 'layui-layer-rim', //加上边框
                          area: ['420px', '240px'], //宽高
                          content: str
                        });
            },
            error:function(data){

            }
        });
}



</script>
</body>
</html>