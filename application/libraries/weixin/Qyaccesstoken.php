<?php
/**
 * 微信企业号获取AccessToken
 */
class Qyaccesstoken{

    /**
     * 企业号CorpID
     */
	protected $CorpID='wx2ba3b00d2d6a9e39';
	protected $Secret='wptdi4ZR2KMwXxY8_Y2gEPeWovYs2rfr8KRTvexro5XBlz_oNC4NTsPKU-Axtyvm';	//企业号Secret;
	protected $curl_timeout=1000;	//CRUL超时时间 默认30S
	protected $qyname='bzh';       //企业号缩写
	

    /**
     * 获取AccessToken
     * @return string
     */
	public function getAccessToken()
    {
		if(!is_file("tokenfile/$this->qyname/access_token.json")){
			return $this->newtoken();
		}
        $data = json_decode(file_get_contents("tokenfile/$this->qyname/access_token.json"));
        if ($data->expire_time < time()) {
            return $this->newtoken();
        } else {
            return $data->access_token;
        }
    }
	
	private function newtoken(){
		$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$this->CorpID."&corpsecret=".$this->Secret;
		$res1 = $this->curlGet($url);
		$res = json_decode($res1, true);
		if (!empty($res['access_token'])) {
			file_put_contents("tokenfile/$this->qyname/access_token.json",json_encode([
				'expire_time' => time() + $res['expires_in'] - 60,
				'access_token' => $res['access_token']
			]));
			return $res['access_token'];
		}else{
			die('FAIL AccessToken');
		}
	}
    
    
    /**
     * 发送get请求，返回结果
     * @param string $url 请求链接
     * @return mixed
     */
    public function curlGet($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // 运行curl，结果以jason形式返回
        $res1 = curl_exec($ch);
        curl_close($ch);
        return $res1;
    }
    
    public function curlPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 运行curl，结果以jason形式返回
        $res1 = curl_exec($ch);
        curl_close($ch);
        return $res1;
    }
}

