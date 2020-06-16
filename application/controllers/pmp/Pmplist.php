<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Pmplist extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$this->user = $this->auth_model->checkPower(-1);
		$this->load->database();
	}
	//项目管理首页
	public function index() {

		//项目参与人员
		$deps = $this->db->query('select id,parentid as pid,name from qy_department where `parentid` != 0 order by `order`')->result();
		foreach ($deps as $key => $va) {
			if ($va->id == 11) {
				continue;
			}

			$users = $this->db->select('userid,name')->where('department', $va->id)->order_by('department ASC')->get('qy_user')->result();
			foreach ($users as $k => $v) {
//				$name = $va->name . '-' . $v->name;
				$name = $v->name;
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
		$this->load->view('newadmin/pmp/index', $data);
	}
	//新增与编辑
	public function edit($id = 0) {
		$list = $this->db->select('*')->where('id', $id)->get('bzh_pm')->row();
		$data['list'] = $list;
		$plas = $this->db->select('userid')->where('pm_id', $id)->get('bzh_players')->result();
		foreach ($plas as $key => $v) {
			$data['pla_id'][] = $v->userid;
		}

		echo json_encode($data);

	}
	public function ajax_edit() {
		$data = $_POST['data'];
		//var_dump($data);die;
		//报销类型表
		//$this->addplaytype($data['playtype']);
		//处理参与人员id
		$plaid = $data['pla_id'];
		unset($data['pla_id']);

		if ($data['id'] == '') {
			unset($data['id']);
			$data['ctime'] = date('Y-m-d H:i:s', time());

			$res = $this->db->insert('bzh_pm', $data);
			$pm_id = $this->db->insert_id();
			if ($res) {
				$this->check_plas($data['userid'], $plaid, $pm_id);
				echo 1;
			}
		} else {
			$res = $this->db->where('id', $data['id'])->update('bzh_pm', $data);
			if ($res) {

				foreach ($plaid as $v) {
					$info = array();
					$id = $this->db->select('*')->where('pm_id', $data['id'])->where('userid', $v)->get('bzh_players')->row();
					if (isset($id)) {
						$info['id'] = $id->id;
						$info['pm_id'] = $data['id'];
						$info['userid'] = $v;
						$info['ctime'] = date('Y-m-d H:i:s', time());
						$this->db->where('id', $info['id'])->update('bzh_players', $info);

					} else {
						$info['pm_id'] = $data['id'];
						$info['userid'] = $v;
						$info['ctime'] = date('Y-m-d H:i:s', time());
						$this->db->insert('bzh_players', $info);
					}

				}

				echo 1;
			}
		}

	}
	//添加项目单号
	public function ajax_code($id) {
		$data = $_POST['pmpcode'];
		$res = $this->db->set('pmpcode', $data)->where('id', $id)->update('bzh_pm');
		if ($res) {
			echo 1;
		}
	}
	//新增报销类型
	public function addplaytype($typename) {
		$res = $this->db->select('*')->where('typename', $typename)->get('bzh_btype')->row();
		if (empty($res)) {
			$data['typename'] = $typename;
			$data['ctime'] = date('Y-m-d H:i:s', time());
			$this->db->insert('bzh_btype', $data);
		}
	}
	//参与人员处理
	public function check_plas($userid, $plaid, $pm_id, $id = 0) {

		if (in_array($userid, $plaid)) {

			foreach ($plaid as $v) {
				$info['pm_id'] = $pm_id;
				$info['userid'] = $v;
				$info['ctime'] = date('Y-m-d H:i:s', time());
				$this->db->insert('bzh_players', $info);
			}

		}

	}
	//取消参与人员
	public function del_pla() {
		$data = $this->input->post();
		$res = $this->db->where('pm_id', $data['pmid'])->where('userid', $data['userid'])->delete('bzh_players');

		if ($res) {
			echo 1;
		}

	}
	//获取项目列表
	public function ajaxpmp() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$text = $this->input->post('text');
		$is_pay = $this->input->post('is_pay');
		$is_vioce = $this->input->post('is_vioce');
		$pmpmoney = $this->input->post('pmpmoney');
		$search_cus = $this->input->post('search_cus');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];

		$all = $this->db->count_all_results('bzh_pm');
		$uname = $this->db->select('userid')->where('qy_user.name', $text)->get('qy_user')->row();
		if ($uname) {
			$uname = $uname->userid;
		}

		$this->db->start_cache();

		if (!empty($begin)) {
			$this->db->where('bzh_pm.ctime >=', $begin);
		}
		if (!empty($end)) {
			$this->db->where('bzh_pm.ctime <', date("Y-m-d", strtotime($end . " +1 day")));
		}

		if (!empty($is_pay)) {
			$this->db->where('bzh_pm.is_pay', $is_pay);
		}
		if (!empty($is_vioce)) {
			$this->db->where('bzh_pm.is_vioce', $is_vioce);
		}
		if (!empty($search_cus)) {
			$this->db->where('cus_id', $search_cus);
		}
		if (!empty($text)) {
			$this->db->group_start();
			$this->db->like('bzh_pm.pmpcode', $text);
			if ($uname) {
				$this->db->or_like('bzh_pm.userid', $uname);
			}

			$this->db->or_like('bzh_pm.pmpname', $text);
			$this->db->group_end();
		}
		$this->db->stop_cache();

		$some = $this->db->count_all_results('bzh_pm');
		$list = $this->db->select('bzh_pmpcus.cus_name,bzh_pmpcus.id as cus_id,qy_user.name,bzh_pm.*')->join('bzh_pmpcus', 'bzh_pmpcus.id=bzh_pm.cus_id')->join('qy_user', 'bzh_pm.userid=qy_user.userid')->order_by('bzh_pm.ctime desc')->order_by('bzh_pm.pmpcode ASC')->get('bzh_pm', $length, $start)->result();
		//获取项目报销的累计金额
		foreach ($list as $key => &$v) {
			$this->db->flush_cache();
			$all_pays = $this->db->select('sum(money) as all_pay')->where('product_id', $v->id)->where('status', 2)->get('bzh_reimbursement')->row();

			if (!empty($all_pays->all_pay)) {
				$v->all_pay = $all_pays->all_pay;
			} else {
				$v->all_pay = '0';

			}
		}

		$data = array(
			"draw" => (int) $draw,
			"recordsTotal" => $all,
			"recordsFiltered" => $some,
			"data" => $list,
		);
		echo json_encode($data);
	}
