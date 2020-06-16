<?php 
include "application/views/newadmin/_header.php";
?>
<link rel="stylesheet" href="style/newadmin/lib/zTree/v3/css/metroStyle/zTreeStyle.css" type="text/css">
<title>用户列表</title>
</head>
<body>

<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户管理 <span class="c-gray en">&gt;</span> 用户列表 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    
<div class="pos-a" style="margin-top:40px;width:150px;left:0;top:0; bottom:0; height:100%; border-right:1px solid #e5e5e5; background-color:#f5f5f5">
	<div class="breadcrumb"><i class="icon Hui-iconfont">&#xe643;</i>&nbsp;&nbsp;&nbsp;组织框架</div>
	<ul id="treeDemo" class="ztree">
	</ul>
</div>
    <div style="margin-left:150px;">
	<div class="text-c"> 
	    搜索：
	    <span class="select-box inline">
    		<select id="type" class="select">
    			<option value="0">员工编号</option>
    			<option value="1">姓名</option>
    			<option value="2">电话</option>
    			<option value="3">微信号</option>
    		</select>
		</span>
		<input type="text" id="search" class="input-text" style="width:150px;">
		<button class="btn btn-success" type="button" onclick="filter();"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
		<button class="btn btn-secondary" type="button" onclick="reset();"><i class="Hui-iconfont">&#xe66b;</i> 取消</button>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> 
	   <span class="l">
    	   <a class="btn btn-primary radius" data-title="输入员工号更新或新增员工数据" onclick="syn_one()" href="javascript:;"><i class="Hui-iconfont">&#xe60a;</i> 更新单个用户</a>
    	   <a class="btn btn-primary radius" data-title="将现有员工的数据与微信企业号同步，不会移除企业号中删除的员工数据" onclick="syn_all()" href="javascript:;"><i class="Hui-iconfont">&#xe62b;</i> 更新所有用户</a>
    	   <a class="btn btn-danger radius" data-title="重新导入员工数据，与企业号保持完全同步，将会删除掉企业号中不存在的员工" onclick="syn_reset()" href="javascript:;"><i class="Hui-iconfont">&#xe641;</i> 重新同步用户</a>
    	   <a class="btn btn-danger radius" data-title="重新导入部门信息" onclick="syn_dp()" href="javascript:;"><i class="Hui-iconfont">&#xe643;</i> 重新同步部门</a>
	   </span>
	</div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th>员工编号</th>
					<th>姓名</th>
					<th>电话</th>
					<th>微信号</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<tr class="text-c">
					<td colspan="5">正在加载……</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="style/newadmin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="style/newadmin/lib/layer/2.1/layer.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.js"></script> 
<script type="text/javascript" src="style/newadmin/js/H-ui.admin.js"></script>
<script type="text/javascript" src="style/newadmin/lib/zTree/v3/js/jquery.ztree.core-3.5.min.js"></script> 
<script type="text/javascript">
var type="";
var text="";
var dp=1;
var table = $('.table-sort').DataTable({
	"searching": false,
    "ordering":  false,
    "serverSide": true,
    "bAutoWidth": false,
    "ajax": {
        "url": "<?php echo base_url();?>index.php/usermanage/userlist/ajaxuserlist",
        "type": "POST",
        "data": function ( d ) {
            d.dp=dp;
            d.type = type;
            d.text = text;
        }
    },
    "columns": [
        { "data": "userid" },
        { "data": "name" },
        { "data": "mobile" },
        { "data": "weixinid" },
        { "render": function(a,a,row) { return '<button class="btn btn-primary size-S radius" onclick="showmore('+row.userid+')">查看详情</button>&nbsp;<button class="btn btn-primary size-S radius" onclick="sendmsg('+row.userid+')">发消息</button>'; } }
    ]
});
function filter(){
	type=$('#type').val();
	text=$('#search').val();
	table.ajax.reload();
}
function reset(){
	type=text="";
	$('#search').val('');
	table.ajax.reload();
}
function showmore(userid){
	layer_show('用户详情','<?php echo base_url();?>index.php/usermanage/userlist/userinfo/'+userid,360,400);
}
function sendmsg(userid){
	alert('制作中');
}
var zNodes=<?php echo json_encode($list);?>;
var setting = {
		view: {
			selectedMulti: false
		},
		data: {
			simpleData: {
				enable:true,
				idKey: "id",
				pIdKey: "pId",
				rootPId: 1
			}
		},
		callback: {
			onClick: function(event, treeId, treeNode) {
				if(treeNode&&dp!=treeNode.id){
					dp=treeNode.id;
					table.ajax.reload();
				}
			}
		}
	};
$(document).ready(function(){
	zNodes[0].open=true;
	var t = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
	t.selectNode(t.getNodeByParam("id",'1'));
	layer.config({
	    extend: 'extend/layer.ext.js'
	});
});
function syn_one(){
	layer.prompt({
	    title: '请输入需要更新资料的员工编号并确认'
	}, function(userid){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		$.get('<?php echo base_url();?>index.php/usermanage/userlist/ajax_one/'+userid, function(data){
			layer.close(ld);
			if(data=='1'){
				layer.alert('更新成功', {icon: 1});
				table.ajax.reload();
			}else{
				layer.alert('更新失败，请确认员工号是否正确', {icon: 2});
			}
		});
	});
}
function syn_all(){
	layer.confirm('点击确认将会更新所有员工数据，会自动添加缺少的员工数据，但不会删除企业号中不存在的数据。', {icon: 3, title:'确认要更新所有员工数据吗？'}, function(index){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		layer.close(index);
		$.get('<?php echo base_url();?>index.php/usermanage/userlist/ajax_all', function(data){
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
function syn_reset(){
	layer.confirm('点击确认将会完全同步员工数据。注意！企业号中不存在的员工数据将会被删除。', {icon: 0, title:'确认要完全同步员工数据吗？'}, function(index){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		layer.close(index);
		$.get('<?php echo base_url();?>index.php/usermanage/userlist/ajax_syn', function(data){
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
function syn_dp(){
	layer.confirm('点击确认将会开始同步部门信息。', {icon: 0, title:'确认要完全同部门信息吗？'}, function(index){
		var ld = layer.load(1, { shade: [0.1,'#000'] });
		layer.close(index);
		$.get('<?php echo base_url();?>index.php/usermanage/userlist/ajax_syndp', function(data){
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
</script> 
</body>
</html>