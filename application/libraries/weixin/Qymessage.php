<?php
if (!class_exists('Qyaccesstoken')) {
    include dirname(__FILE__) . '/Qyaccesstoken.php';
}
class Qymessage extends Qyaccesstoken{
    
    public function sendMessage($data){
        $url='https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$this->getAccessToken();
        $res=$this->curlPost($url, $this->json_encode_ex($data));
        $result=json_decode($res);
        if($result->errcode==0) return true;
        else return FALSE;
    }
	
	public function json_encode_ex($value)
    {
        if ( version_compare( PHP_VERSION,'5.4.0','<'))
        {
            $str = json_encode($value);
            $str =  preg_replace_callback(
                "#\\\u([0-9a-f]{4})#i",
                function($matchs)
                {
                    return  iconv('UCS-2BE', 'UTF-8',  pack('H4',  $matchs[1]));
                },
                $str
            );
            return  $str;
        }
        else
        {
            return json_encode( $value, JSON_UNESCAPED_UNICODE);
        }
    }
}