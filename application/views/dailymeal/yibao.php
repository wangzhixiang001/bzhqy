<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>今日订餐</title>
    <link href="style/weui/weui.min.css" rel="stylesheet" />
    <link href="style/weui/example.css" rel="stylesheet" />
</head>
<body>
<div class="page">
    <div class="hd weui_msg">
        <h1 class="page_title">今日报饭</h1>
        <p class="weui_msg_desc"><?php echo date('Y年m月d日');?> 星期<?php $arr = array('日','一','二','三','四','五','六');echo $arr[date('w')];?></p>
    </div>
    <div class="bd">
<?php
$baofan=array();
$buqian=array();
$quxiao=array();
$jiucan = 0;
$bj = 0;
foreach($list as $val){
    if ($val->type==0){
        array_push($quxiao, $val);
    }elseif ($val->type==1){
        if ($val->status ==1){
            $bj ++;
            array_push($baofan, $val);
        }elseif ($val->status ==2){
            array_push($buqian, $val);
        }else{
            array_push($baofan, $val);
        }
    }
    if ($val->status >0)$jiucan ++;
}
?>
        <div class="weui_cells_title">已就餐 <?php echo $jiucan;?>人(<?php echo $bj;?>报饭+<?php echo count($buqian);?>补签) </div>
        <div class="weui_cells_title">已报饭 <?php echo count($baofan);?>人 </div>
        <div class="weui_cells">
<?php
foreach($baofan as $val){
?>
            <div class="weui_cell">
                <div class="weui_cell_bd">
                    <img src="<?php echo $val->avatar;?>" style="width:50px;margin-right:5px;desplay:block;">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?php echo $val->name;?></p>
                </div>
                <?php if ($val->status == 1){?>
                    <div class="weui_cell_ft" style="color: #00CC00">已就餐</div>
                <?php } ?>
                <div class="weui_cell_ft"><?php echo substr($val->time, 10);?></div>
            </div>
<?php } ?>
        </div>
        <div class="weui_cells_title">已补签 <?php echo count($buqian);?>人</div>
        <div class="weui_cells">
            <?php foreach($buqian as $val){ ?>
                <div class="weui_cell">
                    <div class="weui_cell_bd">
                        <img src="<?php echo $val->avatar;?>" style="width:50px;margin-right:5px;desplay:block;">
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <p><?php echo $val->name;?></p>
                    </div>
                    <div class="weui_cell_ft" style="color: #00CC00">已就餐</div>
                    <div class="weui_cell_ft"><?php echo substr($val->eat_time, 10);?></div>
                </div>
            <?php } ?>
        </div>
        <div class="weui_cells_title">已取消 <?php echo count($quxiao);?>人</div>
        <div class="weui_cells">
<?php foreach($quxiao as $val){ ?>
            <div class="weui_cell">
                <div class="weui_cell_bd">
                    <img src="<?php echo $val->avatar;?>" style="width:50px;margin-right:5px;desplay:block;">
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p><?php echo $val->name;?></p>
                </div>
                <div class="weui_cell_ft"><?php echo substr($val->time, 10);?></div>
            </div>
<?php } ?>
        </div>
	</div>
</div>
</body>
</html>