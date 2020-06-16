<?php
class Gift_m extends CI_Model {

    public function __construct() {
        //调用父类的构造函数
        parent::__construct();
        $this->load->database();
    }
    
    public function getRemainGift($act_id){
        return $this->db->where(array('act_id'=>$act_id,'giftnum >'=>0))->order_by("chance", "asc")->get('gift_library')->result();
    }
    
    public function chooseGift($act_id){
        $temp=0;
        $allgift=$this->getRemainGift($act_id);
        $lucknum=mt_rand(1, 100000);
        foreach ($allgift as $gift){
            $temp+=$gift->chance*100000;
            if($lucknum<=$temp){
                $this->db->query("update gift_library set giftnum=giftnum-1,outnum=outnum+1 where Id=".$gift->Id." and giftnum>0");
                if($this->db->affected_rows()>0){
                    return $gift;
                }else{
                    return '';
                }
            }
        }
        return '';
    }
}