<?php

defined('APPPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public $time = 1;

	public function __construct() {
		$this->oauth_type = 'snsapi_userinfo';
		parent::__construct();
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('weixin/jssdk');
		$this->load->library('weixin/qyoauth');

	}

	public function index() {
		// $this->load->library('session');//加载session类
		// $help_openid = $this->session->userdata('help_openid'); //读取session
		// // $help_openid = $this->session->unset_tempdata('help_openid');

		// if(empty($help_openid)){

		$this->load->view('test/index', ['num' => ' $num', 'user_info' => ' $user_info',
			'jssdk' => $this->jssdk->getSignPackage()]);
		// }else{
		// 	$user_info=$this->start();
		// 		$this->load->view('act/nec/help',['type'=>0,'user_info'=>$user_info,'openid'=>$help_openid,'jssdk'=>$this->wxsdk()->js->config(['hideMenuItems', 'onMenuShareTimeline', 'onMenuShareAppMessage'])]);

		// }

	}

	public function f5() {
		if ($this->input->is_ajax_request()) {
			$collect_info = $this->db->select('id,h_num')->where('openid', $this->userinfo['openid'])->where('status', 0)->get('nec_h_users')->result_array();
			if (empty($collect_info)) {
				return $this->ajaxResult(0);
			} else {
				return $this->ajaxResult(1, $collect_info);
			}
		}
	}

	public function f52() {
		if ($this->input->is_ajax_request()) {
			$openid = $this->input->get('openid');
			$collect_info = $this->db->select('id,h_num')->where('openid', $openid)->where('status', 0)->get('nec_h_users')->result_array();
			if (empty($collect_info)) {
				return $this->ajaxResult(0);
			} else {
				return $this->ajaxResult(1, $collect_info);
			}
		}
	}

	public function sj_get() {
		if ($this->time) {
			return $this->ajaxResult(0, '活动已经结束');
		}

		if ($this->input->is_ajax_request()) {
			$this->start();
			$sj = $this->db->where(['openid' => $this->userinfo['openid'], 'h_openid' => $this->userinfo['openid']])->count_all_results('nec_h_users');

			if (!empty($sj)) {
				return $this->ajaxResult(0, '您已经收集过了，赶快去邀请好友帮忙吧');
			}

			$collect_info = $this->db->select('id,h_num')->where('openid', $this->userinfo['openid'])->where('status', 0)->get('nec_h_users')->result_array();

			if (empty($collect_info)) {
				$num = rand(0, 4);
				$this->db->insert('nec_h_users', ['openid' => $this->userinfo['openid'], 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
				return $this->ajaxResult(1, $num);
			} else {

				if (count($collect_info) == 5) {
					$this->db->where(['openid' => $this->userinfo['openid'], 'status' => 0])->update('nec_h_users', ['status' => 1]);
					if ($this->db->affected_rows()) {
						$num = rand(0, 4);
						$this->db->insert('nec_h_users', ['openid' => $this->userinfo['openid'], 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
						return $this->ajaxResult(1, $num);
					}
					return $this->ajaxResult(0, '网络错误');
				} else if (count($collect_info) == 4) {
					$gift = [0, 1, 2, 3, 4];
					foreach ($collect_info as $k => $v) {
						unset($gift[$v['h_num']]);
					}
					$num = array_rand($gift);
					$this->db->insert('nec_h_users', ['openid' => $this->userinfo['openid'], 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
					$this->db->where('openid', $this->userinfo['openid'])->set('choos_num', 'choos_num+1', false)->update('nec_users');
					return $this->ajaxResult(1, $num);
				} else {
					$gift = [0, 1, 2, 3, 4];
					foreach ($collect_info as $k => $v) {
						unset($gift[$v['h_num']]);
					}
					$num = array_rand($gift);
					$this->db->insert('nec_h_users', ['openid' => $this->userinfo['openid'], 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
					return $this->ajaxResult(1, $num);
				}
			}

		}
	}

	public function choos_num() {
		if ($this->time) {
			return $this->ajaxResult(0, '活动已经结束');
		}

		if ($this->input->is_ajax_request()) {
			$user_info = $this->start();

			if ($user_info['choos_num'] == 0) {
				return $this->ajaxResult(0, '您没有抽奖机会了');
			}

			$num = $this->db->where('openid', $this->userinfo['openid'])->count_all_results('nec_records');

			if ($num >= 1) {
				$this->db->where('openid', $this->userinfo['openid'])->set('choos_num', 'choos_num-1', false)->update('nec_users');
				return $this->ajaxResult(0, '很遗憾，您未中奖<br/>再来一次吧！');
			}

			$this->load->model('Gift');
			$gift = $this->Gift->chooseGift(20180131);

			$this->db->where('openid', $this->userinfo['openid'])->set('choos_num', 'choos_num-1', false)->update('nec_users');

			if (empty($gift)) {
				return $this->ajaxResult(0, '很遗憾，您未中奖<br/>再来一次吧！');
			}

			$this->db->insert('nec_records', ['openid' => $this->userinfo['openid'], 'c_time' => time(), 'pid' => $gift->id, 'type' => 0]);
			$data['rid'] = $this->db->insert_id();

			$data['id'] = $gift->id;
			$data['name'] = $gift->giftname;

			return $this->ajaxResult(1, $data);
		}

	}

	public function sub_data() {
		if ($this->time) {
			return $this->ajaxResult(0, '活动已经结束');
		}
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			$info = $this->db->where(['openid' => $this->userinfo['openid'], 'id' => $data['id']])->get('nec_records')->row_array();
			if (empty($info)) {
				return $this->ajaxResult(0, '参数错误');
			}

			if (!empty($info['l_time'])) {
				return $this->ajaxResult(0, '请勿重复提交信息');
			}

			if (!preg_match("/^1[3|5|7|8|6|9][0-9]\d{8}$/", $data['tel'])) {
				return $this->ajaxResult(0, ['msg' => '手机号错误']);
			}

			$this->db->where(['openid' => $this->userinfo['openid'], 'id' => $data['id']])->update('nec_records', ['tel' => $data['tel'], 'address' => $data['address'], 'name' => $data['name'], 'l_time' => time()]);

			return $this->ajaxResult(1);

		}
	}

	public function get_list() {

		$data_info = $this->db->select('nec_records.id,pid,giftname,l_time')->where('openid', $this->userinfo['openid'])->join('act_gift_library', 'act_gift_library.id=nec_records.pid', 'left')->order_by('l_time', 'asc')->get('nec_records')->result_array();

		$html = '';

		if (empty($data_info)) {
			$html = '<h2 style="padding-top: 42px;text-align:  center;">暂无奖品</h2>';
		} else {
			foreach ($data_info as $v) {
				$html .= '<li data-id="' . $v['id'] . '" data-pid="' . $v['pid'] . '" data-l_time="' . ($v['l_time'] ? $v['l_time'] : '0') . '" ><span>' . $v['giftname'] . '<em>' . ($v['l_time'] ? '已领取' : '待领取') . '</em></span></li>';
			}
		}

		return $this->ajaxResult(0, $html);

	}

	public function help($openid) {

		if (empty($openid)) {
			header('Location: http://women.bzh001.com/index.php/act/Nec/index');
		}
		// 	$this->load->library('session');//加载session类
		// 	$array['help_openid']=$openid;
		// $this->session->set_userdata($array);//保存session
		// $name = $this->session->userdata('help_openid'); //读取session

		$user_info = $this->start();
		$this->load->view('act/nec/help', ['user_info' => $user_info, 'openid' => $openid, 'jssdk' => $this->wxsdk()->js->config(['hideMenuItems', 'onMenuShareTimeline', 'onMenuShareAppMessage'])]);
	}

	// public function del(){
	// 	$this->load->library('session');//加载session类
	// 	// $this->session->sess_destroy();

	// 	unset($_SESSION['help_openid']);
	// }

	public function help_xxx() {
		if ($this->time) {
			return $this->ajaxResult(0, '活动已经结束');
		}
		if ($this->input->is_ajax_request()) {
			$this->start();
			$openid = $this->input->get('help_openid');

			if ($openid == $this->userinfo['openid']) {
				return $this->ajaxResult(0, '自己不能给自己助力哦');
			}

			if (empty($openid)) {
				return $this->ajaxResult(0, '参数错误');
			}

			$sj = $this->db->where(['openid' => $openid, 'h_openid' => $this->userinfo['openid']])->count_all_results('nec_h_users');

			if (!empty($sj)) {
				return $this->ajaxResult(0, '您已经帮助过好友了');
			}

			$collect_info = $this->db->select('id,h_num')->where('openid', $openid)->where('status', 0)->get('nec_h_users')->result_array();

			if (empty($collect_info)) {
				$num = rand(0, 4);
				$this->db->insert('nec_h_users', ['openid' => $openid, 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
				return $this->ajaxResult(1, $num);
			} else {

				if (count($collect_info) == 5) {
					$this->db->where(['openid' => $openid, 'status' => 0])->update('nec_h_users', ['status' => 1]);
					if ($this->db->affected_rows()) {
						$num = rand(0, 4);
						$this->db->insert('nec_h_users', ['openid' => $openid, 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
						return $this->ajaxResult(1, $num);
					}
					return $this->ajaxResult(0, '网络错误');
				} else if (count($collect_info) == 4) {
					$gift = [0, 1, 2, 3, 4];
					foreach ($collect_info as $k => $v) {
						unset($gift[$v['h_num']]);
					}
					$num = array_rand($gift);
					$this->db->insert('nec_h_users', ['openid' => $openid, 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
					$this->db->where('openid', $openid)->set('choos_num', 'choos_num+1', false)->update('nec_users');
					return $this->ajaxResult(1, $num);
				} else {
					$gift = [0, 1, 2, 3, 4];
					foreach ($collect_info as $k => $v) {
						unset($gift[$v['h_num']]);
					}
					$num = array_rand($gift);
					$this->db->insert('nec_h_users', ['openid' => $openid, 'h_openid' => $this->userinfo['openid'], 'c_time' => time(), 'h_num' => $num]);
					return $this->ajaxResult(1, $num);
				}
			}

		}
	}

	/**
	 * 输出json结果
	 * @param int $code 状态码
	 * @param type $data 数据
	 */
	private function ajaxResult($code = 0, $data = NULL) {
		$result['code'] = $code;
		if ($data !== NULL) {
			$result['data'] = $data;
		}

		header("Content-type:application/json");
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}

}
