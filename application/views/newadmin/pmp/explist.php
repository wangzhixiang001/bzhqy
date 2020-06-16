<?php
include "application/views/newadmin/_header.php";
$begin = date('2017-01-01');
$end = date('Y-m-d');
?>
<title>报销记录</title>

</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 报销记录 <span class="c-gray en">&gt;</span>记录列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c">
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin; ?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end; ?>">

		<input type="text" id="search" placeholder ="请输入姓名/项目名/单号 搜索" class="input-text" style="width:150px;">
		<span class="select-box inline">
    		<select id="pay" class="select">
    			<option value="2">是否付款</option>
    			<option value="0">未付款</option>
    			<option value="1">已付款</option>
    		</select>
		</span>
		<span class="select-box inline">
    		<select id="vioce" class="select">
    			<option value="0">有无票据</option>
    			<option value="1">无票据</option>
    			<option value="2">有票据</option>
    		</select>
		</span>
		<span class="select-box inline">
    		<select id="status" class="select">
    			<option value="4">审核状态</option>
    			<option value="0">未审核</option>
    			<option value="1">部门审核</option>
    			<option value="2">最终审核</option>
    			<option value="-1">驳回</option>
    		</select>
		</span>
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
        <span class="select-box inline">
            <select id="search_cus" name="search_cus" class="select">
                    <option value="0">客户筛选</option>
                     <?php foreach ($cus as $item): ?>
                    <option value="<?php echo $item->id; ?>"><?php echo $item->cus_name; ?></option>
                     <?php endforeach;?>
                <!-- <option value="4">报销类型</option> -->
            </select>
        </span>
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
                    <th>所属客户</th>
				    <th>项目名称</th>
				    <th>项目单号</th>
				    <th>项目描述</th>
                    <th>报销申请人id</th>
                    <th>报销申请人</th>
					<!-- <th>参与人员</th> -->
				    <th>报销类型</th>
				    <th>报销事由</th>
				    <th>报销金额</th>
                    <th>申请时间</th>
				    <th>票据</th>
                    <th>审核</th>
					<th>付款</th>



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
	<form action="" method="post" class="form form-horizontal" id="form-article-add" style="display: none;margin-left:50px">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目创建人：</label>
			<div id="useridSelect" class="mySelect" style="width: 250px;float: left"></div>
			<input type="hidden" class="input-text" value=""  id="userid" name="userid">
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目名称</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="pmpname" name="pmpname">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">项目单号：</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="pmpcode" name="pmpcode">
			</div>
		</div>
		<!-- <div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目金额：</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="pmpcode" name="pmpcode">
			</div>
		</div> -->
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>报销类型：</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="playtype" name="playtype">
				<select  id="is_type" class="select" style="display: none">
					<option value="0">请选择</option>
					 <?php foreach ($btype as $item): ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->typename; ?></option>
					 <?php endforeach;?>
				</select>
			</div>

		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>付款情况：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box " style="width: 300px">
				<select name="is_pay" id="is_pay" class="select" >
					<option value="0">请选择</option>
					<option value="1">是</option>
                    <option value="-1">否</option>
				</select>
				</span>
			 </div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>是否开发票：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box " style="width: 300px">
				<select name="is_vioce" id="is_vioce" class="select" >
					<option value="0">请选择</option>
					 <option value="1">是</option>
                    <option value="-1">否</option>
				</select>
				</span>
			 </div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">项目摘要：</label>
			<div class="formControls col-xs-8 col-sm-3">
				<textarea  id ="pmpdesc" name="pmpdesc" cols="" rows="" class="textarea"  placeholder="项目主要描述" ></textarea>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>所属客户：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box " style="width: 300px">
				<select name="cus_id" id="cus_id" class="select" >
					<option value="0">请选择</option>
					 <?php foreach ($cus as $item): ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->cus_name; ?></option>
					 <?php endforeach;?>
				</select>
				</span>
			 </div>

		</div>
		<div class="row cl" >
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目参与人员：</label>
			<div id="mySelect" class="mySelect" style="width: 250px;float: left"></div>
			<input type="hidden" class="input-text" value=""  id="pla_id" name="pla_id">
		</div>
		<input type="hidden" class="input-text" value=""  id="id" name="id">
	</form>

<script type="text/javascript" src="style/newadmin/lib/jquery/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/jquery/select.js"></script>
<!-- <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script> -->
<script type="text/javascript" src="style/newadmin/lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>

<script type="text/javascript">
    $(function(){
       mySelect= $("#mySelect").mySelect({
          mult:true,//true为多选,false为单选
          option:<?php echo json_encode($list, JSON_UNESCAPED_UNICODE); ?>,
          onChange:function(res){//选择框值变化返回结果
          	$('#pla_id').val(res);

          }
      });
      mySelect.setResult([]);
       useridSelect= $("#useridSelect").mySelect({
          mult:false,//true为多选,false为单选
          option:<?php echo json_encode($list, JSON_UNESCAPED_UNICODE); ?>,
          onChange:function(res){//选择框值变化返回结果
          	$('#userid').val(res);

          }
      });
      useridSelect.setResult([]);

    })
    //报销类型s
    $("#playtype").hover(function (){
            $("#is_type").show();
        },function (){
        	$("#is_type").change(function(){
        		var s=$("#is_type option:selected").text();
			    $("#playtype").val(s);
			    $("#is_type").hide();
			});
        	setTimeout(function(){//两秒后跳转
			     $("#is_type").hide();
			  },4000);
        });

    $("#playtype").on('input',function(e){
       $("#is_type").hide();
    });

