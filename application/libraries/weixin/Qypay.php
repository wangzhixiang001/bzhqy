<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qypay {

	protected $appid = '';
	protected $cert_path = '';
	protected $key_path = '';
	protected $mchid = '';
	protected $key = '';
	protected $time_out = 5;
	private $curl;

	public function __construct($config = []) {
		foreach ($config as $key => $val) {
			$this->$key = $val;
		}
		$this->init();
	}

	/**
	 * 企业付款
	 * @param array $orderinfo
	 * @return array
	 */
	public function sendQyPay($orderinfo) {
//		return [true,123,456];
		$orderinfo['mch_appid'] = $this->appid;
		if (empty($orderinfo['check_name'])) {
			$orderinfo['check_name'] = 'NO_CHECK';
		}
		$orderinfo['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];
		$orderinfo['mchid'] = $this->mchid; //商户号
		$xml = $this->createSign($orderinfo);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$response = $this->curPostXmlSSL($url, $xml);
		if (!isset($response['return_code'])) {
			$result = [FALSE, 'CURL_ERROR', '未收到正确响应'];
		} else if ($response['return_code'] == 'FAIL') {
			$result = [FALSE, 'return_error', $response['return_msg']];
		} else if ($response['result_code'] == 'FAIL') {
			$result = [FALSE, $response['err_code'], $response['err_code_des']];
		} else {
			$result = [TRUE, $response['payment_no'], $response['payment_time']];
		}
		return $result;
	}

	/**
	 * 发微信红包
	 * @param array $orderinfo mch_billno商户订单号,send_name商户名称,re_openid用户openid,total_amount付款金额,wishing红包祝福语,remark备注,act_name活动名称,scene_id场景id
	 * @return array
	 */
	public function sendRedpacket($orderinfo) {
		$orderinfo['wxappid'] = $this->appid;
//		$orderinfo['total_num'] = 1;
		//		$orderinfo['client_ip'] = $_SERVER['SERVER_ADDR'];
		$orderinfo['mch_id'] = $this->mchid; //商户号
		$xml = $this->createSign($orderinfo, true);
//        echo json_encode($xml);die;
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendworkwxredpack';
		$response = $this->curPostXmlSSL($url, $xml);
		if (!isset($response['return_code'])) {
			$result = [false, 'CURL_ERROR', '未收到正确响应'];
		} elseif ($response['return_code'] == 'FAIL') {
			$result = [false, $response['return_code'], $response['return_msg']];
		} elseif ($response['result_code'] == 'FAIL') {
			$result = [false, $response['err_code'], $response['err_code_des']];
		} else {
			$result = [true, $response['send_listid'], date('Y-m-d H:i:s')];
		}
		return $result;
	}

	/**
	 * 作用：查询红包
	 * mch_billno商户订单号
	 */
	public function queryRedpacket($mch_billno) {
		$orderinfo['mch_billno'] = $mch_billno;
		$orderinfo['appid'] = $this->appid;
		$orderinfo['bill_type'] = 'MCHT';
		$orderinfo['mch_id'] = $this->mchid; //商户号
		$xml = $this->createSign($orderinfo);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
		$response = $this->curPostXmlSSL($url, $xml);
		return $response;
	}

	/**
	 * 作用：查询企业支付
	 * partner_trade_no商户订单号
	 */
	public function queryQyPay($partner_trade_no) {
		$orderinfo['partner_trade_no'] = $partner_trade_no;
		$orderinfo['appid'] = $this->appid;
		$orderinfo['mchid'] = $this->mchid; //商户号
		$xml = $this->createSign($orderinfo);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';
		$response = $this->curPostXmlSSL($url, $xml);
		return $response;
	}

	/**
	 *    作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr($length = 32) {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 *    作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = '';
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}

			$buff .= $k . '=' . $v . '&';
		}
		return substr($buff, 0, strlen($buff) - 1);
	}

	/**
	 *    作用：生成签名
	 */
	public function getSign($Parameters) {
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$String = $String . '&key=' . $this->key;
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($String);
		return $result;
	}

	/**
	 *    作用：生成签名
	 */
	public function createSign($parameters) {
		$parameters = array_filter($parameters);
		$parameters['nonce_str'] = $this->createNoncestr(); //随机字符串
		$parameters['sign'] = $this->getSign($parameters); //微信支付签名
		return $parameters;
	}

	/**
	 *    作用：将xml转为array
	 */
	public function xmlToArray($xml) {
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	/**
	 *    作用：array转xml
	 */
	public function arrayToXml($arr) {
		$xml = '<xml>';
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
			} else {
				$xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
			}

		}
		$xml .= '</xml>';
		return $xml;
	}

	/**
	 *    作用：使用证书，以post方式提交xml到对应的接口url
	 */
	public function curPostXmlSSL($url, $xml_array, $second = 5) {
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->arrayToXml($xml_array));
		//运行curl
		$data = curl_exec($this->curl);
		//返回结果
		if ($data !== FALSE) {
			return $this->xmlToArray($data);
		} else {
			$curl_error_code = curl_errno($this->curl);
			return [
				'return_code' => 'FAIL',
				'return_error' => 'CURL_ERROR',
				'return_msg' => $curl_error_code,
			];
		}
	}

	/**
	 * 关闭curl
	 */
	public function close() {
		curl_close($this->curl);
	}

	/**
	 * 初始化curl
	 */
	public function init() {
		$this->curl = curl_init();
		//设置超时
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->time_out);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false); //严格校验
		//设置header
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		//要求结果为字符串且输出到屏幕上
		if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
			curl_setopt($this->curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($this->curl, CURLOPT_SSLCERT, $this->cert_path);
		curl_setopt($this->curl, CURLOPT_SSLKEYTYPE, 'PEM');
		curl_setopt($this->curl, CURLOPT_SSLKEY, $this->key_path);
		//post提交方式
		curl_setopt($this->curl, CURLOPT_POST, true);
	}
}
