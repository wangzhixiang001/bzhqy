<?php

class Admin extends CI_Controller {

    private $departTable ="assessment_department";
    private $uTable ="assessment_user";
    private $joinUTable ="assessment_join_user";
    private $rTable ="assessment_record";
    private $oTable ="assessment_option_record";

    public function __construct(){
        parent::__construct();
        $this->load->model('admin/auth_model');
        $this->user = $this->auth_model->checkPower(1);
        $this->load->database();
        $this->load->model('admin/department_model','departModel');
    }

    // 部门
    public function index(){
        $this->departModel->init();
        $this->load->view('newadmin/performance/list');
    }

    /**
     * 关系部门
     */
    public function departmentListAjax(){
        $name = $this->input->post('name');

        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        if($name){
            $this->db->like('name',$name);
        }
        $list = $this->db->get($this->departTable,$length,$start)->result_array();

         foreach($list as &$v){
             if(empty($v['brother_department'])){
                 $v['brother_department'] = "所有部门";
             }else{
                 $brothers = json_decode($v['brother_department'],true);
                 $result = $this->db->where_in('id',$brothers)->select('name')->get($this->departTable)->result_array();
                 $v['brother_department'] = array_column($result,'name');
             }
         }
        $some=$this->db->count_all_results($this->departTable);
        $all=count($list);
        echo json_encode(['data'=>$list,"recordsTotal"=>$all,'recordsFiltered'=>$some]) ;die;
    }

    public  function edit_depart(){
        $id = $this->input->get('id');
        $info = $this->db->where('id',$id)->get($this->departTable)->row_array();

        $info['brother_department'] = empty($info['brother_department'])?array():json_decode($info['brother_department'],true);

        $departs = $this->db->where('id !=',$id)->select('id,name')->get($this->departTable)->result_array();

        if($departs){
            $departs = array_column($departs,'name','id');
        }else{
            $departs = array();
        }
        $this->load->view('newadmin/performance/edit_depart',['info'=>$info,'departs'=>$departs]);
    }

    public function editAjax(){
        $param = $this->input->post();
        $id = $param['id'];
        if(!empty($param['brother_department'])) {
            $brother_department = json_encode($param['brother_department']);
        }else{
            $brother_department = "";
        }
        $res = $this->db->where('id',$id)->update($this->departTable,array('brother_department'=>$brother_department));
        if($res){
            echo json_encode(['code'=>0,"msg"=>'设置完成']);
        }else{
            echo json_encode(['code'=>400,"msg"=>'设置失败']);
        }
        die();
    }


    /**
     *  账号管理
     */
    public function joinlist(){
        $this->load->view('newadmin/performance/jlist');
    }

    /**
     * 关系部门
     */
    public function jListAjax(){
        $name = $this->input->post('name');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        if($name){
            $this->db->like('name',$name);
            $this->db->or_like('userid',$name);
        }
        $list = $this->db->get($this->joinUTable,$length,$start)->result_array();

        foreach($list as &$v){
            $department = $this->parent_department($v['department'],array(),0);
            $department = array_reverse($department);
            $v['department'] = implode('/',$department);
            $v['is_join_text'] = !empty($v['is_join'])?"考核人员":"非考核人员";
        }
        $some=$this->db->count_all_results($this->joinUTable);
        $all=count($list);
        echo json_encode(['data'=>$list,"recordsTotal"=>$all,'recordsFiltered'=>$some]) ;die;
    }

    public function setStatus(){
        $userid = $this->input->post('userid');
        $is_join = $this->input->post('is_join');
        $this->db->where('userid',$userid)->update($this->joinUTable,['is_join'=>$is_join]);
        echo json_encode(['code'=>0,'msg'=>'设置完成']);die;
    }

    public function setManage(){
        $userid = $this->input->post('userid');
        $manage = $this->input->post('manage');
        $this->db->where('userid',$userid)->update($this->joinUTable,['manage'=>$manage]);
        echo json_encode(['code'=>0,'msg'=>'设置完成']);die;
    }

    /**
     *  获取上层部门
     */
    public function parent_department($id,$result=array(),$parentid = 0){

           $department =  $this->db->where('id',$id)->get('qy_department')->row_array();
           if($department){
               if($department['parentid']!=$parentid) {
                   $result[] = $department['name'];
                   return $this->parent_department($department['parentid'], $result, $parentid);
               }else{
                   return $result;
               }
           }else{
               return $result;
           }
    }

