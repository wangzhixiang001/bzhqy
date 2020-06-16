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
	 .mySelect{
            position: relative;
        }
        .mySelect .inputWrap{
            width:100%;
            min-height: 40px;
            border: 1px solid #ccc;
            border-radius: 3px;
            position: relative;
            cursor: pointer;
        }
        .mySelect ul{
            padding:0 5px ;
            margin: 0;
            padding-right: 35px;
        }
        .mySelect ul,li{
            list-style: none;
        }
        .mySelect li{
            display: inline-block;
            background: #eaeaea;
            padding: 5px;
            margin: 5px 5px 5px 0;
            border-radius: 5px;
        }
        .mySelect .fa-close{
            cursor: pointer;
        }
        .mySelect .fa-close:hover{
            color: #237eff;
        }
        .mySelect .mySelect-option{
            width: 100%;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: scroll;
            position: absolute;
            z-index: 1023;
            background-color: beige;
            height: 0;
            opacity: 0;
        }
        .mySelect .mySelect-option div{
            padding: 10px;
        }
        .mySelect .inputWrap>i{
            position: absolute;
            padding: 13px;
            right: 0;
            top: 0;
        }
        .mySelect-option div{
            cursor: pointer;
            border-bottom: 1px solid #e7e7e7;
            margin: 5px;
        }
        .mySelect-option div i{
            float: right;
            color: #ffffff;
        }
        .mySelect-option div.selected{
            background: #237eff;
            color: #ffffff;
            border-radius: 5px;
        }
        .mySelect-option div:hover{
            /*background: #9ec6ff;*/
            color: #9ec6ff;
            border-bottom: 1px solid #9ec6ff;
        }
</style>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 项目管理 <span class="c-gray en">&gt;</span>项目列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
	<div class="text-c">
	    日期范围：
		<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;" value="<?php echo $begin; ?>">
		-
		<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;" value="<?php echo $end; ?>">

		<input type="text" id="search" placeholder ="请输入姓名/项目名/单号 搜索" class="input-text" style="width:150px;">

		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
		<span class="select-box inline">
    		<select id="search_cus" name="search_cus" class="select">
    				<option value="0">客户筛选</option>
					 <?php foreach ($cus as $item): ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->cus_name; ?></option>
					 <?php endforeach;?>
    		</select>
		</span>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20">
	<span class="l">
	 	<a class="btn btn-success radius" data-title="新增项目"  onclick="edit()" href="javascript:;"><i class="Hui-iconfont">&#xe665;</i> 新增项目</a>
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
					<th>所属客户</th>
				    <th>项目名称</th>
				    <th>项目单号</th>
                    <th>项目金额</th>
					<th>描述</th>
					<th>创建人</th>
					<th>创建时间</th>
					<th>报销金额</th>
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
	<form action="" method="post" class="form form-horizontal" id="form-article-add" style="display: none;margin-left:50px">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目创建人：</label>
			<select id="userid" name="userid" autocomplete="off" class="chosen-select" data-placeholder="项目创建人" required >
                    <option value="0" >请选择项目创建人</option>
					<?php foreach ($list as $item): ?>
                    <option value="<?php echo $item->value ?>" ><?php echo $item->label ?></option>
					<?php endforeach;?>
        	</select>

		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目名称</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="pmpname" name="pmpname" aria-required="true" aria-invalid="true">
			</div>
		</div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目金额</label>
            <div class="formControls col-xs-8 col-sm-3">
                <input type="text" class="input-text" value="" placeholder="" id="pmmoney" name="pmmoney" aria-required="true" aria-invalid="true">
            </div>
        </div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">项目单号：</label>
			<div class="formControls col-xs-8 col-sm-3">
				<input type="text" class="input-text" value="" placeholder="" id="pmpcode" name="pmpcode" >
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
				<select name="cus_id" id="cus_id" class="select" required>
					<option value="0">请选择</option>
					 <?php foreach ($cus as $item): ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->cus_name; ?></option>
					 <?php endforeach;?>
				</select>
				</span>
			 </div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>项目参与人员：</label>
			<select id="pla_id" name="pla_id"  autocomplete="off" class="chosen-select" data-placeholder="请选择项目参与人员"  multiple="multiple" required>
                    <option value="0" >请选择项目参与人员</option>
					<?php foreach ($list as $item): ?>
                    <option value="<?php echo $item->value ?>" ><?php echo $item->label ?></option>
					<?php endforeach;?>
        	</select>
		</div>
		<input type="hidden" class="input-text" value=""  id="id" name="id">
	</form>

