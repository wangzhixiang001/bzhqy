<?php
//http://127.0.0.1/CI302/index.php/dailymeal/user
class Front extends CI_Controller {

    private $departTable ="assessment_department";
    private $uTable ="assessment_user";
    private $joinUTable ="assessment_join_user";
    private $rTable ="assessment_record";
    private $oTable ="assessment_option_record";
    private $act =[];
    private $options=[];
    private $start_time="2019-01-23";
    private $end_time="2019-01-27";
    private $error="";
	private $number="";
    public function __construct() {
        //调用父类的构造函数
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library('session');
        $this->load->library('weixin/qyoauth');
        $this->config();
    }

    /**
     * 配置项
     */
    public function config(){
        $this->config->load('act');
        $acts = $this->config->item('act');
        $act = [];
        foreach($acts as &$v){
            if($v['is_on']){
                $act = $v;
                break;
            }
        }
        $this->act =$act;

        $this->options = $this->config->item('options');
    }

    public function _wap(){
        if((strtotime($this->start_time)>time())){
            $this->ajaxReturn(200, '暂未开始');
        }

        if((strtotime($this->end_time)<time())){
            $this->ajaxReturn(200, '已结束');
        }

        if(!empty($this->error)){
            $this->ajaxReturn(200, $this->error);
        }

         /*  if(isset($_COOKIE["bzh_userid"]))
		{
			$this->ajaxReturn(300,'请在企业号内打开',);
		}*/
		
		// 判断是否登录
       $account =  $this->session->userdata('ascsessment_account_23_start');
       if(empty($account)){
           $departs =  $this->departments();
           $this->ajaxReturn(400,'请先登录',['departs'=>$departs]);
       }else{
		    $name = explode('-',$account['name']);
            $this->number =$name[1].$name[2];
	   }
       return $account;
    }
    //报饭入口
    public function index(){

        $account =  $this->session->userdata('ascsessment_account_23_start');
        if(empty($account)) {
            $userid = $this->qyoauth->getUserid();

            if(empty($userid)){
                echo "<script> alert('请在企业号内打开');</script>";
                die;
            }

            $userinfo = $this->db->where('userid', $userid)->get($this->joinUTable)->row_array();

            if ($userinfo['department']) {
                // 主管
                if ($userinfo['manage'] == 1) {
                    $this->claim4();
                } else {
                    $departid = $this->parent_department($userinfo['department'], 1);
                    $this->claim3($departid);
                }
            }
        }
        redirect(base_url('wap/kh'));
    }

    public function parent_department($id,$parentid = 1){

        $department =  $this->db->where('id',$id)->get('qy_department')->row_array();
        if($department){
            if($department['parentid'] != $parentid) {
                return $this->parent_department($department['parentid'], $parentid);
            }else{
                return $id;
            }
        }else{
            return 0;
        }
    }
	
	/**
     *  认领账号
     */
    public function claim3($departid){
        //获取该部门未认领的 员工账号
        $zaccout = $this->act['name']."-".$departid.'-'.'000';
        $accounts = $this->db->where('departid',$departid)->where('status',0)->where('name !=',$zaccout)->get($this->uTable)->result_array();
        if($accounts){
            $num = count($accounts);
            $index = rand(1,$num);
            $account = $accounts[$index-1];
            $name = explode('-',$account['name']);
            $number =$name[1].$name[2];
            $status = 1;
            $staff = $this->db->where('departid',$departid)->where('staff',1)->where('name !=',$zaccout)->count_all_results($this->uTable);
            if($staff < 2 ){
                // 设置代表
                if(2*$index>$num){
                    $staff =1;
                }

            }else{
                $staff = 0;
            }
            $this->db->where('id',$account['id'])->update($this->uTable,['status'=>$status,'staff'=>$staff,'updated_at'=>date('Y-m-d H:i:s')]);
            $account = $this->accountInfo($number);
            $this->number = $number;
            if($account){
                $ud = [
                    'ascsessment_account_24_start'=>$account,
                    'number'=>$number,
                ];
                $this->session->set_userdata($ud);
            }
        }
    }

