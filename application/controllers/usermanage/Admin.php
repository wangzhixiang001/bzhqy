<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Admin extends CI_Controller {

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
    
    public function msglist(){
        $this->load->view('newadmin/dailymeal/msg');
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
        $all=count($list);
        
        $data=array(
            "draw"=>(int)$draw,
            "recordsTotal"=>$all,
            "recordsFiltered"=>$some,
            "data"=>$list
        );
        echo json_encode($data);
    }
    
    public function ajaxmsglist(){
        $begin=$this->input->post('begin');
        $end=$this->input->post('end');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
    
        $all=$this->db->count_all('bzh_dailymeal_msg');
    
        $this->db->start_cache();

        if(!empty($begin)) $this->db->where('time >=',$begin);
        if(!empty($end)) $this->db->where('time <',date("Y-m-d",strtotime($end." +1 day")));
    
        $this->db->stop_cache();
    
        $some=$this->db->count_all_results('bzh_dailymeal_msg');
        $list=$this->db->select('qy_user.userid,qy_user.name,bzh_dailymeal_msg.msg,bzh_dailymeal_msg.time')->join('qy_user','qy_user.userid=bzh_dailymeal_msg.userid')->order_by('bzh_dailymeal_msg.Id','desc')->get('bzh_dailymeal_msg',$length,$start)->result();
    
        $data=array(
            "draw"=>(int)$draw,
            "recordsTotal"=>$all,
            "recordsFiltered"=>$some,
            "data"=>$list
        );
        echo json_encode($data);
    }
    
    public function out(){
        $begin=$this->input->get('begin');
        $end=$this->input->get('end');
        
        $this->db->start_cache();
        $this->db->where('type',1);
        if(!empty($begin)) $this->db->where('time >=',$begin);
        if(!empty($end)) $this->db->where('time <',date("Y-m-d",strtotime($end." +1 day")));
        $this->db->stop_cache();
        $some=$this->db->count_all_results('bzh_dailymeal_main');
        $list=$this->db->select('qy_user.userid,qy_user.name,bzh_dailymeal_main.time')->join('qy_user','qy_user.userid=bzh_dailymeal_main.userid')->get('bzh_dailymeal_main')->result();
        
        if($some>3000){
            header ( 'Content-Type: application/vnd.ms-excel' );
            header ( 'Content-Disposition: attachment;filename="报饭记录-'.date('Y_m_d').'.csv"' );
            header ( 'Cache-Control: max-age=0' );
            $file = fopen ( 'php://output', 'a' );
            
            $titie = "员工编号,姓名,报饭时间\n";
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
            header('Content-Disposition: attachment;filename="报饭记录_'.$begin.'_'.$end.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        }
    }
    
    public function outnum(){
        $begin=$this->input->get('begin');
        $end=$this->input->get('end');
    
        $this->db->where('type',1);
        if(!empty($begin)) $this->db->where('time >=',$begin);
        if(!empty($end)) $this->db->where('time <',date("Y-m-d",strtotime($end." +1 day")));
        $list=$this->db->select('qy_user.userid,qy_user.name,count(*) as num',FALSE)->join('qy_user','qy_user.userid=bzh_dailymeal_main.userid')->get('bzh_dailymeal_main')->result();
        
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("admin")
            ->setLastModifiedBy("admin")
            ->setTitle("报饭次数统计")
            ->setCategory("admin");
            $worksheet=$objPHPExcel->setActiveSheetIndex(0);
    
            $worksheet->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '报饭次数');
    
            $worksheet->getColumnDimension('A')->setWidth(15);
            $worksheet->getColumnDimension('B')->setWidth(15);
            $worksheet->getColumnDimension('C')->setWidth(10);
    
            $row = 2;
    
            foreach($list as $data)
            {
                $lie=0;
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->userid);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->name);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->num);
                $row++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="报饭次数统计_'.$begin.'_'.$end.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
    }
}