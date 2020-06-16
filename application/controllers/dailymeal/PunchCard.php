<?php

use EasyWeChat\Factory;
//http://127.0.0.1/CI302/index.php/dailymeal/user
class PunchCard extends CI_Controller
{
	private $userInfo = null;

	public function __construct()
	{
		//调用父类的构造函数
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->database();
		if (!$this->session->wechat_outh_user_info) {
			$config = [
				'app_id' => 'wx9e212857c64488e3',         // AppID
				'secret' => '747150026c8a84aeef959983db1a583d',    // AppSecret
				'oauth' => [
					'scopes'   => ['snsapi_userinfo'],
					// 'callback' => '/oauth_callback',
				],
			];
			$app = Factory::officialAccount($config);
			$oauth = $app->oauth;
			if ($this->input->get('code')) {
				$user = $oauth->user();
				$exitsUser = $this->db->where('openid', $user['id'])->get('punch_card_users')->row_array();
				if ($exitsUser) { // 更新用户信息
					$this->db->where()->update('punch_card_users', [
						'nickname' => $user['nickname'],
						'headimgurl' => $user['headimgurl'],
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				} else {
					$this->db->insert('punch_card_users', [
						'openid' => $user['id'],
						'nickname' => $user['nickname'],
						'headimgurl' => $user['headimgurl'],
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
				$this->session->set_userdata('wechat_outh_user_info', $user);
			} else {
				$oauth->redirect($this->curPageURL())->send();
			}
		}
		$this->userInfo = $this->session->wechat_outh_user_info;
	}

	//报饭入口
	public function index()
	{
		$exits = $this->db->where('openid', $this->userInfo['id'])->get('punch_card_record')->row_array();
		if (!$exits) {
			$this->db->insert('punch_card_record', [
				'day' => date('Y-m-d'),
				'openid' => $this->userInfo['id'],
				'created_at' => date('Y-m-d H:i:s'),
			]);
		}
		$this->load->view('punchcard/index');
	}

	public function dateList()
	{
		$indexDay = date('Y-m-01');
		$where = $this->getWhere($indexDay);
		$list = $this->db->where($where)->get('punch_card_record')->result_array();
		$this->load->view('punchcard/list', ['indexDay' => $indexDay, 'list' => $list]);
	}

	private function getWhere($indexDay)
	{
		return [
			'created_at <=' => date('Y-m-d H:i:s', strtotime("$indexDay +1 month")),
			'created_at >=' => date('Y-m-d H:i:s', strtotime("$indexDay -1 month")),
			'openid' => $this->userInfo['id']
		];
	}

	public function getData()
	{
		$indexDay = $this->input->get('indexDay');
		$where = $this->getWhere($indexDay);
		$list = $this->db->where($where)->get('punch_card_record')->result_array();
		$this->ajaxResult(['code' => 1, 'list' => $list]);
	}

	/**
	 * 输出json结果
	 * @param int $code 状态码
	 * @param type $data 数据
	 */
	private function ajaxResult($result)
	{
		header("Content-type:application/json");
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	private function curPageURL()
	{
		$pageURL = 'http';

		if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}
