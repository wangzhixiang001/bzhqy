<!DOCTYPE html>
<html>
	<head>
	    <base href="<?php echo base_url();?>" />
		<meta charset="utf-8">
		<title>工资查询</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<!--标准mui.css-->
		<link rel="stylesheet" href="style/mui/css/mui.min.css">
		<link rel="stylesheet" href="style/mui/css/load_ui.css">
		<!--App自定义的css-->
		<link rel="stylesheet" href="style/mui/css/mui.picker.min.css">
	</head>

	<body>
		<header class="mui-bar mui-bar-nav">
			<h1 class="mui-title">工资查询</h1>
		</header>
		<div class="mui-content">
			<div class="mui-card">
				<div class="mui-card-content">
				<ul class="mui-table-view">
						<li class="mui-table-view-cell">
						查看月份：
        						<span id="month"><?php echo empty($info)?date('Y年m'):$info->year.'年'.$info->month;?>月份</span>
        						<button type="button" class="mui-btn" id="s_month">
        							<span class="mui-icon mui-icon-search"></span>
        						</button>
        					</li>
        					</ul>
				</div>
			</div>
			<div class="mui-card">
				<div class="mui-card-header"><?php echo empty($info)?date('Y年m'):$info->year.'年'.$info->month;?>月份工资信息</div>
				<div class="mui-card-content">
						<ul class="mui-table-view" id="salary_info">
        					<li class="mui-table-view-cell">基础额度<div class="mui-pull-right" data-s="basic" data-i=""></div></li>
        					<li class="mui-table-view-cell">统筹额度<div class="mui-pull-right" data-s="overall" data-i=""></div></li>
        					<li class="mui-table-view-cell">餐费<div class="mui-pull-right" data-s="food" data-i=""></div></li>
        					<li class="mui-table-view-cell">请假<div class="mui-pull-right" data-s="leave" data-i=""></div></li>
        					<li class="mui-table-view-cell">处罚<div class="mui-pull-right" data-s="punish" data-i=""></div></li>
        					<li class="mui-table-view-cell">奖励<div class="mui-pull-right" data-s="reward" data-i=""></div></li>
        				</ul>
				</div>
				<div class="mui-card-footer">
					合计：
					<strong style="color:red" class="mui-pull-right" id="all" data-i="">
					</strong>
				</div>
			</div>
		</div>
		
		<div class="mui-backdrop" id="load_layer">
            <div class="sui_toast">
                <span class="mui-spinner"></span>
                <p>正在加载</p>
            </div>
		</div>
		<script src="style/mui/js/mui.min.js"></script>
		<script src="style/mui/expand/mui.picker.min.js"></script>
		<script>
		var picker=null;
		var ym='<?php echo empty($info)?date('Y/m'):$info->year.'/'.$info->month;?>';
		function insertdate(data){
			mui('#salary_info .mui-pull-right').each(function(i,span){
				var type=span.getAttribute('data-s');
				span.innerHTML=data.hasOwnProperty(type)?data[type]:'-';
				span.setAttribute('data-i',data.hasOwnProperty(type+'_i')?data[type+'_i']:'');
			});
			document.querySelector('#all').innerHTML=data.hasOwnProperty('all')?data['all']:'-';
		}
		insertdate(<?php echo empty($info)?'{}':$this->authcode->code($info->salary,'DECODE',$info->userid);?>);
		document.getElementById('load_layer').style.display='none';
		(function($) {
			$.init();
			picker = new $.DtPicker({
				"type":"month"
			});
			document.querySelector('#s_month').addEventListener('tap', function() {
				picker.show(function(rs) {
					var cym=rs.y.value+'/'+rs.m.value;
					if(cym!=ym){
						ym=cym;
						document.getElementById('load_layer').style.display='block';
						$.get('index.php/salary/user/querysalary/'+ym,function(data){
							document.getElementById('load_layer').style.display='none';
							if(data.code==0){
								$.alert( data.msg, '提示');
							}else if(data.code==1){
								document.querySelector('.mui-card-header').innerHTML=rs.y.text+'年'+rs.m.text+'月份工资信息';
								insertdate(data.info.salary);
							}
						},'json');
					}
					document.querySelector('#month').innerText = rs.y.text+'年'+rs.m.text+'月份';
				});
			}, false);
			$('#salary_info').on('tap','li',function(){
				var children=this.childNodes;
				var text=children[1].getAttribute('data-i');
				console.log(children);
				if(text!='')
				    $.alert(text, children[0].data+'详细说明');
			});
		})(mui);
		</script>
	</body>

</html>