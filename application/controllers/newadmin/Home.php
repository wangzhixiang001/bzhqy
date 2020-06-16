<?php
class Home extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('admin/auth_model');
        $this->auth_model->checkPower();
    }

    public function index(){
        $this->load->view('newadmin/index');
    }
    
    public function loginOut(){
        $this->session->sess_destroy();
        header('location:'.site_url('newadmin/auth/login'));
    }

    public function changePsw(){
        $oldpsw=trim($this->input->post('oldpsw'));
        if($oldpsw){
            $newpsw=trim($this->input->post('newpsw'));
            $newpsw2=trim($this->input->post('newpsw2'));
            if($newpsw!=$newpsw2) $data['tip2']=1;
            else {
                $this->load->model('admin/auth_model');
                $result=$this->auth_model->changepsw($oldpsw,$newpsw);
                if($result){
                    $this->session->sess_destroy();
                    die("<script language='javascript' type='text/javascript'>parent.location.reload();</script>");
                    $this->loginOut();
                    return;
                }else $data['tip1']=1;
            }
            $this->load->view('newadmin/changepsw',$data);
        }else
            $this->load->view('newadmin/changepsw');
    }
}