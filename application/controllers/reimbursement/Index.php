<?php
class Index extends CI_Controller {

    public function __construct() {
        //调用父类的构造函数
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('weixin/qyoauth');
    }

    //报销表单页
    public function index(){
        $id = (int)$this->uri->segment(4);
        if ($id>0){
            //获取详情
            $where['r.id']       = $id;
            $detail = $this->db
                ->select('r.id,u.name,customer_id,product_id,type,typename,avatar,r.director,r.userid,cus_name,pmpname,typename,cause,money,is_online,r.happend_time,r.ac_time,r.status,r.has_invoice,r.photos')
                ->where($where)
                ->join('qy_user u','u.userid = r.userid')
                ->join('bzh_pmpcus c','c.id = r.customer_id')
                ->join('bzh_pm p','p.id = r.product_id')
                ->join('bzh_btype b','b.id = r.type')
                ->get('bzh_reimbursement r')
                ->row_array();
            if ($detail){
                //获取负责人名字
                $detail['director_name'] = $this->db->select('name')->where('userid',$detail['director'])->get('qy_user')->row_array()['name'];
                $detail['is_online_name'] = (int)$detail['is_online']===1?'线上':'线下';
                $detail['has_invoice_name'] = (int)$detail['has_invoice']===2?'有':'无';
                $detail['photos'] = json_decode($detail['photos']);
            }
            echo "<!--";
            var_dump($detail);
            echo "-->";
        }else{
            $detail=[];
        }
        $user_id = $this->qyoauth->getUserid();
        //获取客户列表
        $customer_list = [];
        $customer_id_list = [];
        foreach ($this->db->select('cus_name,id')->get('bzh_pmpcus')->result_array() as $v){
            $customer_list[] = $v['cus_name'];
            $customer_id_list[] = $v['id'];
        }

        //获取报销类型
        $type_list = [];
        $type_ids = [];
        foreach ($this->db->select('typename,id')->get('bzh_btype')->result_array() as $v){
            $type_list[] = $v['typename'];
            $type_ids[] = $v['id'];
        }

        $this->load->view('reimbursement/index',[
            'customer_list' => json_encode($customer_list),
            'customer_id_list' => json_encode($customer_id_list),
            'type_list' => json_encode($type_list),
            'type_ids' => json_encode($type_ids),
            'userid'        => $user_id,
            'detail'        => $detail
        ]);
    }

    public function ajaxGetDirector(){
        $product_id =  $this->input->post('product_id');
        $director = $this->db
            ->select('u.userid,u.name')
            ->where('p.id',$product_id)
            ->join('qy_user u','u.userid = p.userid')
            ->get('bzh_pm p')
            ->row_array();
        return $this->ajaxReturn(1,$director);
    }

    //记录
    public function log(){
        $user_id = $this->qyoauth->getUserid();
        $list = $this->db
            ->select('r.id,u.name,r.userid,cus_name,pmpname,cause,money,r.ac_time,r.status')
            ->where('r.userid',$user_id)
            ->join('qy_user u','u.userid = r.userid')
            ->join('bzh_pmpcus c','c.id = r.customer_id')
            ->join('bzh_pm p','p.id = r.product_id')
            ->get('bzh_reimbursement r')
            ->result_array();
        $log = [];
        foreach ($list as $v){
            //记录
            $m = date('m',strtotime($v['ac_time']));
            $log[$m][] = $v;

        }
        $this->load->view('reimbursement/log',[
            'log' => $log
        ]);

    }

    //获取选择客户的项目列表
    public function ajaxGetProjects(){
       $customer_id =  $this->input->post('id');
       $data['products'] = [];
       $data['products_ids'] = [];
       foreach ($this->db->select('id,pmpname')->where('cus_id',$customer_id)->get('bzh_pm')->result_array() as $v){
           $data['products'][] = $v['pmpname'];
           $data['products_ids'][] = $v['id'];
       }
        return $this->ajaxReturn(1,$data);
    }

