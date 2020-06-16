<?php
if (!class_exists('Qyaccesstoken')) {
	include dirname(__FILE__) . '/Qyaccesstoken.php';
}
class Wxjs extends Qyaccesstoken {

	/**
	 * 获取微信JS所需的参数
	 * @param string $url 使用微信js页面的url（可选参数，默认当前地址）
	 * @return array
	 */
	public function getSignPackage($url = 0) {
		$url = $url ? $url : "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$jsapiTicket = $this->getJsApiTicket();
		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId" => $this->CorpID,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"signature" => $signature,
		);
		return $signPackage;
	}

	private function getJsApiTicket() {
		if(!is_file("tokenfile/$this->qyname/jsapi_ticket.json")){
			$data = [
				'expire_time' => 0
			];
		}else{
			$data = json_decode(file_get_contents("tokenfile/$this->qyname/jsapi_ticket.json"),true);
		}
		
		if ($data['expire_time'] < time()) {
			$accessToken = $this->getAccessToken();
			$url = "http://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->curlGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data['expire_time'] = time() + $res->expires_in - 60;
				$data['jsapi_ticket'] = $ticket;
				file_put_contents("tokenfile/$this->qyname/jsapi_ticket.json", json_encode($data));
			}
		} else {
			$ticket = $data['jsapi_ticket'];
		}
		return $ticket;
	}

	/**
	 * 生成随机字符串
	 * @param int $length 随机字符串长度
	 * @return string
	 */
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
}