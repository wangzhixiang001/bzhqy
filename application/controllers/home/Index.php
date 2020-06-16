<?php
require_once(APPPATH.'core/WXBizMsgCrypt.php');
class Index extends CI_Controller {

    public function __construct() {
        //调用父类的构造函数
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->library('weixin/qyoauth');
    }
    public function index(){
		
        if($_POST){
			
			
			//file_put_contents('wxlog.txt','ces');die;
		}else{
			$this->Valid();
		}
		
    } 
	
	public function Valid(){
		 $sVerifyMsgSig = $_REQUEST["msg_signature"];
		 $sVerifyTimeStamp = $_REQUEST["timestamp"];
		 $sVerifyNonce = $_REQUEST["nonce"];
		 $sVerifyEchoStr = $_REQUEST["echostr"];
		 // 需要返回的明文
		 $sEchoStr = "";
		 
		 $req=$this->input->post();
		 file_put_contents('wxlog.txt',json_encode($req),FILE_APPEND);
		 $errCode=$this->CheckSignature($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
		// file_put_contents('wxlog.txt',$errCode,FILE_APPEND);
		// file_put_contents('wxlog.txt',$sEchoStr,FILE_APPEND);
		 if ($errCode){
				echo $sEchoStr;
		 }
	 }
	/*
	------------使用示例一：验证回调URL---------------
	*企业开启回调模式时，企业号会向验证url发送一个get请求 
	假设点击验证时，企业收到类似请求：
	* GET /cgi-bin/wxpush?msg_signature=5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3&timestamp=1409659589&nonce=263014780&echostr=P9nAzCzyDtyTWESHep1vC5X9xho%2FqYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp%2B4RPcs8TgAE7OaBO%2BFZXvnaqQ%3D%3D 
	* HTTP/1.1 Host: qy.weixin.qq.com

	接收到该请求时，企业应
	1.解析出Get请求的参数，包括消息体签名(msg_signature)，时间戳(timestamp)，随机数字串(nonce)以及公众平台推送过来的随机加密字符串(echostr),
	这一步注意作URL解码。
	2.验证消息体签名的正确性 
	3. 解密出echostr原文，将原文当作Get请求的response，返回给公众平台
	第2，3步可以用公众平台提供的库函数VerifyURL来实现。

	*/
	public  function CheckSignature($signature, $timestamp, $nonce,$echostr, &$retEchostr){
		
			// $token = "token"; //配置的token
			// $corpId = "corpId"; //corpid,
			// $encodingAESKey = "encodingAESKey"; //配置的tokenencodingAESKey
			$encodingAesKey = "zuyFDqqsa1LaAJObQsWxssO1emqeDjYIqujGU6dZ9dn";
			$token = "XNlbno9lzX5KuwuwDeRwJ";
			$corpId = "wx2ba3b00d2d6a9e39";
			$wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId); //调用微信提供的函数
			$result = $wxcpt->VerifyURL($signature, $timestamp, $nonce, $echostr, $retEchostr);//调用微信提供的函数
			if ($result != 0)
			{
				return false;
			}
			return true;
			//ret==0表示验证成功，retEchostr参数表示明文，用户需要将retEchostr作为get请求的返回参数，返回给企业号。    
		
	}
	
	
}