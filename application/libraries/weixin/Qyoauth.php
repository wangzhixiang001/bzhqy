<?php
if (!class_exists('Qyaccesstoken')) {
    include dirname(__FILE__) . '/Qyaccesstoken.php';
}

class Qyoauth extends Qyaccesstoken{
    
    
    /**
     * OAuth2.0获取userid
     * @param String $backUrl 回调链接
     * @return String|boolean userid|获取失败或不在企业号中
     */
    public function getUserid($backUrl=false) {
        return '6666231';
        if(isset($_COOKIE[$this->qyname."_userid"]))
		{
			return $_COOKIE[$this->qyname.'_userid'];
		}
		else if(isset($_GET["code"])){
		    $url='https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$this->getAccessToken().'&code='.$_GET["code"];
		    $res=$this->curlGet($url);
		    $data = json_decode($res,true);
		    if(isset($data['UserId'])){
		        setcookie($this->qyname."_userid",$data['UserId'],time()+432000,$_SERVER['SCRIPT_NAME']);
		        return $data['UserId'];
		    }
		    else return false;
		}
		else
		{
	        if(!$backUrl) $backUrl=$this->curPageURL();
            $backUrl = urlencode($backUrl);
	        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->CorpID.'&redirect_uri='.$backUrl.'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
	        header("Location: ".$url);
			exit();
		}
    }
    
    /**
     * 获取成员的详细资料
     * @param string $userid
     * @return array|boolean
     */
    public function getUserInfo($userid)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=' . $this->getAccessToken() . '&userid=' . $userid;
        $res = $this->curlGet($url);
        $data = json_decode($res, true);
        if ($data['errcode']==0) {
            return $data;
        } else{
            return false;
        }
    }
    
    /**
     * 获取部门成员列表
     * @param int $department_id 获取的部门id
     * @param int $status 0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。未填写则默认为4
     * @param int $fetch_child 1/0：是否递归获取子部门下面的成员,默认0
     * @return array|boolean
     */
    public function getDepartmentUser($department_id,$status=4,$fetch_child=0){
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token='.$this->getAccessToken().'&department_id='.$department_id.'&fetch_child='.$fetch_child.'&status='.$status;
        $res = $this->curlGet($url);
        $data = json_decode($res, true);
        if ($data['errcode']==0) {
            return $data['userlist'];
        } else{
            return false;
        }
    }
    
    /**
     * 获取部门成员包含详细信息的列表
     * @param int $department_id 获取的部门id
     * @param int $status 0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。未填写则默认为4
     * @param int $fetch_child 1/0：是否递归获取子部门下面的成员,默认0
     * @return array|boolean
     */
    public function getDepartmentUserInfo($department_id,$status=4,$fetch_child=0){
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$this->getAccessToken().'&department_id='.$department_id.'&fetch_child='.$fetch_child.'&status='.$status;
        $res = $this->curlGet($url);
        $data = json_decode($res, true);
        if ($data['errcode']==0) {
            return $data['userlist'];
        } else{
            return false;
        }
    }
    
    /**
     * 获取部门列表
     * @param number $id 部门id（可选，不填默认获取所有部门）。获取指定部门及其下的子部门
     * @return array|boolean
     */
    public function getDepartment($id=0){
        $url='https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$this->getAccessToken();
        if($id) $url=$url.'&id='.$id;
        $res = $this->curlGet($url);
        $data = json_decode($res, true);
        if ($data['errcode']==0) {
            return $data['department'];
        } else{
            return false;
        }
    }
    
    /**
     * userid转换成openid
     * @param string $userid 用户的userid
     * @param number $agentid 需要发送红包的应用ID(可选参数，若只是使用微信支付和企业转账，则无需该参数)
     * @return string|array|boolean 无应用ID时返回openid，有应用ID时返回包含agentid、openid的数组，获取失败返回false
     */
    public function convertToOpenid($userid,$agentid=0){
        $data='{"userid": "'.$userid;
        if($agentid) $data=$data.'","agentid": "'.$agentid;
        $data=$data.'"}';
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token='.$this->getAccessToken();
        $res=$this->curlPost($url, $data);
        $result=json_decode($res, true);
        if(isset($result["openid"])){
            if($agentid) return $result;
            else return $result["openid"];
        }else return false;
    }
    
    /**
     * openid转换成userid
     * @param string $openid 用户的openid
     * @return string|boolean 返回userid，获取失败返回false
     */
    public function convertToUserid($openid){
        $data='{"openid": "'.$openid.'"}';
        $url='https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token='.$this->getAccessToken();
        $res=$this->curlPost($url, $data);
        $result=json_decode($res, true);
        if(isset($result["userid"])){
            return $result["userid"];
        }else return false;
    }
    /**
     * 获取当前的完整URL（包含参数）
     * @return string
     */
    function curPageURL()
    {
        $pageURL = 'http';
    
        if (isset($_SERVER['HTTPS'])&&$_SERVER["HTTPS"] == "on")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";
    
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function getUseridAjax() {
        if(isset($_COOKIE[$this->qyname."_userid"]))
        {
            return array('userid'=> $_COOKIE[$this->qyname.'_userid']);
        }
        else if(isset($_POST["code"])){
            $url='https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$this->getAccessToken().'&code='.$_POST["code"];
            $res=$this->curlGet($url);
            $data = json_decode($res,true);
            if(isset($data['UserId'])){
                setcookie($this->qyname."_userid",$data['UserId'],time()+432000,$_SERVER['SCRIPT_NAME']);
                return array('userid'=>$data['UserId']);
            }
            else return false;
        }
        else
        {
            $backUrl = empty($_POST['path'])?'':$_POST['path'];
            if(!$backUrl) $backUrl=$this->curPageURL();
            $backUrl = urlencode($backUrl);
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->CorpID.'&redirect_uri='.$backUrl.'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
            return array('code'=>0,'url'=>$url);
        }
    }
}