<script type="text/javascript" src="style/newadmin/lib/jquery/jquery.min.js"></script>
<!-- <script type="text/javascript" src="style/newadmin/lib/jquery/select.js"></script> -->
<!-- <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script> -->
<script type="text/javascript" src="style/newadmin/lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script>
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="style/newadmin/lib/jqselect/chosen.jquery.js"></script>
<script>
	var pla_id= '';
	 $(".chosen-select").chosen(
	        {
	            no_results_text: "没有搜索结果!",
	            search_contains:true,
	        }
       );

	 	$(document).ready(function(){

            chose_get_ini('#pla_id');
            //change 事件
            $('#pla_id').change(function(){

                 pla_id =chose_get_value('#pla_id');


            });
        });
        //select 数据同步
        function chose_get_ini(select){
            $(select).chosen().change(function(){$(select).trigger("liszt:updated");});
        }
        //select value获取
        function chose_get_value(select){
            return $(select).val();
        }
        //select text获取，多选时请注意
        function chose_get_text(select){
            return $(select+" option:selected").text();
        }


</script>
<script type="text/javascript">
var type="";
var text="";
var is_pay="";
var is_vioce="";
var pmpmoney="";
var search_cus='';
var pmid ='';

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
        "url": "<?php echo base_url(); ?>index.php/pmp/pmplist/ajaxpmp",
        "type": "POST",
        "data": function ( d ) {
            d.begin = begin;
            d.end = end;
            d.type=type;
            d.text=text;
            d.is_pay = is_pay;
            d.pmpmoney=pmpmoney;
            d.is_vioce= is_vioce;
            d.search_cus=search_cus;
        }
    },
    "columns": [
    	{ "data": "id" },
    	{ "data": "cus_name" },
        { "data": "pmpname" },
         { "render": function(a,a,row) { return row.pmpcode =='' ||row.pmpcode==0 ? '<span class=" size-S radius" onclick="add_code('+row.id+')" style="color:#3BB4F2;cursor:pointer;">请填写单号</span>' :row.pmpcode} },
        { "data": "pmmoney" },
        { "data": "pmpdesc" },
        { "data": "name" },
        { "data": "ctime" },
        { "render": function(a,a,row) { return '¥  '+row.all_pay/100 } },
        { "render": function(a,a,row) { return '<button class="btn btn-success size-S radius" onclick="get_pla('+row.id+')">参与人员</button>&nbsp;<button class="btn btn-success size-S radius" onclick="check_all('+row.id+')">报销统计</button>&nbsp;<button class="btn btn-primary size-S radius" onclick="edit('+row.id+',\''+row.cus_name+'\')">编辑</button>&nbsp;<button class="btn btn-danger size-S radius" onclick="ajax_del('+row.id+')">删除</button>'} }
    ]
});
//客户搜索
$('#search_cus').change(function(event) {
	begin=$('#logmin').val();
	end=$('#logmax').val();
	type=$('#type').val();
	text = $('#search').val();
	is_pay = $('#pay').val();
	is_vioce = $('#vioce').val();
	pmpmoney=$('#pmpmoney').val();
	search_cus=$(this).val();
	table.ajax.reload();
});
function filter(){
	begin=$('#logmin').val();
	end=$('#logmax').val();
	type=$('#type').val();
	text = $('#search').val();
	is_pay = $('#pay').val();
	is_vioce = $('#vioce').val();
	pmpmoney=$('#pmpmoney').val();
	table.ajax.reload();
}
function reset(){
	begin='<?php echo $begin; ?>';
	end='<?php echo $end; ?>';
	$('#logmin').val(begin);
	$('#logmax').val(end);
	pmpmoney=type=text="";
	$('#search').val('');
	$('#pay').val(0);
	$('#vioce').val(0);
	$('#search_cus').val(0);
	is_pay = $('#pay').val();
	is_vioce = $('#vioce').val();
	search_cus=$('#search_cus').val();
	table.ajax.reload();
}
function outexcel(){
	window.location.href="<?php echo base_url(); ?>index.php/dailymeal/admin/out?begin="+begin+"&end="+end;
}
//编辑
function edit(id=0){
	if (id== 0) {
		var title = '新增项目';

	}else{
		var title= '编辑项目';

	}
	// var ld = layer.load(1, { shade: [0.1,'#000'] });
	// layer.close(ld);

	//Ajax获取
	$.get('<?php echo base_url(); ?>index.php/pmp/pmplist/edit/'+id,function(res){
		  layer.open({
		  	title :title,
		    type: 1,
		    area: ['600px', '700px'],
		    content:  $('#form-article-add'),
		    btn: ['提交', '重置']
			  ,yes: function(index, layero){
			  	var data = get_data('#form-article-add');
			  	data.pla_id = chose_get_value('#pla_id');
				$.post('<?php echo base_url(); ?>index.php/pmp/pmplist/ajax_edit',{data:data}, function(d){
					 if(d=='1'){
						layer.alert('更新成功', {icon: 1});
						table.ajax.reload();
						$('#pmpname').val('');$('#pmpdesc').val('');$('#cus_id').val(0);
						 layer.close(index);
						}else{
							layer.alert('更新失败!', {icon: 2});
							layer.close(index);
						}
					},"json");
			  }
			  ,btn2: function(index, layero){
			    $('#pmpname').val('');$('#pmpdesc').val('');$('#cus_id').val(0);
			  },cancel: function(){
			    $(":input","#form-article-add").not(":button",":reset","submit").val("");
			   	window.location.reload()
			  }
		  });

		  if (res.list!=null) {
		  	pmid= res.list.id;
		  	$('#pmpname').val(res.list.pmpname);
		  	$('#pmmoney').val(res.list.pmmoney);
		  	 $('#pmpcode').val(res.list.pmpcode);$('#playtype').val(res.list.playtype);
		  	 $('#is_pay').val(res.list.is_pay);$('#is_vioce').val(res.list.is_vioce);
		  	 $('#pmpdesc').val(res.list.pmpdesc);$('#cus_id').val(res.list.cus_id);
		  	 $('#id').val(res.list.id);
		  	 $("#userid option[value='"+res.list.userid+"']").attr("selected","selected");
		  	 $('#userid').trigger('chosen:updated');//更新选项
		  	 for (var i = 0; i < res.pla_id.length; i++) {
		  	 	$("#pla_id option[value='"+res.pla_id[i]+"']").attr("selected","selected");
		  	 }

            $('#pla_id').trigger('chosen:updated');//更新选项


		  }
			$('#form-article-add').show();
	},'json');


}
//参与人员编辑


	$('#pla_id').on('change', function(e, params) {
			$.post('<?php echo base_url(); ?>index.php/pmp/pmplist/del_pla',{userid:params.deselected,pmid:pmid},function(res){
				console.log(res)
				if (res=='1') {
					layer.alert('取消参与人员成功', {icon: 1});
				}
			},'json');

	});

