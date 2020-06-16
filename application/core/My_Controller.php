<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends  CI_Controller {
    function __construct() {
        parent::__construct();
        header("Content-type:text/html;charset=utf-8");
        $this->load->helper(array('url','fun','request','cookie'));
        $this->load->library('session');
    }
}


//企业微信控制器
class Qywx_Controller extends  MY_Controller {
    protected $user_id;
    function __construct() {
        parent::__construct();
        $this->load->library('weixin/qyoauth');
        $this->user_id  = $this->qyoauth->getUserid();
    }
}

class Admin_Controller extends MY_Controller{
    function __construct() {
        parent::__construct();
        $this->load->model('admin/auth_model');
        $this->user = $this->auth_model->checkPower(1);
        $this->load->database();
    }
}
