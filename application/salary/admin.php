<?php
// 127.0.0.1/ci302/index.php/salary/admin
class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('admin/auth_model');
        $this->user = $this->auth_model->checkPower(1);
        $this->load->database();
    }
    
    public function index(){
        $this->load->view('newadmin/salary/index');
    }
    
    public function ajaxlist(){
        $this->load->library('authcode');
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        
        $this->db->start_cache();
        
        if($year>0) $this->db->where('year',$year);
        if($month>0) $this->db->where('month',$month);
        
        $this->db->stop_cache();
        
        $some=$this->db->count_all_results('bzh_salary');
        $list=$this->db->select('qy_user.userid,qy_user.name,qy_department.name as department,bzh_salary.salary,bzh_salary.time',false)
        ->join('qy_user','qy_user.userid=bzh_salary.userid')
        ->join('qy_department','qy_user.department=qy_department.Id')
        ->get('bzh_salary',$length,$start)->result();
        
        foreach ($list as $key=>$val){
            $list[$key]->salary=$this->authcode->code($val->salary,'DECODE',$val->userid);
        }
        
        $data=array(
            "draw"=>(int)$draw,
            "recordsTotal"=>$some,
            "recordsFiltered"=>$some,
            "data"=>$list
        );
        echo json_encode($data);
    }
    
    //上传表
    public function upload(){
        $config=array(
            'upload_path'=>'style/upload/salary',
            'allowed_types'=>'xlsx|xls',
            'max_size'=>'10240',
            'file_name'=>$this->getMicrotime()
        );
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('file')){
            echo json_encode(array('code'=>0,'msg'=>$this->upload->display_errors('','')));
        }else{
            echo json_encode(array('code'=>1,'msg'=>$this->upload->data('file_name')));
        }
    }
    
    public function addBatch(){
        set_time_limit(0);
        $filename=$this->input->post('filename');
        $date['year']=$this->input->post('year');
        $date['month']=$this->input->post('month');
        echo '{"code":0,"msg":"'.$date['year'].$date['month'].$filename.'"}';
        return;
        ini_set("memory_limit", "512M");
        $this->load->library('PHPExcel');
    
        $error_msg='';
        
        $path = 'style/upload/salary/'.$filename;
        try{
            $objReader = PHPExcel_IOFactory::createReaderForFile($path);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($path);
        }catch (Exception $e){
            echo '{"code":0,"msg":"文件损坏或格式有误，请检测表格和网络后稍后重试。若反复出现该情况请联系管理员协助解决。"}';
            return;
        }
        $objWorksheet = $objPHPExcel->getSheet(0);
        $row_num = $objWorksheet->getHighestRow();//总行数
    
        if($row_num<2){
            echo '{"code":0,"msg":"文件内容为空!"}';
            @unlink($path);
            return;
        }
        $insert_num=0;
        $update_num=0;
    
        $insert_data=array();
        $update_data=array();
    
        //跳过表头从第二行开始读取
        for ($row = 2; $row<= $row_num; $row++){
            $col=0;//从第1列开始
            $row_data=array(//读取一行
                'userid'=>trim((String)$objWorksheet->getCellByColumnAndRow($col++, $row)->getValue()),
                'salary'=>(String)$objWorksheet->getCellByColumnAndRow($col++, $row)->getValue()
            );
            if(empty($row_data['userid'])||empty($row_data['salary'])){
                $error_msg.="<br>第 $row 行缺少必填数据";
                continue;
            }
            $row_data['salary']=$this->authcode->code($row_data['salary'],'ENCODE',$row_data['userid']);
            $db_row=$this->db->select('Id')->where($date)->where('userid',$row_data['userid'])->get('bzh_salary')->row_array();
            if(empty($db_row)){
                array_push($insert_data, $date+$row_data);
                $insert_num++;
            }else{
                array_push($update_data, $db_row+$row_data);
                $update_num++;
            }
        }
        $msg='文件共有数据'.($row_num-1).'条记录';
        if(count($insert_data)>0){
            $this->db->insert_batch('bzh_salary',$insert_data);
            $msg.='<br>数据库导入'.count($insert_data).'条记录';
        }
        if(count($update_data)>0){
            $this->db->update_batch('bzh_salary',$insert_data,'Id');
            $msg.='<br>数据库更新'.count($update_data).'条记录';
        }
        echo '{"code":1,"msg":"'.$msg.'"}';
        @unlink($path);
    }
    
    public function out(){
        $this->load->library('authcode');
        $year=$this->input->get('year');
        $month=$this->input->get('month');
        
        $this->db->start_cache();
        
        if($year>0) $this->db->where('year',$year);
        if($month>0) $this->db->where('month',$month);
        
        $this->db->stop_cache();
        
        $some=$this->db->count_all_results('bzh_salary');
        $list=$this->db->select('qy_user.name,qy_department.name as department,bzh_salary.*',false)
        ->join('qy_user','qy_user.userid=bzh_salary.userid')
        ->join('qy_department','qy_user.department=qy_department.Id')
        ->get('bzh_salary')->result();
        
        if($some>3000){
            header ( 'Content-Type: application/vnd.ms-excel' );
            header ( 'Content-Disposition: attachment;filename="工资表-'.date('Y_m_d').'.csv"' );
            header ( 'Cache-Control: max-age=0' );
            $file = fopen ( 'php://output', 'a' );
            
            $titie = "员工编号,姓名,所在部门,工资月份,工资,导入时间\n";
            fwrite($file, iconv("utf-8", "gbk", $titie));
            $limit = 1000;
            foreach($list as $data)
            {
                $limit--;
                $temp[0] = iconv("utf-8", "gbk", $data->userid);
                $temp[1] = iconv("utf-8", "gbk", $data->name);
                $temp[2] = iconv("utf-8", "gbk", $data->department);
                $temp[3] = iconv("utf-8", "gbk", $data->year.'年'.$data->month.'月');
                $temp[4] = iconv("utf-8", "gbk", $data->salary);
                $temp[5] = iconv("utf-8", "gbk", $data->time);
                fputcsv($file, $temp);
                if($limit<1){
                    ob_flush ();
                    flush ();
                    $limit=1000;
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
            ->setTitle("工资表")
            ->setCategory("admin");
            $worksheet=$objPHPExcel->setActiveSheetIndex(0);
            
            $worksheet->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '所在部门')
            ->setCellValue('D1', '工资月份')
            ->setCellValue('E1', '工资')
            ->setCellValue('F1', '导入时间');
            
            $row = 2;
            
            foreach($list as $data)
            {
                $lie=0;
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->userid);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->name);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->department);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->year.'年'.$data->month.'月');
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->salary);
                $worksheet->setCellValueByColumnAndRow($lie++,$row,$data->time);
                $row++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="工资表_'.$begin.'_'.$end.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        }
    }
    
    private function getMicrotime(){
        $dt=microtime();
        return substr($dt,11).substr($dt,2,6);
    }
}