//获取form表单的数据
function get_data(argument) {
	var params = $(argument).serializeArray();
		 var values = {};
		 for( x in params ){
		 	values[params[x].name] = params[x].value;
		 }
		 // return JSON.stringify(values)
		 return values
}
//填写单号
function add_code(id){
	layer.prompt({
	    title: '填写单号'
	}, function(pmpcode){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmplist/ajax_code/'+id,
            data:{'id':id,'pmpcode':pmpcode},
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
//参与人员
function get_pla(id) {
	 $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmplist/get_pla/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(res){
            	var tr = new Array();
            	for (var i = 0; i < res.num; i++) {
            		var td='<tr class="text-c"><td>'+res.list[i].dname+'</td><td>'+res.list[i].uname+'</td></tr>';
            		tr.push(td);

            	}

            	var str ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>参与人员</th><th>姓名</th></tr></thead><tbody>'+tr.join('')+'</tbody></table>';
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
//查看统计
function check_all(id) {
	$.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmplist/check_all/'+id,
            data:{},
            type:"Post",
            dataType:"json",
            success:function(res){
            	var tr = new Array();
            	for (var i = 0; i < res.list.length; i++) {
				 var td='<tr class="text-c"><td>'+res.list[i].uname+'</td><td> ¥ '+res.list[i].money/100+'</td></tr>';
					tr.push(td);
            	}
            	var str1 ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>报销人员明细</th><th>金额</th></tr></thead><tbody>'+tr.join('')+'</tbody></table>';
            	var tr2 = new Array();
            	for (var i = 0; i < res.userlist.length; i++) {
				 var td2='<tr class="text-c"><td>'+res.userlist[i].uname+'</td><td> ¥ '+res.userlist[i].num_user/100+'</td></tr>';
					tr2.push(td2);
            	}
            	var str2 ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>报销人员汇总</th><th>金额</th></tr></thead><tbody>'+tr2.join('')+'</tbody></table>';
            	var tr3 = new Array();

				 var td3='<tr class="text-c"><td> ¥ '+res.allnum[0].allnum/100+'</td></tr>';
					tr3.push(td3);

            	var str3 ='<table class="table table-border table-bordered"><thead class="text-c"><tr><th>项目总金额</th></tr></thead><tbody>'+tr3.join('')+'</tbody></table>';
				  layer.open({
					  type: 1,
					  title:'报销金额',
					  skin: 'layui-layer-rim', //加上边框
					  area: ['620px', '640px'], //宽高
					  content: str3+str2+str1
					});
            },
            error:function(data){

            }
        });
}
//删除
function ajax_del(id) {
	layer.confirm('是否要删除?', {icon: 3, title:'删除'}, function(data){
    var ld = layer.load(1, { shade: [0.1,'#000'] });
     $.ajax({
            url:'<?php echo base_url(); ?>index.php/pmp/pmplist/ajax_del/'+id,
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