//获取参与人员
	public function get_pla($id) {
		$list = $this->db->select('qy_user.name as uname,qy_department.name as dname,bzh_players.*')->where('pm_id', $id)->join('qy_user', 'qy_user.userid=bzh_players.userid')->join('qy_department', 'qy_user.department=qy_department.id')->order_by('id')->get('bzh_players')->result();
		$data['num'] = count($list);
		$data['list'] = $list;

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	public function ajaxuserlist() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query = "select userid,count(*) as num from bzh_dailymeal_main where type=1";
		if (!empty($begin)) {
			$query .= " and time>='$begin'";
		}

		if (!empty($end)) {
			$query .= " and time<'" . date("Y-m-d", strtotime($end . " +1 day")) . "'";
		}

		$query = "select u.name,m.* from ($query group by userid) m join qy_user u on u.userid=m.userid";

		$list = $this->db->query($query)->result();
		$all = count($list);

		$data = array(
			"draw" => (int) $draw,
			"recordsTotal" => $all,
			"recordsFiltered" => $all,
			"data" => $list,
		);
		echo json_encode($data);
	}
	//查看项目金额统计
	public function check_all($id) {
		$list = $this->db->select('qy_user.name as uname,bzh_reimbursement.*')->where('product_id', $id)->where('is_pay', 1)->join('qy_user', 'qy_user.userid=bzh_reimbursement.userid')->order_by('bzh_reimbursement.userid')->get('bzh_reimbursement')->result();
		$allnum = $this->db->select('sum(money) as allnum ')->where('product_id', $id)->where('is_pay', 1)->order_by('userid')->get('bzh_reimbursement')->result();

		$userlist = $this->db->select('qy_user.name as uname,sum(bzh_reimbursement.money) as num_user,bzh_reimbursement.*')->where('product_id', $id)->where('is_pay', 1)->join('qy_user', 'qy_user.userid=bzh_reimbursement.userid')->group_by('bzh_reimbursement.userid')->get('bzh_reimbursement')->result();

		$data = array('list' => $list, 'userlist' => $userlist, 'allnum' => $allnum);

		echo json_encode($data);
	}
//删除
	public function ajax_del($id) {
		if ($id) {
			$res = $this->db->where('id', $id)->delete('bzh_pm');
			$this->db->where('pm_id', $id)->delete('bzh_players');
			if ($this->db->affected_rows()) {
				echo 1;
			}
		}
	}

}