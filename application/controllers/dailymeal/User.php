<?php
use EasyWeChat\Factory;
//http://127.0.0.1/CI302/index.php/dailymeal/user
class User extends CI_Controller {

	public function __construct() {
		//调用父类的构造函数
		parent::__construct();
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('weixin/qyoauth');
	}

	//报饭入口
	public function index() {
		//$this->load->view('dailymeal/index2');return;
		$userid = $this->qyoauth->getUserid();
		$user = $this->getUserToday($userid);
		$now = time();
		//10:30前
		if ($now <= strtotime(date('Y-m-d 20:30'))) {
			if (empty($user) || $user->type != 1) {
				if ((int) date('Hi') < 2030) {
				
					$this->load->view('dailymeal/index', [
						'cantp' => $this->checkTip(),
						'end' => strtotime(date('Y-m-d') . ' 09:30:00') - time()
					]);
				} else {
					
					$this->load->view('dailymeal/waitbad');
				}

			} else {
				$this->load->view('dailymeal/quxiao', ['cantp' => $this->checkTip()]);
			}
		}
		//等开饭
		else if ((int) date('H') < 13) {
			if (empty($user) || $user->type == 0) {
				$this->load->view('dailymeal/waitbad');
			} else {
				$data['pm'] = $this->db->query("select c.pm from (select Id,(@rowNo:=@rowNo+1) as pm from bzh_dailymeal_main,(select (@rowNo :=0) ) b where time>'" . date('Y-m-d') . "' and type=1 order by Id) c left join bzh_dailymeal_main on c.Id=bzh_dailymeal_main.Id where c.Id=" . $user->Id)
					->row()->pm;
				$data['all'] = $this->db->where(['time >' => date('Y-m-d'), 'type' => 1])->count_all_results('bzh_dailymeal_main');
				$this->load->view('dailymeal/wait', $data);
			}
		}
		//1点后自动显示留言板
		else {
			$this->msg();
		}
	}

	//用户当月订饭记录
	public function info() {
		$userid = $this->qyoauth->getUserid();
		$temp = $this->db->query("select day(time) d from bzh_dailymeal_main where time>'" . date('Y-m-1') . "' and userid='$userid' and type=1")->result();
		$data['bao'] = [];
		$i = 0;
		foreach ($temp as $val) {
			$data['bao'][$i++] = $val->d;
		}
		$this->load->view('dailymeal/info', $data);
	}

	//当日报饭用户
	public function vf() {
		$data['list'] = $this->db->query("select u.userid,u.name,u.avatar,m.time,m.type,m.status,m.eat_time from bzh_dailymeal_main m join qy_user u on u.userid=m.userid  where m.time>'" . date('Y-m-d') . "' and type<3")->result();
		$this->load->view('dailymeal/yibao', $data);
	}

	//留言板
	public function msg() {
		$data['list'] = $this->db->query("select u.name,u.avatar,m.msg,m.msgtime from bzh_dailymeal_main m join qy_user u on u.userid=m.userid where m.msgtime>'" . date('Y-m-d') . "' order by m.msgtime")->result();
		$this->load->view('dailymeal/msg', $data);
	}

	//  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -
	public function bf_ajax($type = 1) {
		$userid = $this->qyoauth->getUserid();
		$user = $this->getUserToday($userid);
		if (!$user) {
			$this->db->insert('bzh_dailymeal_main', [
				'userid' => $userid,
				'type' => $type
			]);
		} else {
			$this->db->where('Id', $user->Id)->set('time', 'now()', false)->update('bzh_dailymeal_main', [
				'type' => $type
			]);
		}
		echo '1';
	}

	//就餐、补签
	public function jc_ajax() {
		$userid = $this->qyoauth->getUserid();
		if (empty($userid)) {

		}

		$user = $this->getUserToday($userid);
		if (!$user) {
			$this->db->insert('bzh_dailymeal_main', [
				'userid' => $userid,
				'status' => 2,
				'eat_time' => date('Y-m-d H:i:s')
			]);
			$status = 2;
		} else {
			$type = 1;
			$status = 1;
			if ($user->type == 0) {
				$status = $type = 2;
			}

			$this->db->where('Id', $user->Id)->set('eat_time', 'now()', false)->update('bzh_dailymeal_main', [
				'status' => $status,
				'type' => 1
			]);

		}
		$this->load->view('dailymeal/jiucan', ['status' => $status]);
	}