    /**
     * 同步用户
     */
    public function ajax_all(){
        /*$this->load->library('weixin/qyoauth');
        $list=$this->qyoauth->getDepartmentUserInfo(1,0,1);*/
        $list = $this->db->get('qy_user')->result_array();
        if(!$list) die('0');
        $this->load->database();

        $query="INSERT INTO `assessment_join_user` (`userid`, `name`, `department`, `position`, `mobile`, `gender`, `email`, `weixinid`, `avatar`, `status`,`is_join`) VALUES";

        foreach ($list as $val){
            $val['is_join'] = 0;
            $department =empty($val['department'])?'':implode(",",(array)$val['department']);
            $t=" ('".$val['userid']."','".$val['name']."','".$department."','".(isset($val['position'])?$val['position']:'')."','".$val['mobile']."','".$val['gender']."','".(isset($val['email'])?$val['email']:'')."','".(isset($val['weixinid'])?$val['weixinid']:'')."','".(isset($val['avatar'])?$val['avatar']:site_url('style/weui/images/default.png/'))."',".$val['status'].",".$val['is_join']."),";
            $query.=$t;
        }

        $query=rtrim($query,",").' ON DUPLICATE KEY UPDATE name=VALUES(name),department=VALUES(department),position=VALUES(position),mobile=VALUES(mobile),gender=VALUES(gender),email=VALUES(email),weixinid=VALUES(weixinid),avatar=VALUES(avatar),status=VALUES(status)';
        $this->db->query($query);
        echo '1';
    }





    /**
     *  账号管理
     */
    public function account(){
        $department = $this->db->get($this->departTable)->result_array();
        if($department){
            $departs = array_column($department,'name','id');
        }else{
            $departs = array();
        }
        $this->load->view('newadmin/performance/ulist',['departs'=>$departs]);
    }

    /**
     * 账号管理
     */
    public function uListAjax(){
        $name = $this->input->post('name');
        $departid = $this->input->post('departid');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        if($name){
            $this->db->like('name',$name);
        }
        if($departid){
            $this->db->where('departid',$departid);
        }
        $list = $this->db->get($this->uTable,$length,$start)->result_array();

        foreach($list as &$v){
            $department = $this->db->where('id',$v['departid'])->get($this->departTable)->row_array();
            $v['department'] = $department['name'];
            $v['status_text'] = !empty($v['status'])?"已认领":"空闲";
        }

        $some=$this->db->count_all_results($this->uTable);
        $all=count($list);
        echo json_encode(['data'=>$list,"recordsTotal"=>$all,'recordsFiltered'=>$some]) ;die;
    }

    /**
     * 添加账号
     */
    public function addAjax(){
          $act = $this->config();
          $departs  =  $this->db->get($this->departTable)->result_array();
          $insert = [];
          foreach($departs as $v){
             $nums   =  $this->nums($v['id']);
             $int = 0;
             while($int<$nums){
                 $role =0;
                 $status =0;
                 if($int==0){
                    $role =1;
                    $status =1;
                 }
                $item = [
                    'departid'=>$v['id'],
                    'name' =>$act['name']."-".$v['id']."-".str_pad($int, 3, "0", STR_PAD_LEFT) ,  //随机数
                    'role' =>$role,
                    'staff' =>0,
                    'status'=>$status,
                    'updated_at'=>date('Y-m-d H:i:s',time()),
                ];
                 $int++;
                 $insert[]=$item;
             }
          }
          $this->db->where('name like',$act['name'].'-%')->delete($this->uTable);
          $this->db->insert_batch($this->uTable,$insert);
          echo json_encode(['code'=>0,'msg'=>'账号添加成功']);
    }

    /**
     *  参与考评结果
     */
    public function join(){

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
        return $act;
    }

    //获取部门人数
    public function nums($did){

        $dids = $this->childs($did);
        $nums = 0;
        if($dids) {
           $nums =  $this->db->where_in('department', $dids)->count_all_results('qy_user');
        }
        return $nums;

    }

    public function childs($id,$list=array()){
        $id = (array)$id;
        $list = array_merge($id,$list);
        $res  = $this->db->where_in('parentid',$id)->get("qy_department")->result_array();
        if($res){
            $ids = array_column($res,'id');
            return $this->childs($ids,$list);
        }else{
            return $list;
        }
    }

    /**
     *  记录列表
     */
    public function rlist(){
        $this->load->view('newadmin/performance/rlist');
    }