    //报销提交
    public function toExamine(){
        $data = $this->input->post();
        if ($data['customer_id']==null)return $this->ajaxReturn(0,'请选择客户');
        if ($data['product_id']==null)return $this->ajaxReturn(0,'请选择项目');
        if ($data['type']==null)return $this->ajaxReturn(0,'请选择报销类型');
        if ($data['is_online']==null)return $this->ajaxReturn(0,'请选择线上线下');
        if ($data['cause']==null)return $this->ajaxReturn(0,'请输入报销事由');
        if ($data['happend_time']==null)return $this->ajaxReturn(0,'请选择发生时间');
        if ($data['money']==null)return $this->ajaxReturn(0,'请输入费用金额');
        if ($data['has_invoice']==null){
            return $this->ajaxReturn(0,'请选择有无票据');
        }elseif ($data['has_invoice']==='2'){
            if (count($data['photos']) ===0){
                return $this->ajaxReturn(0,'请上传票据照片');
            }else{
                //图片上传
                $data['photos'] = $this->upload($data['photos']);
            }
        }elseif ($data['has_invoice']==='1'){
            unset($data['photos']);
        }
        $data['money'] = $data['money']*100;
        //判断是否为编辑
        if (isset($data['id'])){
            //编辑
            $where['id'] = $data['id'];
            $where['status'] = 0;
            $res = $this->db->where($where)->set($data)->update('bzh_reimbursement');
            $id = $data['id'];
        }else{
            $res = $this->db->insert('bzh_reimbursement', $data);
            $id = $this->db->insert_id();
        }

        if ($res){
            $url = site_url('Reimbursement/index/detail/'.$id);
            $this->sendMsg($data['director'],$url);
            return $this->ajaxReturn(1,'已提交审批',$url);
        }else{
            return $this->ajaxReturn(0,'提交失败,请重试');
        }
    }

    //报销撤销
    public function delete(){
        $id = $this->input->post('id');
        $where['userid'] = $this->qyoauth->getUserid();
        $where['id']     = $id;
        $where['status'] = 0;
        $re = $this->db->where($where)->delete('bzh_reimbursement');
        if ($re){
            return $this->ajaxReturn(1,'撤销成功');
        }else{
            return $this->ajaxReturn(0,'撤销失败');
        }
    }

    //图片上传
    public function upload($imgs){

        foreach ($imgs as $k=>$img){
            if (strstr($img,'static')){
                continue;
            }
            // 获取图片
            list($type, $data) = explode(',', $img);
            // 判断类型
            if(strstr($type,'image/jpeg')!=''){
                $ext = '.jpg';
            }elseif(strstr($type,'image/gif')!=''){
                $ext = '.gif';
            }elseif(strstr($type,'image/png')!=''){
                $ext = '.png';
            }

            $path = "static/upload/".date ( 'Ymd').'/'; // 接收文件目录

            if (! file_exists ( $path )) {
                mkdir ( "$path", 0777, true );
            }
            // 生成的文件名
            $imgs[$k] = $path.time().rand(0,99999).$ext;

            // 生成文件
            file_put_contents($imgs[$k], base64_decode($data), true);
        }
        return json_encode($imgs);
    }

/**************************************************************************************.**************************/

    //审批列表页
    public function examine(){
        $keywords = isset($_POST['keywords'])?$_POST['keywords']:null;
        $user_id = $this->qyoauth->getUserid();
        if ($keywords!==null){
            $like['u.name'] = $keywords;
            $or_like['cus_name'] = $keywords;
            $list = $this->db
                ->select('r.id,u.name,avatar,r.userid,cus_name,pmpname,cause,money,r.ac_time,r.status')
                ->where('director',$user_id)
                ->like($like)
                ->or_like($or_like)
                ->join('qy_user u','u.userid = r.userid')
                ->join('bzh_pmpcus c','c.id = r.customer_id')
                ->join('bzh_pm p','p.id = r.product_id')
                ->order_by('r.ac_time desc')
                ->get('bzh_reimbursement r')
                ->result_array();
        }else{
            $list = $this->db
                ->select('r.id,u.name,avatar,r.userid,cus_name,pmpname,cause,money,r.ac_time,r.status')
                ->where('director',$user_id)
                ->join('qy_user u','u.userid = r.userid')
                ->join('bzh_pmpcus c','c.id = r.customer_id')
                ->join('bzh_pm p','p.id = r.product_id')
                ->order_by('r.ac_time desc')
                ->get('bzh_reimbursement r')
                ->result_array();
        }


        $wait = [];
        $success = [];
        foreach ($list as $k=>$v){
            if ($v['status']==0){
                $wait[$v['name']][]=$v;
            }else{
                $success[$v['name']][]=$v;
            }
        }


        $this->load->view('reimbursement/examine',[
            'wait' =>$wait,
            'success' => $success,
            'keywords' =>$keywords
        ]);
    }


