<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="<?php echo base_url('style/punchcard') ?>/" />
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>签到日历</title>   
		
		<link rel="stylesheet" type="text/css" href="css/common.css" />
		<link rel="stylesheet" type="text/css" href="css/calendar.css" /> 
		
	</head>
	<body>
		<div id="app" class="vueBox" v-cloak>
			<div class="plan-finish-header">
				<div class="plan-finish-bg"></div>
			</div>
			<div class="plan-finish-calendar mtop1">
				<div class="calendar">
					<!-- 年份 月份 -->
					<ul class="month bottom-line">
						<!--点击会触发pickpre函数，重新刷新当前日期 -->
						<li class="arrow" @click="pickPre(currentYear,currentMonth)">
							<i class="mintuifont mintui-arrowright arrowleft"></i>
						</li>
						<li class="year-month">
							<span v-text="currentYear+'年'+currentMonth+'月'"></span>
						</li>
						<li class="arrow" @click="pickNext(currentYear,currentMonth)">
							<i class="mintuifont mintui-arrowright"></i>
						</li>
					</ul>
					<!-- 星期 -->
					<ul class="weekdays">
						<li>日</li>
						<li>一</li>
						<li>二</li>
						<li>三</li>
						<li>四</li>
						<li>五</li>
						<li>六</li>
					</ul>
					<!-- 日期 -->
					<ul class="days bottom-line">
						<li v-for="day in days" @click="dayCheck(day)">
							<!--本月已签到日期-->
							<span :day="day.day" :class="['day-li',day.isChecked?'day-checked':'']">
								<span :class="['day-span',day.isSign?'day-sign':'',day.day.getMonth()+1 !== currentMonth?'other':'',day.isSigned?'day-signed':'']"
								 v-text="day.day.getDate()"></span>
							</span>
						</li>
					</ul>
				</div>
				<!-- <div class="plan-calendar-info" v-show="currentPlan.date">
					<div class="calendar-info-date"> 
						<span v-text="currentPlan.date"></span>
					</div>
					<div class="calendar-info-title">
						<span v-text="currentPlan.title"></span>
					</div>
					<ul class="calendar-info-list">
						<li v-for="item in currentPlan.list">
							<span v-text="item.name"></span>
						</li>
					</ul>
					<div class="calendar-info-text" v-if="currentPlan.nums">
						<p v-text="currentPlan.name"></p>
						<p class="calendar-info-nums" v-text="'完成题目：'+ currentPlan.nums+'道'"></p>
					</div>
				</div> -->
			</div>
		</div>
		<script>
			var list = <?php echo json_encode($list) ?>;
			var indexDay = '<?php echo $indexDay ?>';
			var api_url = '<?php echo site_url('dailymeal/PunchCard') ?>/';
		</script>
		<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.1.1/mobile/layer.min.js"></script>
		<script src="js/vue.js"></script>
		<script src="js/resize.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/calendar.js" type="text/javascript" charset="utf-8"></script>
	</body>
</html>
