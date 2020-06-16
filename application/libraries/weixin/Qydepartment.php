<?php
if (!class_exists('Qyaccesstoken')) {
    include dirname(__FILE__) . '/Qyaccesstoken.php';
}

class Qydepartment extends Qyaccesstoken{
    /**
     * 获取部门列表
     * @param int $department_id 部门id(可选，默认获取所有部门)
     * @return array|boolean
     */
    public function getDepartmentList($department_id=''){
        $url='https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$this->getAccessToken();
        if(!empty($department_id))$url.='&id='.$department_id;
        $res = $this->curlGet($url);
        $data = json_decode($res, true);
        if ($data['errcode']==0) {
            return $data['department'];
        } else{
            return false;
        }
    }
}