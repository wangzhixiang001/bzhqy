<?php
if (!class_exists('Qyaccesstoken')) {
	include dirname(__FILE__) . '/Qyaccesstoken.php';
}
class Jssdk extends Qyaccesstoken {

	public function echoJsConfig($use, $url = 0) {
		$signPackage = $this->getSignPackage($url);
		$signPackage['debug'] = false;
		$signPackage['jsApiList'] = $use;
		$cfg = json_encode($signPackage);
		$js = '<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script><script type="text/javascript">wx.config(' . $cfg . ');</script>';
		return $js;
	}

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
		$string = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string);

		$signPackage = array(
			"appId" => $this->CorpID,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"signature" => $signature,
		);
		return $signPackage;
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

	/**
	 * 获取JSApiToken
	 * @return string
	 */
	public function getJsApiTicket() {

		$data = $data = json_decode(file_get_contents("tokenfile/$this->qyname/_access_token.json"));
		if ($data->expire_time < time()) {
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=' . $this->getAccessToken();
			$res = json_decode($this->curlGet($url));
			if (!isset($res->ticket)) {
				throw new Exception($res->errmsg, $res->errcode);
			}
			$data->expire_time = time() + $res->expires_in;
			$data->jsapi_ticket = $res->ticket;
			file_put_contents("tokenfile/$this->qyname/_jsapi_token.json", json_encode($data));
		}
		return $data->jsapi_ticket;
	}
}