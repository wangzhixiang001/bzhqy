<?php
// 127.0.0.1/ci302/index.php/usermanage/userlist/
class Userlist extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('admin/auth_model');
        $this->user = $this->auth_model->checkPower(0);
        $this->load->database();
    }
    
    public function index(){
        $data['list']=$this->db->query('select id,parentid as pId,name from qy_department order by `order`')->result();
        $this->load->view('newadmin/usermanage/userlist',$data);
    }
    
    public function userinfo($userid){
        $data=$this->db->where('userid',$userid)->get('qy_user')->row();
        $data->department=$this->db->select('name')->where('id',$data->department)->get('qy_department')->row()->name;
        $this->load->view('newadmin/usermanage/userinfo',$data);
    }
    
    public function ajaxuserlist(){
        $dp=$this->input->post('dp');
        $type=$this->input->post('type');
        $text=$this->input->post('text');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        $all=$this->db->count_all('qy_user');
        $this->db->start_cache();
        if($dp>1) $this->db->where('department',$dp);
        if(!empty($text)){
            $tag=array('userid','name','mobile','weixinid');
            $this->db->where($tag[$type],$text);
        }
        $this->db->stop_cache();
        $some=$this->db->count_all_results('qy_user');
        $list=$this->db->select('userid,name,mobile,weixinid')->get('qy_user',$length,$start)->result();
        
        $data=array(
            "draw"=>(int)$draw,
            "recordsTotal"=>$some,
            "recordsFiltered"=>$some,
            "data"=>$list
        );
        echo json_encode($data);
    }
    
    public function out(){
        $dp=$this->input->get('dp');
        $type=$this->input->get('type');
        $text=$this->input->get('text');
        if($dp>1) $this->db->where('department',$dp);
        if(!empty($text)){
            $tag=array('userid','name','mobile','weixinid');
            $this->db->where($tag[$type],$text);
        }
        $list=$this->db->select('userid,name,mobile,weixinid')->get('qy_user')->result();
        $some=count($list);
        
        if($some>3000){
            header ( 'Content-Type: application/vnd.ms-excel' );
            header ( 'Content-Disposition: attachment;filename="员工信息-'.date('Y_m_d').'.csv"' );
            header ( 'Cache-Control: max-age=0' );
            $file = fopen ( 'php://output', 'a' );
            
            $titie = "员工编号,姓名,手机,微信号,部门\n";
            fwrite($file, iconv("utf-8", "gbk", $titie));
            $limit = 5000;
            foreach($list as $data)
            {
                $limit--;
                $temp[0] = iconv("utf-8", "gbk", $data->userid);
                $temp[1] = iconv("utf-8", "gbk", $data->name);
                $temp[2] = iconv("utf-8", "gbk", $data->time);
                fputcsv($file, $temp);
                if($limit<1){
                    ob_flush ();
                    flush ();
                    $limit=5000;
                }
            }
            fclose($file);
        }else{
            set_time_limit(90);
            ini_set("memory_limit", "512M");
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("admin")
            ->setLastModifiedBy("admin")
            ->setTitle("报饭记录")
            ->setCategory("admin");
            $worksheet=$objPHPExcel->setActiveSheetIndex(0);
            
            $worksheet->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '报饭时间');
            
            $worksheet->getColumnDimension('A')->setWidth(15);
            $worksheet->getColumnDimension('B')->setWidth(15);
            $worksheet->getColumnDimension('C')->setWidth(30);
            
            $row = 2;
            
            foreach($list as $data)
            {
                $lie=0;
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->userid);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->name);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->time);
                $row++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="报饭记录_.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        }
    }
    
    public function ajax_one($userid){
        $this->load->library('weixin/qyoauth');
        $val=$this->qyoauth->getUserInfo($userid);
        if(!$val) die('0');
        $this->load->database();
        $data=array(
            'userid'=>$val['userid'],
            'name'=>$val['name'],
            'department'=>implode(",",$val['department']),
            'position'=>isset($val['position'])?$val['position']:'',
            'mobile'=>$val['mobile'],
            'gender'=>$val['gender'],
            'email'=>isset($val['email'])?$val['email']:'',
            'weixinid'=>isset($val['weixinid'])?$val['weixinid']:'',
            'avatar'=>isset($val['avatar'])?$val['avatar']:site_url('style/weui/images/default.png/'),
            'status'=>$val['status']
        );
        $query=$this->db->insert_string('qy_user',$data).' ON DUPLICATE KEY UPDATE name=VALUES(name),department=VALUES(department),position=VALUES(position),mobile=VALUES(mobile),gender=VALUES(gender),email=VALUES(email),weixinid=VALUES(weixinid),avatar=VALUES(avatar),status=VALUES(status)';
        $this->db->query($query);
        echo '1';
    }
    
    public function ajax_all(){
        $this->load->library('weixin/qyoauth');
        $list=$this->qyoauth->getDepartmentUserInfo(1,0,1);
        if(!$list) die('0');
        $this->load->database();
    
        $query="INSERT INTO `qy_user` (`userid`, `name`, `department`, `position`, `mobile`, `gender`, `email`, `weixinid`, `avatar`, `status`) VALUES";
    
        foreach ($list as $val){
            $t=" ('".$val['userid']."','".$val['name']."','".implode(",",$val['department'])."','".(isset($val['position'])?$val['position']:'')."','".$val['mobile']."','".$val['gender']."','".(isset($val['email'])?$val['email']:'')."','".(isset($val['weixinid'])?$val['weixinid']:'')."','".(isset($val['avatar'])?$val['avatar']:site_url('style/weui/images/default.png/'))."',".$val['status']."),";
            $query.=$t;
        }
    
        $query=rtrim($query,",").' ON DUPLICATE KEY UPDATE name=VALUES(name),department=VALUES(department),position=VALUES(position),mobile=VALUES(mobile),gender=VALUES(gender),email=VALUES(email),weixinid=VALUES(weixinid),avatar=VALUES(avatar),status=VALUES(status)';
        $this->db->query($query);
        echo '1';
    }
    
    public function ajax_syn(){
        $this->load->library('weixin/qyoauth');
        $list=$this->qyoauth->getDepartmentUserInfo(1,0,1);
        if(!$list) die('0');
        $this->load->database();

        $query="INSERT INTO `qy_user` (`userid`, `name`, `department`, `position`, `mobile`, `gender`, `email`, `weixinid`, `avatar`, `status`) VALUES";
    
        foreach ($list as $val){
            $t=" ('".$val['userid']."','".$val['name']."','".implode(",",$val['department'])."','".(isset($val['position'])?$val['position']:'')."','".$val['mobile']."','".$val['gender']."','".(isset($val['email'])?$val['email']:'')."','".(isset($val['weixinid'])?$val['weixinid']:'')."','".(isset($val['avatar'])?$val['avatar']:site_url('style/weui/images/default.png/'))."',".$val['status']."),";
            $query.=$t;
        }
    
        $query=rtrim($query,",");
        $this->db->simple_query('truncate table qy_user');
        $this->db->query($query);
        echo '1';
    }
	
	public function ajax_syndp(){
        $this->load->library('weixin/qyoauth');
        $list=$this->qyoauth->getDepartment();
        if(!$list) die('0');
        $this->load->database();
		
        $query="INSERT INTO `qy_department` (`id`,`name`,`parentid`,`order`) VALUES ";
    
        foreach ($list as $val){
            $query.="({$val['id']},'{$val['name']}',{$val['parentid']},{$val['order']}),";
        }
        $query=rtrim($query,",");
        $this->db->simple_query('truncate table qy_department');
        $this->db->query($query);
        echo '1';
    }
}