	public function msg_ajax() {
		$userid = $this->qyoauth->getUserid();
		$data['msg'] = $this->input->post('msg');
		if (!empty($data['msg'])) {
			$u = $this->getUserToday($userid);
			if (!$u) {
				$data['msgtime'] = date('Y-m-d H:i:s');
				$data['userid'] = $userid;
				$data['type'] = 9;
				$this->db->insert('bzh_dailymeal_main', $data);
				die('1');
			}
			if (!empty($u->msg)) {
				die('0');
			}

			$this->db->where('Id', $u->Id)->set('msgtime', 'now()', false)->update('bzh_dailymeal_main', $data);
		}
		echo 1;
	}

	public function eval_ajax() {
		$userid = $this->qyoauth->getUserid();
		$data['eval'] = '1' . $this->input->post('eval') . $this->input->post('sel');
		if (strlen($data['eval']) == 3) {
			$u = $this->getUserToday($userid);
			if (!$u || $u->type != 1) {
				die('-1');
			}
			if ($u->eval != 0) {
				die('0');
			}

			$this->db->where('Id', $u->Id)->update('bzh_dailymeal_main', $data);
			echo 1;
		}
	}

	public function more_msg($page = 1) {
		$begin = 20 * $page;
		$data = $this->db->query("select u.name,u.avatar,m.msg,m.time from bzh_dailymeal_msg m join qy_user u on u.userid=m.userid order by m.Id desc limit $begin,20")->result();
		echo json_encode($data);
	}

	public function send_msg($type = 1) {
		if ($this->checkTip(1) > 2) {
			die('2');
		}

		$userid = $this->qyoauth->getUserid();
		if (!$userid) {
			die('0');
		}

		$this->load->library('weixin/qymessage');
		if ($type) {
			$name = $this->db->select('name')->where('userid', $userid)->get('qy_user')->row();
			$name = $name ? $name->name : '猜猜我是谁';
		} else {
			$name = '叫我雷锋';
		}
		$msg = [
			'touser' => '@all',
			'msgtype' => 'text',
			'agentid' => 2,
			'text' => [
				'content' => $name . '温馨提醒：人是铁饭是钢，小伙伴们要积极报饭哦~'
			]
		];
		echo $this->qymessage->sendMessage($msg) ? '1' : '0';
	}

	private function getUserToday($userid) {
		if (empty($userid) || !$this->db->where('userid', $userid)->count_all_results('qy_user')) {
			$this->load->view('dailymeal/error', [
				'msg' => '未能找到您的员工信息，请联系管理员同步员工信息至就餐平台'
			]);
			$this->output->_display();
			exit;
		}
		$res = $this->db->query("select * from bzh_dailymeal_main where time>'" . date('Y-m-d') . "' and userid='$userid' order by Id desc limit 1")->row();
		return $res;
	}

	private function checkTip($add = 0) {
		if (!is_file('note/tipnum.json')) {
			$note = [
				'last' => ''
			];
		} else {
			$note = json_decode(file_get_contents('note/tipnum.json'), true);
		}
		if ($note['last'] != date('ymd')) {
			$note['last'] = date('ymd');
			$note['num'] = 0;
			file_put_contents('note/tipnum.json', json_encode($note));
			return 0;
		} else {
			if ($add && $note['num'] < 3) {
				$note['num']++;
				file_put_contents('note/tipnum.json', json_encode($note));
				return $note['num'] - 1;
			} else {
				return $note['num'];
			}

		}
	}

	public function clearn() {
		setcookie('bzh_userid', 0, time() - 600, $_SERVER['SCRIPT_NAME']);
		redirect('dailymeal/user/index');
	}
}