//报销类型e
</script>
<script type="text/javascript">
var type="";
var text="";
var is_pay="2";
var has_invoice="";
var status="4";
var search_cus='';
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
        "url": "<?php echo base_url(); ?>index.php/pmp/pmpexp/ajaxpmp",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
            d.type=type;
            d.text=text;
            d.is_pay = is_pay;
            d.has_invoice= has_invoice;
            d.status=status;
            d.search_cus=search_cus;
        }
    },
    "columns": [
    	{ "data": "id" },
    	{ "data": "cus_name" },
        { "data": "pmpname" },
        { "data": "pmpcode" },
        { "data": "pmpdesc" },
        { "data": "userid" },
        { "data": "uname" },
        // { "render": function(a,a,row) { return '<button class="btn btn-success size-S radius" onclick="get_pla('+row.product_id+')">查看参与人员</button>'; } },
        { "data": "typename" },
        { "data": "cause" },
        { "render": function(a,a,row) { return '¥  '+row.money/100 } },
        { "data": "ac_time" },
       { "render": function(a,a,row) { return row.has_invoice ==2 ? '<span class=" size-S radius" onclick="check_bmp('+row.id+') " style="color:#5A98DE;cursor:pointer;">有票据</span>' :'无票据'} },
       { "render": function(a,a,row) { return row.status ==-1? '已驳回': row.status==2 ?'领导已审核' : row.status ==1 ?'部门已审核':'未审核' } },
        { "render": function(a,a,row) { return row.is_pay == 1 ? '已付款' : '未付款' } },

        // { "render": function(a,a,row) { return '<button class="btn btn-primary size-S radius" onclick="edit('+row.id+',\''+row.cus_name+'\')">编辑</button>&nbsp;<button class="btn btn-danger size-S radius" onclick="ajax_del('+row.id+')">删除</button>'; } }
    ]
});
//客户搜索
$('#search_cus').change(function(event) {
    begin=$('#logmin').val();
    end=$('#logmax').val();
    type=$('#type').val();
    text = $('#search').val();
    is_pay = $('#pay').val();
    has_invoice = $('#vioce').val();
    status = $('#status').val();
    search_cus=$(this).val();
    table.ajax.reload();
});
function filter(){
	begin=$('#logmin').val();
	end=$('#logmax').val();
	type=$('#type').val();
	text = $('#search').val();
	is_pay = $('#pay').val();
	has_invoice = $('#vioce').val();
	status = $('#status').val();
	table.ajax.reload();
}
function reset(){
	begin='<?php echo $begin; ?>';
	end='<?php echo $end; ?>';
	$('#logmin').val(begin);
	$('#logmax').val(end);
	type=text="";
	$('#search').val('');
	$('#pay').val(2);
	$('#vioce').val(0);
	$('#status').val(4);
    $('#search_cus').val(0);
	is_pay = $('#pay').val();
	is_vioce = $('#vioce').val();
	status = $('#status').val();
    search_cus=$('#search_cus').val();
	table.ajax.reload();
}
function outexcel(){
	window.location.href="<?php echo base_url(); ?>index.php/pmp/pmpexp/out?begin="+begin+"&end="+end+"&type="+type+"&text="+text+"&is_pay="+is_pay+"&has_invoice="+has_invoice+"&status="+status;
}

//获取form表单的数据
function get_data(argument) {
	var params = $(argument).serializeArray();
		 var values = {};
		 for( x in params ){
		 	values[params[x].name] = params[x].value;
		 }
		 return JSON.stringify(values)

}

//查看参与人员
function get_pla(id) {
	 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmpexp/get_pla/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(res){
            	var tr = new Array();
            	for (var i = 0; i < res.num; i++) {
            		var td='<tr class="text-c"><td>'+res.list[i].dname+'</td><td>'+res.list[i].uname+'</td></tr>';
            		tr.push(td);

            	}

               var str ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>所属部门</th><th>姓名</th></tr></thead><tbody>'+tr.join('')+'</tbody></table>';
				  layer.open({
					  type: 1,
					  title:'参与人员',
					  skin: 'layui-layer-rim', //加上边框
					  area: ['420px', '240px'], //宽高
					  content: str
					});
            },
            error:function(data){

            }
        });

}
//票据
function check_bmp(id) {
	 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmpexp/check_bmp/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(res){

            	var tr = new Array();
            	for (var i = 0; i < res.list.length; i++) {
            		var td='<tr class="text-c"><td><img style="width:300px;height:200px" src="<?php echo base_url(); ?>'+res.list[i]+'" alt=""></td></tr>';
            		tr.push(td);

            	}
               var str ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>票据</th></tr></thead><tbody>'+tr.join('')+'</tbody></table>';
				  layer.open({
					  type: 1,
					  title:'票据',
					  skin: 'layui-layer-rim', //加上边框
					  area: ['600px', '600px'], //宽高
					  content: str
					});
            },
            error:function(data){

            }
        });
}
//支付
function send_pay(userid,id) {
      layer.open({
            title :'是否付款',
            type: 1,
            content: '',
            btn: ['确认', '取消']
              ,yes: function(index, layero){
                $.ajax({
                    url:'<?php echo base_url(); ?>index.php/pmp/pmpexp/send_pay/'+userid+'/'+id,
                    data:{},
                    type:"Post",
                    dataType:"json",
                    success:function(res){
                        if(res=='1'){
                        layer.alert('付款成功', {icon: 1});
                        table.ajax.reload();
                        }else{
                            layer.alert('付款失败!', {icon: 2});
                        }
                    },
            error:function(data){

            }
        });
              }
              ,btn2: function(index, layero){
                layer.close(index);
              },cancel: function(index, layero){
                 layer.close(index);
              }
          });

}

//删除
function ajax_del(id) {
	layer.confirm('是否要删除?', {icon: 3, title:'删除'}, function(data){
    var ld = layer.load(1, { shade: [0.1,'#000'] });
     $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmpexp/ajax_del/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(data){

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