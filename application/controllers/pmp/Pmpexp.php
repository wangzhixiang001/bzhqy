<?php
// 127.0.0.1/kfweixin/index.php/dailymeal/admin
class Pmpexp extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$this->load->library('weixin/qyoauth');
		$this->user = $this->auth_model->checkPower(-1);
		$this->load->database();
	}
	//项目管理首页
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
	/****************************************个人报销汇总*********************************************/
	//个人报销汇总
	public function one_apply() {
		$begin = date('Y-m-01', strtotime(date("Y-m-d")));
		$end = date('Y-m-d', strtotime("$begin +1 month -1 day"));

		$moth_pay = $this->db->select('sum(money) as yes_money')
			->where('status', 2)
			->where('is_pay', 0)
			->where('ac_time >=', $begin)
			->where('ac_time <', $end)
			->get('bzh_reimbursement')->row()->yes_money;
		$no_pay = $this->db->select('sum(money) as yes_money')
			->where('status <', 2)
			->where('is_pay', 0)
			->get('bzh_reimbursement')->row()->yes_money;
		$all_pay = $this->db->select('sum(money) as yes_money')
			->where('status', 2)
			->where('is_pay', 1)
			->get('bzh_reimbursement')->row()->yes_money;
		//var_dump($this->db->last_query());die;
		$this->load->view('newadmin/pmp/one_apply', ['moth_pay' => $moth_pay, 'no_pay' => $no_pay, 'all_pay' => $all_pay]);
	}
	//获取报销汇总
	public function ajax_one_apply() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$type = $this->input->post('type');
		$text = $this->input->post('text');
		$is_pay = $this->input->post('is_pay');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];

		$all = $this->db->count_all_results('bzh_reimbursement');
		$dname = $this->db->select('id')->like('qy_department.name', $text)->get('qy_department')->row();
		if ($dname) {
			$dname = $dname->id;
			$unames = $this->db->select('*')->like('qy_user.department', $dname)->get('qy_user')->result();

		}
		$uname = $this->db->select('*')->like('qy_user.name', $text)->get('qy_user')->row();
		$this->db->start_cache();
		if (!empty($text)) {
			if (is_numeric($text)) {
				$this->db->or_like('bzh_reimbursement.userid', $text);
			}
			if ($dname) {
				$this->db->group_start();
				foreach ($unames as $key => $v) {
					$this->db->or_like('bzh_reimbursement.userid', $v->userid);

				}
				$this->db->group_end();
			}
			if ($uname) {
				$this->db->group_start();
				$this->db->or_like('bzh_reimbursement.userid', $uname->userid);
				$this->db->group_end();
			}

		}
		if (!empty($begin)) {
			$this->db->where('bzh_reimbursement.ac_time >=', $begin);
		}
		if (!empty($end)) {
			$this->db->where('bzh_reimbursement.ac_time <', date("Y-m-d", strtotime($end . " +1 day")));
		}

		$this->db->stop_cache();

		$list = $this->db->select('uqy.name as uname,uqy.department,bzh_reimbursement.*')
			->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
			->group_by('bzh_reimbursement.userid')
			->order_by('bzh_reimbursement.status desc ')->get('bzh_reimbursement', $length, $start)->result();
		//var_dump($this->db->last_query());die;
		$some = count($list);
		//未审核
		$no_c = $this->db->select('userid,sum(money) as yes_money')
			->where('status<', 2)
			->group_by('userid')
			->get('bzh_reimbursement')->result();

		foreach ($list as $key => $v) {
			$v->dname = $this->get_dname($v->department);
			$v->yes_c = $this->get_yesc($v->userid);
			$v->no_c = $this->get_noc($v->userid);
			$v->pays = $this->get_pays($v->userid);
		}

		$data = array(
			"draw" => (int) $draw,
			"recordsTotal" => $all,
			"recordsFiltered" => $some,
			"data" => $list,
		);
		echo json_encode($data);
	}