    public  function rListAjax(){
        $name = $this->input->post('name');
        $departid = $this->input->post('departid');
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        if($name){
            $this->db->like('assessment_record.userid',$name);
            $this->db->or_like('assessment_join_user.name',$name);
        }
        $this->db->select('assessment_record.userid,assessment_join_user.name');
        $this->db->join($this->joinUTable, 'assessment_join_user.userid = assessment_record.userid');
        $this->db->distinct('userid');
        $list = $this->db->get($this->rTable,$length,$start)->result_array();
        foreach($list as &$v){
            $rids = $this->db->where('userid',$v['userid'])->select('id')->get($this->rTable)->result_array();
            if($rids){
                $v['person_count'] = count($rids);
                $v=$this->getcountandsum($v);
            }else{
                $v['person_count'] = 0;
            }

        }
        $some=$this->db->group_by('userid')->count_all_results($this->rTable);

        $all=count($list);
        echo json_encode(['data'=>$list,"recordsTotal"=>$all,'recordsFiltered'=>$some]) ;die;

    }

    public function getcountandsum($v){
        $query = "select r.id from ".$this->rTable.' r  left join '.$this->uTable." u on u.id = r.uid where u.role =0 and r.userid ='".$v['userid']."'";
        $query = "select count(*) count from " .$this->oTable." where rid in (".$query.") and `option` ='专业能力成果'";
		$result = $this->db->query($query)->row_array();
        $v['y_one_nums'] = $result['count'];

        $query = "select r.id from ".$this->rTable.' r  left join '.$this->uTable." u on u.id = r.uid where u.role =0 and r.userid ='".$v['userid']."'";
        $query = "select count(*) count from " .$this->oTable." where rid in (".$query.") and `option` ='职业素养表现'";
        $result = $this->db->query($query)->row_array();
        $v['y_two_nums'] = $result['count'];

        $v = $this->scoreDetail($v,0);

        $v['y_one_avg'] =  $v['y_one_nums']>0?($v['y_one_score']/$v['y_one_nums']):0;
        $v['y_two_avg'] =  $v['y_two_nums']>0?($v['y_two_score']/$v['y_two_nums']):0;
        $v['y_one_avg'] = round($v['y_one_avg'],1);
        $v['y_two_avg'] = round($v['y_two_avg'],1);
        $v['y_zscore'] =  $v['y_two_avg']+$v['y_one_avg'];

        $query = "select r.id from ".$this->rTable.' r  left join '.$this->uTable." u on u.id = r.uid where u.role =1 and r.userid ='".$v['userid']."'";
        $query = "select count(*) count from " .$this->oTable." where rid in (".$query.") and `option` ='专业能力成果'";
        $result = $this->db->query($query)->row_array();
		$v['m_one_nums'] = $result['count'];

        $query = "select r.id from ".$this->rTable.' r  left join '.$this->uTable." u on u.id = r.uid where u.role =1 and r.userid ='".$v['userid']."'";
        $query = "select count(*) count from " .$this->oTable." where rid in (".$query.") and `option` ='职业素养表现'";
        $result = $this->db->query($query)->row_array();
		$v['m_two_nums'] = $result['count'];

        $v = $this->scoreDetail($v,1);

        $v['m_one_avg'] =  $v['m_one_nums']>0?($v['m_one_score']/$v['m_one_nums']):0;
        $v['m_two_avg'] =  $v['m_two_nums']>0?($v['m_two_score']/$v['m_two_nums']):0;
        $v['m_one_avg'] = round($v['m_one_avg'],1);
        $v['m_two_avg'] = round($v['m_two_avg'],1);
        $v['m_zscore'] =  $v['m_two_avg']+$v['m_one_avg'];

        return $v;
    }


    /**
     * 分数明细
     */
    public function scoreDetail($v,$role = 1){
        $query = "select r.id from ".$this->rTable.' r  left join '.$this->uTable." u on u.id = r.uid where u.role =$role and r.userid ='".$v['userid']."'";
        $res = $this->db->query($query)->result_array();
        $one = array();
        $two = array();
        foreach($res as $r){
            $query = "select score from " .$this->oTable." where rid ='".$r['id']."' and `option` ='专业能力成果'";
            $one_option = $this->db->query($query)->row_array();
            $one_score =empty($one_option)?0:$one_option['score'];
            $one[] = $one_score;

            $query = "select score from " .$this->oTable." where rid ='".$r['id']."' and `option` ='职业素养表现'";
            $two_option = $this->db->query($query)->row_array();
            $two_score =empty($two_option)?0:$two_option['score'];
            $two[] = $two_score;

        }
        if($role ==1){
            $v['m_one_score'] = array_sum($one);
            $v['m_two_score'] = array_sum($two);
            $v['m_one_score_detail'] =(empty($one) || count($one)==1)?"":implode('+',$one)."=";
            $v['m_two_score_detail'] =(empty($two) || count($one)==1)?"":implode('+',$two)."=";
        }else{
            $v['y_one_score'] = array_sum($one);
            $v['y_one_score_detail'] =(empty($one)||count($one)==1) ?"":implode('+',$one)."=";
            $v['y_two_score'] = array_sum($two);
            $v['y_two_score_detail'] = (empty($two) ||count($one)==1) ?"":implode('+',$two)."=";
        }
        return $v;
    }


