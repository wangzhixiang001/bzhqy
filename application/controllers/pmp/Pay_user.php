<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Pay_user extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$this->load->library('weixin/qyoauth');
		$this->user = $this->auth_model->checkPower(-1);
		$this->load->database();
	}
	//付款管理
	public function index() {
		//项目参与人员
		$deps = $this->db->query('select id,parentid as pid,name from qy_department where `parentid` != 0 order by `order`')->result();
		foreach ($deps as $key => $va) {

			$users = $this->db->select('userid,name')->where('department', $va->id)->order_by('department ASC')->get('qy_user')->result();
			foreach ($users as $k => $v) {
				$name = $va->name . '-' . $v->name;
				$data['list'][] = (object) array(
					'label' => $name,
					'value' => $v->userid,
				);
			}

		}
		//项目所属客户
		$data['cus'] = $this->db->select('id,cus_name')->order_by('id')->get('bzh_pmpcus')->result();
		//项目所属报销类型
		$data['btype'] = $this->db->select('id,typename')->order_by('id')->get('bzh_btype')->result();
		$this->load->view('newadmin/pmp/explist', $data);
	}
	public function check_pass() {
		$pass = $this->input->post('pass');
		$userid = $this->session->userdata('code');
		if (empty($userid)) {
			$data['code'] = 0;
			$data['msg'] = '您没有权限付款!';
		} else {

			$res = $this->db->select('*')->where('userid', $userid)->where('pass', md5($pass))->get('bzh_pay_user')->row();
			if (!empty($res)) {
				$data['code'] = 1;
				$data['msg'] = '密码正确';
			} else {
				$data['code'] = 0;
				$data['msg'] = '密码不正确';
			}
		}

		echo json_encode($data);
	}
	public function list_pay() {
		$userid = $this->input->post('userid');
		$yes_c = $this->db->select('qy_user.name as uname,bzh_pmpcus.cus_name,bzh_pm.pmpname,bzh_btype.typename,bzh_reimbursement.*')
			->where('bzh_reimbursement.status', 2)
			->where('bzh_reimbursement.is_pay', 0)
			->where('bzh_reimbursement.userid', $userid)
			->join('qy_user', 'qy_user.userid =bzh_reimbursement.userid ')
			->join('bzh_pm', 'bzh_pm.id =bzh_reimbursement.product_id ')
			->join('bzh_pmpcus', 'bzh_reimbursement.customer_id=bzh_pmpcus.id')
			->join('bzh_btype', 'bzh_reimbursement.type=bzh_btype.id')
			->get('bzh_reimbursement')->result_array();

		if ($yes_c) {
			echo json_encode($yes_c);
		}
	}
	//修改付款密码
	public function edit_pass() {
		$this->load->view('newadmin/pmp/expinfo');
	}
	public function ajax_edit() {
		$data = $this->input->post();
		$code = $this->session->userdata('code');
		$info = $this->db->select('pass')->where('userid', $code)->get('bzh_pay_user')->row();
		if ($info) {
			$oldpsw = $info->pass;
			if ($oldpsw != md5($data['oldpsw'])) {
				return $this->ajaxReturn(0, '原始密码不正确!');
			}
			$newpsw = md5($data['newpsw']);
			$res = $this->db->set('pass', $newpsw)->where('userid', $code)->update('bzh_pay_user');
			if ($res) {
				return $this->ajaxReturn(1, '修改成功!');
			} else {
				return $this->ajaxReturn(1, '修改失败!');
			}
		}

	}
	//josn 返回
	public function ajaxReturn($code, $msg, $url = '') {
		echo json_encode([
			'code' => $code,
			'msg' => $msg,
			'url' => $url,
		]);
	}
}