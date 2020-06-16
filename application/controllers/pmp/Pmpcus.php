<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Pmpcus extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$this->user = $this->auth_model->checkPower(-1);
		$this->load->database();
	}
	//项目客户首页
	public function index() {
		$this->load->view('newadmin/pmp/cusindex');
	}
	//获取客户列表
	public function ajaxpmp() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');

		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];
		$all = $this->db->count_all_results('bzh_pmpcus');
		$this->db->start_cache();

		if (!empty($begin)) {
			$this->db->where('ctime >=', $begin);
		}

		if (!empty($end)) {
			$this->db->where('ctime <', date("Y-m-d", strtotime($end . " +1 day")));
		}

		$this->db->stop_cache();

		$some = $this->db->count_all_results('bzh_pmpcus');
		$list = $this->db->select('*')->order_by('id')->get('bzh_pmpcus', $length, $start)->result();

		$data = array(
			"draw" => (int) $draw,
			"recordsTotal" => $all,
			"recordsFiltered" => $some,
			"data" => $list,
		);

		echo json_encode($data);
	}
	//新增与编辑
	public function ajax_edit($id) {
		$data = $_POST;

		if ($id == 0) {
			unset($data['id']);
			$data['ctime'] = date('Y-m-d H:i:s', time());
			$res = $this->db->insert('bzh_pmpcus', $data);
		} else {
			$res = $this->db->where('id', $id)->update('bzh_pmpcus', $data);
		}
		if ($res) {
			echo 1;
		}
	}
	//删除
	public function ajax_del($id) {
		if ($id) {
			$res = $this->db->where('id', $id)->delete('bzh_pmpcus');
			if ($this->db->affected_rows()) {
				echo 1;
			}
		}
	}

}