    //审批详情页
    public function detail(){
        $id = (int)$this->uri->segment(4);
        if ($id > 0){
            //获取该报销记录详情
            $user_id = $this->qyoauth->getUserid();
            $where['r.id']       = $id;
            $detail = $this->db
                ->select('r.id,u.name,avatar,r.director,r.userid,cus_name,pmpname,typename,cause,money,is_online,r.happend_time,r.ac_time,r.status,r.has_invoice,r.photos')
                ->where($where)
                ->join('qy_user u','u.userid = r.userid')
                ->join('bzh_pmpcus c','c.id = r.customer_id')
                ->join('bzh_pm p','p.id = r.product_id')
                ->join('bzh_btype b','b.id = r.type')
                ->get('bzh_reimbursement r')
                ->row_array();
            $type = 'none';
            if ($detail){
                $detail['is_online'] = (int)$detail['is_online']===1?'线上':'线下';
                $detail['has_invoice'] = (int)$detail['has_invoice']===2?'有':'无';
                $detail['photos'] = json_decode($detail['photos']);

                if ($user_id==$detail['userid'])$type='self';
                if ($user_id==$detail['director'])$type='director';
                if ($user_id==0)$type='final';
            }

            $this->load->view('reimbursement/detail',[
                'detail' =>$detail,
                'type'  =>$type
            ]);
        }

    }

    //审批操作

    public function doExamine(){
        $ids = $this->input->post('id');
        $type = $_POST['type'];
        $final = isset($_POST['final'])?true:false;
        //是否为终审
        if ($final||$type==2){
            $where['status'] = 1;
        }else{
            $where['status'] = 0;
            $where['director'] = $this->qyoauth->getUserid();
        }
        $re = $this->db
            ->where_in('id',$ids)
            ->where($where)
            ->set('status',$type)
            ->update('bzh_reimbursement');
        if ($re){
            $url = site_url('Reimbursement/index/departments');
            if(!$final){
//                $this->sendMsg(6666241,'您有新的报销待审批');
                $url = site_url('Reimbursement/index/examine');
            }
            return $this->ajaxReturn(1,'审批成功',$url);
        }else{
            return $this->ajaxReturn(0,'审批失败');
        }

    }

    //部门列表
    /*todo:$user_id注释取消*/
    public function departments(){
        $user_id = 0;//$this->qyoauth->getUserid();
        if ($user_id == 0){
            $list = $this->db
                ->select('u.name,avatar,r.userid,r.ac_time,GROUP_CONCAT(r.status) as status,GROUP_CONCAT(r.money) as money')
                ->join('qy_user u','u.userid = r.userid')
                ->join('bzh_pmpcus c','c.id = r.customer_id')
                ->join('bzh_pm p','p.id = r.product_id')
                ->order_by('r.ac_time desc')
                ->group_by('r.userid')
                ->get('bzh_reimbursement r')
                ->result_array();
           foreach ($list as $k=>$v){
               isset($list[$k]['total_money'])?:$list[$k]['total_money']=0;
               $status = explode(',',$v['status']);
               $money = explode(',',$v['money']);
               foreach ($status as $sk=>$s){
                   if($s==1){
                      $list[$k]['total_money'] += $money[$sk];
                   }
               }
           }
        }

        $this->load->view('reimbursement/departments',[
            'users'=>$list
        ]);

    }

    //按人终审
    public function personal(){
        $user_id = (int)$this->uri->segment(4);
        $list = $this->db
            ->select('r.id,u.name,r.userid,cus_name,pmpname,cause,money,r.ac_time,r.status')
            ->where('r.userid',$user_id)
            ->where('r.status !=',0)
            ->join('qy_user u','u.userid = r.userid')
            ->join('bzh_pmpcus c','c.id = r.customer_id')
            ->join('bzh_pm p','p.id = r.product_id')
            ->get('bzh_reimbursement r')
            ->result_array();
        $wait = [];
        $log = [];
        foreach ($list as $v){
            if ($v['status']==1){
                //待终审
                $wait[] = $v;
            }else{
                //记录
                $m = date('m',strtotime($v['ac_time']));
                $log[$m][] = $v;
            }
        }

        $this->load->view('reimbursement/personal',[
            'wait' =>$wait,
            'log' => $log
        ]);

    }
/**************************************************************************************.**************************/

    //发消息通知
    private function sendMsg($toUser,$url){
        $this->load->library('weixin/qymessage');
        $msg=array(
            "touser"=>$toUser,
            "msgtype"=>"textcard",
            "agentid"=>0,
            "textcard"=>array(
                "title" => "审批申请",
                "description" => "<div class=\"gray\">".date('Y年m月d日')."</div> <div class=\"normal\">有一个发送给你的报销待审批</div><div class=\"highlight\">请及时处理</div>",
                "url" => $url,
                "btntxt"=>"查看详情"
            )
        );
        $this->qymessage->sendMessage($msg);
    }

    //返回
    public function ajaxReturn($code,$msg,$url=''){
        echo json_encode([
            'code'=>$code,
            'msg'=>$msg,
            'url'=>$url
        ]);
    }

}