    /**
     *  认领账号
     */
    public function claim4(){
        $accounts = $this->db->where('status',0)->where('role',1)->get($this->uTable)->result_array();
        if($accounts){
            $num = count($accounts);
            $index = rand(1,$num);
            $account = $accounts[$index-1];
            $name = explode('-',$account['name']);
            $number =$name[1].$name[2];
            $status = 1;
            $this->db->where('id',$account['id'])->update($this->uTable,['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')]);
            $account = $this->accountInfo($number);
            $this->number = $number;
            if($account){
                $ud = [
                    'ascsessment_account_24_start'=>$account,
                    'number'=>$number,
                ];
                $this->session->set_userdata($ud);
            }
        }
        return $account;
    }

    /**
     *  获取初始化数据
     */
    public function init(){
        $account =  $this->_wap();
        $result = [];
        if($account['role'] == 1){
            //获取主管信息 对主管进行投票
            $personnel = $this->db->where('is_join', 1)->where('manage', 1)->get($this->joinUTable)->result_array();

            $item = array(
                'name' => "部门主管",
                'Personnel' => $personnel,
            );
            $result[] = $item;
            $depart =array();
        }else {
            if ($account['staff'] == 1) {
                //获取主管信息 对主管进行投票
                $personnel = $this->db->where('is_join', 1)->where('manage', 1)->get($this->joinUTable)->result_array();

                $item = array(
                    'name' => "部门主管",
                    'Personnel' => $personnel,
                );
                $result[] = $item;
            }
            // 获取 上下游部门
            $depart = $this->db->where('id', $account['departid'])->get($this->departTable)->row_array();
        }
        if(!empty($depart['brother_department'])){
            $departs = json_decode($depart['brother_department'],true);
            $departs = array_merge([$account['departid']],$departs);
        }else{
            $departs =  $this->db->get($this->departTable)->result_array();
            if($departs){
                $departs = array_column($departs,'id');
            }else{
                $departs = array();
            }
        }

        foreach($departs as $v){
            $item = $this->getMembers($v);
            if(!empty($item)) {
                $result[] = $item;
            }
        }
        $records = $this->db->where('uid',$account['id'])->get($this->rTable)->result_array();
        $userids = array();
        if($records){
            $userids = array_column($records,'userid');
        }
        $this->ajaxReturn(0,'',array('data'=>$result,'recodes'=>$userids,'options'=>$this->options,'number'=>$this->number));
    }

    /**
     * 提交结果
     */
    public function subAddAjax(){
        $account = $this->_wap();
        $userid = $this->input->post('userid');
        $options = $this->input->post('options');
        $remark = $this->input->post('remark');
        if(empty($remark) || empty(trim($remark))){
            $this->ajaxReturn(200,'请完善备注信息，否则视为无效！');
        }
        $one = 0;
        $score = 0;
        $items = array();
        foreach($options as $v){
            if(empty($v['score']) && $v['score']!=0){
                break;
            }else{
                $one++;
                $score+=$v['score'];
                $items[]=$v;
            }
        }
        if($one==0){
            $this->ajaxReturn(200,'提交失败！');
        }
        $insert =array(
            'uid' =>$account['id'],
            'userid'=>$userid,
            'score'=>$score,
            'status'=>1,
            'remark'=>$remark,
            'updated_at' =>date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->rTable,$insert);
        $rid = $this->db->insert_id();
        foreach($items as $v){
            $v['rid'] = $rid;
            $v['updated_at'] = date("Y-m-d H:i:s");
            $this->db->insert($this->oTable,$v);
        }

        $this->ajaxReturn(0,'已提交');
    }

    /**
     *  根据部门获取成员
     */
    public function getMembers($id){
        $departs = $this->db->where('parentid',$id)->get('qy_department')->result_array();
        $depart = $this->db->where('id',$id)->get('qy_department')->row_array();
        if($departs) {
            $group = array();
            foreach ($departs as $v) {
                $item =  $this->getMembers($v['id']);
                if(!empty($item)) {
                    $group[] = $item;
                }
            }
            $depart['group']=$group;
        }
        $Personnel= $this->db->where('department',$id)->where('is_join',1)->where('manage',0)->get($this->joinUTable)->result_array();
        $depart['Personnel']=$Personnel;

        if(empty($depart['group']) && empty($depart['Personnel']) ){
            return array();
        }

        return  $depart;
    }


    /**
     * 获取部门
     */
    public function departments(){
        $departs = $this->db->select('id,name department')->get($this->departTable)->result_array();
        if(empty($departs)){
            $departs=array();
        }
        return $departs;// $this->ajaxReturn(0,'',['departs'=>$departs]);
    }

    /**
     *  认领账号
     */
    public function claim(){
        $departid = $this->input->post('departid');
        if(empty($departid)){
            $this->ajaxReturn(200,'请先选择部门');
        }
        //获取该部门未认领的 员工账号
        $zaccout = $this->act['name']."-".$departid.'-'.'000';

        $accounts = $this->db->where('departid',$departid)->where('status',0)->where('name !=',$zaccout)->get($this->uTable)->result_array();
        if($accounts){
            $num = count($accounts);
            $index = rand(1,$num);
            $account = $accounts[$index-1];
            $name = explode('-',$account['name']);
            $number =$name[1].$name[2];
            $status = 1;
            $staff = $this->db->where('departid',$departid)->where('staff',1)->where('name !=',$zaccout)->count_all_results($this->uTable);
            if($staff < 2){
                // 设置代理
                if(2*$index>$num){
                    $staff =1;
                }

            }else{
                $staff = 0;
            }
            $this->db->where('id',$account['id'])->update($this->uTable,['status'=>$status,'staff'=>$staff,'updated_at'=>date('Y-m-d H:i:s')]);

            $this->ajaxReturn(0,'',['number'=>$number]);
        }else{
            $this->ajaxReturn(200,'没有空闲的账号了，请联系系统管理员');
        }
    }

    /**
     *  认领账号
     */
    public function claim2(){
        $role =$this->input->post('role');

        $accounts = $this->db->where('status',1)->where('role',1)->get($this->uTable)->result_array();
        if($accounts){
            $num = count($accounts);
            $index = rand(1,$num);
            $account = $accounts[$index-1];
            $name = explode('-',$account['name']);
            $number =$name[1].$name[2];
            $status = 1;
            $this->db->where('id',$account['id'])->update($this->uTable,['status'=>$status,'updated_at'=>date('Y-m-d H:i:s')]);

            $this->ajaxReturn(0,'',['number'=>$number]);
        }else{
            $this->ajaxReturn(200,'没有空闲的账号了，请联系系统管理员');
        }
    }

    /**
     *  登录账号
     */
    public function login(){
        $number = $this->getNumber();
        if(empty($number)){
            $this->ajaxReturn(200,'请输入账号');
        }
        $account = $this->accountInfo($number);
        if($account){
            if($account['status']==0){
                $this->ajaxReturn(500,'无效账号');
            }
            $ud = [
                'ascsessment_account_23_start'=>$account,
                'number'=>$number,
            ];
            $this->session->set_userdata($ud);
            $this->ajaxReturn(0,'登录成功',['staff'=>$account['staff']]);
        }else{
            $ud = [
                'ascsessment_account_23_start'=>'',
                'number'=>'',
            ];
            $this->session->set_userdata($ud);
            $this->ajaxReturn(500,'无效账号');
        }
    }

    /**
     * @return string 解析登录账号
     */
    public function getNumber(){
        $number = $this->input->post('number');
        if(empty($number)){
           return "";
        }
        $length = strlen($number);
        $depertid = substr($number,0,($length-3));
        $name = substr($number,-3);
        $number = $this->act['name']."-".$depertid.'-'.$name;
        return $number;
    }

    /**
     *  根据登录账号 获取账号信息
     */
    public function accountInfo($number){
        $account = $this->db->where('name',$number)->get($this->uTable)->row_array();
        return $account;
    }

    public function ajaxReturn($code,$msg="",$data=array()){
        $return =[
            'code'=>$code,
            'msg' =>$msg,
            'data' =>$data,
        ];
        echo json_encode($return);die;
    }

    /**
     * 清缓存
     */
    public function clears(){
        $ud = [
            'ascsessment_account_23_start'=>"",
            'number'=>"",
        ];
        $this->session->set_userdata($ud);
        $this->ajaxReturn(0);
    }

    public function qyauth(){
        $res =$this->qyoauth->getUseridAjax();

        if($res){
            if(!empty($res['userid'])){
                $number = "";
                $userid = $res['userid'];
                $userinfo = $this->db->where('userid',$userid)->get($this->joinUTable)->row_array();
                if($userinfo['department']){
                    // 主管
                    if($userinfo['manage'] ==1 ){
                        $this->claim4();
                    }else{
                        $departid =  $this->parent_department($userinfo['department'],1);
                        $this->claim3($departid);
                    }
                }
                $this->ajaxReturn(0,'',array('number'=>$this->number));
            }else{
                $this->ajaxReturn(0,'',array('url'=>$res['url']));
            }
        }else{
            $this->ajaxReturn('600','请在企业微信中打开');
        }
    }
}