    public function out(){
        $this->db->select('assessment_record.userid,assessment_join_user.name');
        $this->db->join($this->joinUTable, 'assessment_join_user.userid = assessment_record.userid');
        $this->db->distinct('userid');
        $list = $this->db->get($this->rTable)->result_array();
        foreach($list as &$v){
            $rids = $this->db->where('userid',$v['userid'])->select('id')->get($this->rTable)->result_array();
            if($rids){
                $v['person_count'] = count($rids);
                $v=$this->getcountandsum($v);
            }else{
                $v['person_count'] = 0;
            }
        }
        set_time_limit(90);
        ini_set("memory_limit", "512M");
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("admin")
            ->setLastModifiedBy("admin")
            ->setTitle("考核记录")
            ->setCategory("admin");
        $worksheet=$objPHPExcel->setActiveSheetIndex(0);

        $worksheet->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '考评人数')

            ->setCellValue('D1', '身份')
            ->setCellValue('E1', '考评项')
            ->setCellValue('F1', '单项考核人数')
            ->setCellValue('G1', '审评明细')
            ->setCellValue('H1', '平均分')
            ->setCellValue('I1', '总分');

        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(15);
        $worksheet->getColumnDimension('D')->setWidth(20);
        $worksheet->getColumnDimension('E')->setWidth(20);
        $worksheet->getColumnDimension('F')->setWidth(15);
        $worksheet->getColumnDimension('G')->setWidth(80);
        $worksheet->getColumnDimension('H')->setWidth(20);
        $worksheet->getColumnDimension('I')->setWidth(50);
        $row = 2;

