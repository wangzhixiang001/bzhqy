<?php
//http://127.0.0.1/CI302/index.php/salary/user
class User extends CI_Controller {

    public function __construct() {
        //调用父类的构造函数
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library('weixin/qyoauth');
    }
    
    //入口
    public function index(){
        $this->load->view('salary/index');
    }
    
    //查询
    public function querysalary($year=0,$month=0){
        $where=array(
            'userid'=>$this->qyoauth->getUserid(),
            'year'=>$year,
            'month'=>$month
        );
        $this->qyoauth->getUserid();
        $info=$this->db->select('salary,dailymeal,overtime')->where($where)->get('bzh_salary')->row();
        if(empty($info)){
            echo '{"code":0,"msg":"没有找到本月工资信息"}';
        }else{
            $this->load->library('authcode');
            foreach ($info as $key=>$val){
                $info[$key]=$this->authcode->code($val,'DECODE',$where['userid']);
            }
            echo json_encode($info);
        }
    }
    
}