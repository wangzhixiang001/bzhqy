<?php

class Auth_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('url'); //url函数库文件
	}

	/**
	 * 检测用户权限，用户是否登录
	 * @param int $power 当前控制器的访问权限编号
	 * @return string 若有权访问，返回当前用户id，否则提示权限或跳转登陆
	 */
	public function checkPower($power = -1) {
		$user = $this->session->userdata('userid');
		if (!$user) {
			header("location:" . site_url('newadmin/auth/login'));
			exit();
		}
		global $powerlist;
		$powerlist = explode('*', $this->session->userdata('power'));
		if ($power >= 0 && $powerlist[0] >= 0) {
			if (!in_array($power, $powerlist)) {
				header("location:" . base_url('style/newadmin/error.html'));
				exit();
			}
		}
		return $user;
	}

	public function login($userid, $pasword) {
		$user = $this->db->select('userid,name,power,status,code')->where(array(
			'userid' => $userid,
			'pasword' => $pasword,
		))->get('auth')->row();
	
		if ($user) {
			$this->db->set('last_time', 'now()', false);
			$this->db->where('userid', $userid)->update('auth', array('last_ip' => $this->input->ip_address()));
		}
		return $user;
	}

	public function changepsw($oldpsw, $newpsw) {
		if ($oldpsw == $newpsw) {
			return 1;
		}

		$userid = $this->session->userdata('userid');
		$user = $this->login($userid, $oldpsw);
		if ($user) {
			$data['pasword'] = $newpsw;
			$this->db->where('userid', $userid)->update('auth', $data);
			return 1;
		} else {
			return 0;
		}
	}
}