        foreach($list as $data)
        {
            $lie=0;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['userid']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'2');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),'3');
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'4');
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['name']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'');
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['person_count']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'');
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,'员工');
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),'主管');
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'');
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,'专业能力成果');
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'职业素养表现');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),'专业能力成果');
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'职业素养表现');
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['y_one_nums']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),$data['y_two_nums']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),$data['m_one_nums']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),$data['m_two_nums']);

            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['y_one_score_detail'].$data['y_one_score']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),$data['y_two_score_detail'].$data['y_two_score']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),$data['m_one_score_detail'].$data['m_one_score']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),$data['m_two_score_detail'].$data['m_two_score']);

            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['y_one_avg']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),$data['y_two_avg']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),$data['m_one_avg']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),$data['m_two_avg']);
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['y_zscore']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+1),'');
            $worksheet->setCellValueByColumnAndRow($lie,($row+2),$data['m_zscore']);
            $worksheet->setCellValueByColumnAndRow($lie,($row+3),'');

            $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":A".($row+3));
            $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":B".($row+3));
            $objPHPExcel->getActiveSheet()->mergeCells("C".$row.":C".($row+3));
            $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":D".($row+1));
            $objPHPExcel->getActiveSheet()->mergeCells("D".($row+2).":D".($row+3));
            $objPHPExcel->getActiveSheet()->mergeCells("I".($row).":I".($row+1));
            $objPHPExcel->getActiveSheet()->mergeCells("I".($row+2).":I".($row+3));

            $row=$row+4;
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="考评统计.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
    
    public function outnum(){
        $begin=$this->input->get('begin');
        $end=$this->input->get('end');
    
        $query="select userid,count(*) as num from bzh_dailymeal_main where type=1";
        if(!empty($begin)) $query.=" and time>='$begin'";
        if(!empty($end)) $query.=" and time<'".date("Y-m-d",strtotime($end." +1 day"))."'";
        $query="select u.name,m.* from ($query group by userid) m join qy_user u on u.userid=m.userid";
        
        $list=$this->db->query($query)->result();
        
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

    /**
     *  投票详情
     */
    public function  optionDetail($r){
        $list = $this->db->where('userid',$r['userid'])->get($this->rTable)->result_array();
        foreach($list as &$v) {
            $user = $this->db->where('id', $v['uid'])->get($this->uTable)->row_array();
            if ($user) {
                if ($user['role'] == 1) {
                    $v['user_role'] = "主管";
                } else {
                    $v['user_role'] = '员工';
                }
            }
            $v['one'] = $this->db->where('rid', $v['id'])->where('option', '专业能力成果')->get($this->oTable)->row_array();
            $v['two'] = $this->db->where('rid', $v['id'])->where('option', '职业素养表现')->get($this->oTable)->row_array();
        }
        $r['list']=$list;
        return $r;
    }

    public function out_dateil(){
        $this->db->select('assessment_record.userid,assessment_join_user.name');
        $this->db->join($this->joinUTable, 'assessment_join_user.userid = assessment_record.userid');
        $this->db->distinct('userid');
        $list = $this->db->get($this->rTable)->result_array();
        foreach($list as &$v){
            $rids = $this->db->where('userid',$v['userid'])->select('id')->get($this->rTable)->result_array();
            if($rids){
                $v['person_count'] = count($rids);
                $v=$this->optionDetail($v);
            }else{
                $v['person_count'] = 0;
            }
        }
        set_time_limit(90);
        ini_set("memory_limit", "512M");
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("admin")
            ->setLastModifiedBy("admin")
            ->setTitle("考核记录")
            ->setCategory("admin");
        $worksheet=$objPHPExcel->setActiveSheetIndex(0);

        $worksheet->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '考评人数')

            ->setCellValue('D1', '身份')
            ->setCellValue('E1', '考评项')
            ->setCellValue('F1', '分数')
            ->setCellValue('G1', '总分')
            ->setCellValue('H1', '原因及意见');

        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(15);
        $worksheet->getColumnDimension('C')->setWidth(15);
        $worksheet->getColumnDimension('D')->setWidth(20);
        $worksheet->getColumnDimension('E')->setWidth(20);
        $worksheet->getColumnDimension('F')->setWidth(15);
        $worksheet->getColumnDimension('G')->setWidth(20);
        $worksheet->getColumnDimension('H')->setWidth(150);
        $row = 2;
        foreach($list as $data)
        {
            $lie=0;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['userid']);
            $count = count($data['list']);
            for($j=1;$j<2*$count;$j++) {
                $worksheet->setCellValueByColumnAndRow($lie, ($row + $j), '');
            }
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['name']);
            for($j=1;$j<2*$count;$j++) {
                $worksheet->setCellValueByColumnAndRow($lie, ($row + $j), '');
            }
            $lie=$lie+1;
            $worksheet->setCellValueByColumnAndRow($lie,$row,$data['person_count']);
            for($j=1;$j<2*$count;$j++) {
                $worksheet->setCellValueByColumnAndRow($lie, ($row + $j), '');
            }
            $roww=$row;
            foreach($data['list'] as $key=>$t) {
                $liee=$lie+1;
                $worksheet->setCellValueByColumnAndRow($liee, $roww, $t['user_role']);
                $worksheet->setCellValueByColumnAndRow($liee, ($roww + 1), '');
                $liee=$liee+1;
                $worksheet->setCellValueByColumnAndRow($liee,$roww,'专业能力成果');
                $worksheet->setCellValueByColumnAndRow($liee,($roww+1),'职业素养表现');
                $liee=$liee+1;
                $worksheet->setCellValueByColumnAndRow($liee,$roww,($t['one']?$t['one']['score']:""));
                $worksheet->setCellValueByColumnAndRow($liee,($roww+1),($t['two']?$t['two']['score']:''));
                $liee=$liee+1;
                $worksheet->setCellValueByColumnAndRow($liee,$roww,$t['score']);
                $worksheet->setCellValueByColumnAndRow($liee,($roww+1),'');
                $liee=$liee+1;
                $worksheet->setCellValueByColumnAndRow($liee,$roww,$t['remark']);
                $worksheet->setCellValueByColumnAndRow($liee,($roww+1),'');

                $objPHPExcel->getActiveSheet()->mergeCells("D".$roww.":D".($roww+1));
                $objPHPExcel->getActiveSheet()->mergeCells("G".$roww.":G".($roww+1));
                $objPHPExcel->getActiveSheet()->mergeCells("H".$roww.":H".($roww+1));
                $roww=$roww+2;
            }
            $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":A".($roww-1));
            $objPHPExcel->getActiveSheet()->mergeCells("B".$row.":B".($roww-1));
            $objPHPExcel->getActiveSheet()->mergeCells("C".$row.":C".($roww-1));
            $row=$roww;
        }
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="考评统计详情.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}