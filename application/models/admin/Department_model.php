<?php

class Department_model extends CI_Model {
    protected  $table="assessment_department";
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('url'); //url函数库文件
	}

	/**
     * 初始化部门
	 */
	public function init($up = 1) {

        $departments  = $this->db->select('id,name')->where(array(
            'parentid' => $up
        ))->get('qy_department')->result_array();

        if($departments){
            $departmentIds = array_column($departments,'id');
            $existIds = $this->db->select('id')->where_in('id',$departmentIds)->get($this->table)->result_array();
            $existIds=empty($existIds)?array():array_column($existIds,'id');
            foreach($departments as $v){
                if(in_array($v['id'],$existIds)){
                    break;
                }else{
                    $insert[]=$v;
                }
            }
            !empty($insert) && $this->db->insert_batch($this->table,$insert);
        }

	}

    /**
     * @param $data
     */
	public function insert($data){
        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

    /**
     * @param $data
     * @param $where
     */
    public function update($data,$where=array()){
	    empty($where) && $this->db->where($where);
	    $this->db->update($this->table,$data);
    }

    /**
     *  查询
     */
    public function get($where=array(),$limit =0){

        $where && $this->db->where($where);

        $query = $this->db->get($this->table);
        if($limit === 1){
            return $query->row_array();
        }
        return $query->result_array();
    }

}