/*********************************END*******************************************/
	//是否付款
	public function get_pays($userid) {
		$this->db->flush_cache();
		$list = $this->db->select('*')
			->where('userid', $userid)
			->where('is_pay', 0)
			->where('status', 2)
			->get('bzh_reimbursement')->result();
		if (!empty($list)) {
			return 1;
		}
	}
	//部门名称
	public function get_dname($department) {
		$this->db->flush_cache();
		$res = $this->db->select('name')->where('id', $department)->get('qy_department')->row()->name;
		return preg_replace('|[0-9]+|', '', $res);
	}
	//
	public function get_yesc($userid) {
		//已审核金额
		$yes_c = $this->db->select(' userid,sum(money) as yes_money')
			->where('status', 2)
			->where('is_pay', 0)
			->where('userid', $userid)
			->group_by('userid')
			->get('bzh_reimbursement')->result();
		if (!empty($yes_c)) {
			return $yes_c[0]->yes_money / 100;
		} else {
			return '00.00';
		}

	}
	public function get_noc($userid) {
		//未审核金额
		$no_c = $this->db->select(' userid,sum(money) as no_money')
			->where('status <', 2)
			->where('userid', $userid)
			->group_by('userid')
			->get('bzh_reimbursement')->result();

		if (!empty($no_c)) {
			return $no_c[0]->no_money / 100;
		} else {
			return '00.00';
		}

	}
	//参与人员处理
	public function check_plas($userid, $plaid, $pm_id, $id = 0) {

		if (strstr($plaid, ',')) {
			$plas = explode(',', $plaid);
			$plas[] = $userid;
		} else {
			$plas[] = $plaid;
			$plas[] = $userid;
		}

		foreach ($plas as $v) {
			$info['pm_id'] = $pm_id;
			$info['userid'] = $v;
			$info['ctime'] = date('Y-m-d H:i:s', time());
			$this->db->insert('bzh_players', $info);
		}

	}

	//获取项目列表
	public function ajaxpmp() {
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$text = $this->input->post('text');
		$is_pay = $this->input->post('is_pay');
		$has_invoice = $this->input->post('has_invoice');
		$status = $this->input->post('status');
		$search_cus = $this->input->post('search_cus');
		$draw = $_POST['draw'];
		$start = $_POST['start'];
		$length = $_POST['length'];

		$all = $this->db->count_all_results('bzh_reimbursement');
		$tag = array('', 'userid', 'pmpname', 'pmpcode');

		$uname = $this->db->select('userid')->where('qy_user.name', $text)->get('qy_user')->row();
		if ($uname) {
			$uname = $uname->userid;
		}
		$pname = $this->db->select('*')->where('bzh_pm.pmpname', $text)->get('bzh_pm')->row();
		if ($pname) {
			$pname = $pname->id;
		}
		$pcode = $this->db->select('*')->where('bzh_pm.pmpcode', $text)->get('bzh_pm')->row();
		if ($pcode) {
			$pcode = $pcode->id;
		}
		$this->db->start_cache();
		if (!empty($text)) {
			$this->db->group_start();

			if ($uname) {
				$this->db->or_like('bzh_reimbursement.userid', $uname);
			}
			if ($pname) {
				$this->db->or_like('bzh_reimbursement.product_id', $pname);
			}
			if ($pcode) {
				$this->db->or_like('bzh_reimbursement.product_id', $pcode);
			}
			$this->db->group_end();
		}
		if (!empty($begin)) {
			$this->db->where('bzh_reimbursement.ac_time >=', $begin);
		}
		if (!empty($end)) {
			$this->db->where('bzh_reimbursement.ac_time <', date("Y-m-d", strtotime($end . " +1 day")));
		}

		if ($is_pay != 2) {
			$this->db->where('bzh_reimbursement.is_pay', $is_pay);
		}
		if (!empty($has_invoice)) {
			$this->db->where('bzh_reimbursement.has_invoice', $has_invoice);
		}
		if ($status != 4) {
			$this->db->where('bzh_reimbursement.status', $status);
		}
		if (!empty($search_cus)) {
			$this->db->where('bzh_reimbursement.customer_id', $search_cus);
		}
		$this->db->stop_cache();

		$some = $this->db->count_all_results('bzh_reimbursement');
		$list = $this->db->select('bzh_pmpcus.cus_name,uqy.name as uname, dqy.name as dname,bzh_pm.pmpname,bzh_pm.pmpdesc,bzh_pm.pmpcode,bzh_btype.typename,bzh_reimbursement.*')
			->join('bzh_pmpcus', 'bzh_pmpcus.id=bzh_reimbursement.customer_id')
			->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
			->join('qy_user as dqy', 'bzh_reimbursement.director=dqy.userid')
			->join('bzh_pm', 'bzh_reimbursement.product_id=bzh_pm.id')
			->join('bzh_btype', 'bzh_reimbursement.type=bzh_btype.id')
			->order_by('bzh_reimbursement.ac_time')->get('bzh_reimbursement', $length, $start)->result();

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
		$list = $this->db->select('qy_user.name as uname,,qy_department.name as dname,bzh_players.*')->where('pm_id', $id)->join('qy_user', 'qy_user.userid=bzh_players.userid')->join('qy_department', 'qy_user.department=qy_department.id')->order_by('id')->get('bzh_players')->result();
		$data['num'] = count($list);
		$data['list'] = $list;

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	//查看票据
	public function check_bmp($id) {
		$list = $this->db->select('photos')->where('id', $id)->get('bzh_reimbursement')->row()->photos;
		$data['list'] = json_decode($list);

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
	//付款
	public function send_pay($userid, $id) {
		$yes_c = $this->input->post('yes_c');
		$list = $this->db->select('bzh_reimbursement.*,bzh_pm.pmpname')->where('userid', $userid)->where('status', 2)->join('bzh_pm', 'bzh_pm.id=bzh_reimbursement.product_id')->order_by('id')->get('bzh_reimbursement')->row();
		$res = $this->sendQy($list, $yes_c);
		echo $res;

	}
	//红包付款
	private function sendRed($list, $yes_money) {
		$this->load->library('weixin/qypay', [
			'cert_path' => $_SERVER['DOCUMENT_ROOT'] . '/bzhqy/data/cert/apiclient_cert.pem',
			'key_path' => $_SERVER['DOCUMENT_ROOT'] . '/bzhqy/data//cert/apiclient_key.pem',
			'mchid' => '1367796902', //'1293344901'
			'appid' => 'wx2ba3b00d2d6a9e39', //'wx9ba3c01032bd0679'
			'key' => '028e9979a6baf58f04300144b8b3d171', //'3948482C52A21955C82B0AE09B267004'
		]);
		//订单编号
		$partner_trade_no = time() . $list->userid;
		$res = $this->qypay->sendRedpacket([
			'mch_billno' => $partner_trade_no,
			're_openid' => $this->qyoauth->convertToOpenid($list->userid), //'ogjuEvy1eOp8E6ozRJGFjMz1VABE'$this->qyoauth->convertToOpenid($list->userid)
			'total_amount' => $yes_money * 100,
			'act_name' => '项目报销',
			'wishing' => '红包',
			'remark' => '注释',
			// 'agentid' => 0,
			'scene_id' => 'PRODUCT_1',
			'send_name' => '红包',

		]);

		if ($res[0] == true) {
			$data = array(
				'payment_no' => $partner_trade_no,
				'record_id' => $list->id,
				'pay_money' => 1, //$list->money
				'pay_status' => 1,
				'pay_scode' => $res[1],
				'pay_msg' => $res[2],
				'pay_time' => date('Y-m-d', time()),
			);
			$info = $this->db->insert('bzh_payrecord', $data);
			$this->db->set('is_pay', 1)->where('userid', $list->userid)->where('status', 2)->update('bzh_reimbursement');
			$this->sendMsg($list->userid, '您的报销已到账请及时查收!');
			return 1;
		} else {

			$data = array(
				'payment_no' => $partner_trade_no,
				'record_id' => $list->id,
				'pay_money' => $list->money,
				'pay_status' => -1,
				'pay_scode' => $res[1],
				'pay_msg' => $res[2],
				'pay_time' => date('Y-m-d', time()),
			);
			$info = $this->db->insert('bzh_payrecord', $data);
			return 0;

		}

	}
	//企业付款
	private function sendQy($list, $yes_money) {
		$this->load->library('weixin/qypay', [
			'cert_path' => $_SERVER['DOCUMENT_ROOT'] . '/bzhqy/data/cert/apiclient_cert.pem',
			'key_path' => $_SERVER['DOCUMENT_ROOT'] . '/bzhqy/data//cert/apiclient_key.pem',
			'mchid' => '1367796902', //'1293344901'
			'appid' => 'wx2ba3b00d2d6a9e39', //'wx9ba3c01032bd0679'
			'key' => '028e9979a6baf58f04300144b8b3d171', //'3948482C52A21955C82B0AE09B267004'
		]);
		//订单编号
		$partner_trade_no = time() . $list->userid;
		$res = $this->qypay->sendQyPay([
			'partner_trade_no' => $partner_trade_no,
			'openid' => $this->qyoauth->convertToOpenid($list->userid), //'oZKSsjh9ILluOb3Os56queDHfBk8'
			'amount' => $list->money, //付款金额
			'desc' => $list->pmpname . '-' . $list->cause,

		]);

		if ($res[0] == true) {
			$data = array(
				'payment_no' => $partner_trade_no,
				'record_id' => $list->id,
				'pay_money' => $list->money,
				'pay_status' => 1,
				'pay_scode' => $res[1],
				'pay_msg' => $res[2],
				'pay_time' => date('Y-m-d H:i:s', time()),
			);
			$info = $this->db->insert('bzh_payrecord', $data);
			$this->db->set('is_pay', 1)->where('id', $list->id)->update('bzh_reimbursement');
			$this->sendMsg($list->userid, '您的报销已到账请及时查收!');
			return 1;
		} else {

			$data = array(
				'payment_no' => $partner_trade_no,
				'record_id' => $list->id,
				'pay_money' => $list->money,
				'pay_status' => -1,
				'pay_scode' => $res[1],
				'pay_msg' => $res[2],
				'pay_time' => date('Y-m-d H:i:s', time()),
			);
			$info = $this->db->insert('bzh_payrecord', $data);
			$this->sendMsg($list->userid, '付款失败!');
			return 0;

		}

	}

	//发消息通知
	private function sendMsg($toUser, $msg) {
		$this->load->library('weixin/qymessage');
		$msg = array(
			"touser" => $toUser,
			"msgtype" => "text",
			"agentid" => 0,
			"text" => array(
				"content" => $msg,
			),
		);
		$this->qymessage->sendMessage($msg);
	}
	//导出报销记录
	public function out() {
		$fileTitle = iconv('utf-8', 'gbk', '报销记录');
		set_time_limit(0);
		header('Content-Type:application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename="' . $fileTitle . '.csv"');
		header('Cache-Control:max-age=0');

		// 打开PHP文件句柄，php://output 表示直接输出到浏览器
		$fp = fopen('php://output', 'a');

		$head = array("报销人id", "报销人姓名", '项目名称', '项目单号', '项目描述', '所属客户', '参与人员', '报销类型', '报销事由', '报销金额', '审核状态', '审核人', '是否付款', '申请时间', '票据');
		foreach ($head as $i => $v) {
			// CSV的Excel支持GBK编码，一定要转换，否则乱码
			$head[$i] = iconv('utf-8', 'gbk', $v);
		}
		fputcsv($fp, $head);
		//计数器
		$cnt = 0;
		$limit_times = 0;
		//每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
		$limit = 10000;
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$type = $this->input->get('type');
		$text = $this->input->get('text');
		$is_pay = $this->input->get('is_pay');
		$has_invoice = $this->input->get('has_invoice');
		$status = $this->input->get('status');

		$this->db->start_cache();
		if (!empty($text)) {
			$tag = array('', 'userid', 'pmpname', 'pmpcode');
			$this->db->where($tag[$type], $text);
		}
		if ($is_pay != 2) {
			$this->db->where('bzh_reimbursement.is_pay', $is_pay);
		}
		if (!empty($has_invoice)) {
			$this->db->where('bzh_reimbursement.has_invoice', $has_invoice);
		}
		if ($status != 4) {
			$this->db->where('bzh_reimbursement.status', $status);
		}
		if (!empty($begin)) {
			$this->db->where('bzh_reimbursement.ac_time >=', $begin);
		}
		if (!empty($end)) {
			$this->db->where('bzh_reimbursement.ac_time <', date("Y-m-d", strtotime($end . " +1 day")));
		}
		$this->db->stop_cache();

		$list = $this->db->select('bzh_pmpcus.cus_name,uqy.name as uname, dqy.name as dname,bzh_pm.pmpname,bzh_pm.pmpdesc,bzh_pm.pmpcode,bzh_btype.typename,bzh_reimbursement.*')
			->join('bzh_pmpcus', 'bzh_pmpcus.id=bzh_reimbursement.customer_id')
			->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
			->join('qy_user as dqy', 'bzh_reimbursement.director=dqy.userid')
			->join('bzh_pm', 'bzh_reimbursement.product_id=bzh_pm.id')
			->join('bzh_btype', 'bzh_reimbursement.type=bzh_btype.id')
			->order_by('bzh_reimbursement.id')->limit(1000)->get('bzh_reimbursement')->result_array();

		while ($list) {
			foreach ($list as &$v) {

				$cnt++;
				if ($limit == $cnt) {
					//刷新一下输出buffer，防止由于数据过多造成问题
					ob_flush();
					flush();
					$cnt = 0;
				}
				$canyu = $this->db->select('qy_user.name as uname')->where('pm_id', $v['product_id'])->join('qy_user', 'qy_user.userid=bzh_players.userid')->get('bzh_players')->result();
				$newinfo = array(
					'userid' => $v['userid'],
					'uname' => $v['uname'],
					'pmpname' => $v['pmpname'],
					'pmpcode' => $v['pmpcode'],
					'pmpdesc' => $v['pmpdesc'],
					'cus_name' => $v['cus_name'],
					'canyu' => json_encode($canyu, JSON_UNESCAPED_UNICODE),
					'typename' => $v['typename'],
					'cause' => $v['cause'],
					'money' => $v['money'],
					'status' => $v['status'] == -1 ? '驳回' : $v['status'] == 2 ? '最终审核' : $v['status'] == 1 ? '部门审核' : '未审核',
					'dname' => $v['dname'],
					'is_pay' => $v['is_pay'] == 1 ? '已付款' : '未付款',

					'ac_time' => $v['ac_time'],

				);

				if (!empty($v['photos'])) {
					foreach (json_decode($v['photos']) as $key => $value) {
						$newinfo['photos' . $key] = base_url($value);
					}
				} else {
					$newinfo['photos'] = '';
				}

				foreach ($newinfo as $key => $val) {
					$newinfo[$key] = iconv('utf-8', 'gbk', $val);
				}
				fputcsv($fp, $newinfo);
			}
			$limit_times++;
			$list = $this->db->select('bzh_pmpcus.cus_name,uqy.name as uname, dqy.name as dname,bzh_pm.pmpname,bzh_pm.pmpdesc,bzh_pm.pmpcode,bzh_btype.typename,bzh_reimbursement.*')
				->join('bzh_pmpcus', 'bzh_pmpcus.id=bzh_reimbursement.customer_id')
				->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
				->join('qy_user as dqy', 'bzh_reimbursement.director=dqy.userid')
				->join('bzh_pm', 'bzh_reimbursement.product_id=bzh_pm.id')
				->join('bzh_btype', 'bzh_reimbursement.type=bzh_btype.id')
				->order_by('bzh_reimbursement.id')->limit(1000, $limit_times * 1000)->get('bzh_reimbursement')->result_array();

		}

	}
	//导出个人报销记录
	public function out_one_apply() {
		$fileTitle = iconv('utf-8', 'gbk', '个人报销记录');
		set_time_limit(0);
		header('Content-Type:application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename="' . $fileTitle . '.csv"');
		header('Cache-Control:max-age=0');

		// 打开PHP文件句柄，php://output 表示直接输出到浏览器
		$fp = fopen('php://output', 'a');

		$head = array("报销人id", "报销人姓名", '部门', '已审核报销', '未审核报销');
		foreach ($head as $i => $v) {
			// CSV的Excel支持GBK编码，一定要转换，否则乱码
			$head[$i] = iconv('utf-8', 'gbk', $v);
		}
		fputcsv($fp, $head);
		//计数器
		$cnt = 0;
		$limit_times = 0;
		//每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
		$limit = 10000;
		$begin = $this->input->post('begin');
		$end = $this->input->post('end');
		$text = $this->input->post('text');

		$all = $this->db->count_all_results('bzh_reimbursement');
		$dname = $this->db->select('id')->like('qy_department.name', $text)->get('qy_department')->row();
		if ($dname) {
			$dname = $dname->id;
			$unames = $this->db->select('*')->like('qy_user.department', $dname)->get('qy_user')->result();

		}
		$uname = $this->db->select('*')->like('qy_user.name', $text)->get('qy_user')->row();
		$this->db->start_cache();
		if (!empty($text)) {
			if (is_numeric($text)) {
				$this->db->or_like('bzh_reimbursement.userid', $text);
			}
			if ($dname) {
				$this->db->group_start();
				foreach ($unames as $key => $v) {
					$this->db->or_like('bzh_reimbursement.userid', $v->userid);

				}
				$this->db->group_end();
			}
			if ($uname) {
				$this->db->group_start();
				$this->db->or_like('bzh_reimbursement.userid', $uname->userid);
				$this->db->group_end();
			}

		}
		if (!empty($begin)) {
			$this->db->where('bzh_reimbursement.ac_time >=', $begin);
		}
		if (!empty($end)) {
			$this->db->where('bzh_reimbursement.ac_time <', date("Y-m-d", strtotime($end . " +1 day")));
		}

		$this->db->stop_cache();

		$list = $this->db->select('uqy.name as uname,uqy.department,bzh_reimbursement.*')
			->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
			->group_by('bzh_reimbursement.userid')
			->order_by('bzh_reimbursement.status desc ')->limit(1000)->get('bzh_reimbursement')->result();

		while ($list) {
			foreach ($list as &$v) {

				$cnt++;
				if ($limit == $cnt) {
					//刷新一下输出buffer，防止由于数据过多造成问题
					ob_flush();
					flush();
					$cnt = 0;
				}
				$newinfo = array(
					'userid' => $v->userid,
					'uname' => $v->uname,
					'dname' => $this->get_dname($v->department),
					'yes_c' => $this->get_yesc($v->userid),
					'no_c' => $this->get_noc($v->userid),

				);

				foreach ($newinfo as $key => $val) {
					$newinfo[$key] = iconv('utf-8', 'gbk', $val);
				}
				fputcsv($fp, $newinfo);
			}
			$limit_times++;
			$list = $this->db->select('uqy.name as uname,uqy.department,bzh_reimbursement.*')
				->join('qy_user as uqy', 'bzh_reimbursement.userid=uqy.userid')
				->group_by('bzh_reimbursement.userid')
				->order_by('bzh_reimbursement.status desc ')->limit(1000, $limit_times * 1000)->get('bzh_reimbursement')->result();

		}

	}

}