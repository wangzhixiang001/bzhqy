<?php

class Auth extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('admin/auth_model');
	}

	//登陆验证
	public function login() {
		$next = site_url('newadmin/home');
		if ($this->session->userdata('userid')) {
			header("location:" . $next);
			exit();
		}
		$userid = trim($this->input->post('userid'));
		if (!$userid) {
			$this->load->view('newadmin/login');
		} else {
			$psw = trim($this->input->post('password'));
		
			$user = $this->auth_model->login($userid, $psw);
			// var_dump($user);die;
			if ($user) {
				if (!$user->status) {
					$data['bad'] = 0;
					$this->load->view('newadmin/login', $data);
					return;
				}
				$ud = array(
					'userid' => $user->userid,
					'name' => $user->name,
					'power' => $user->power,
					'code' => $user->code,
				);
				
				$res = $this->session->set_userdata($ud);
				header("location:" . $next);
				exit();
			} else {
				$data['bad'] = 1;
				$this->load->view('newadmin/login', $data);
			}
		}
	}

}