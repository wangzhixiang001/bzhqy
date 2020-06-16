<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
function is_exist($keyWord, $stack) {
    foreach ($stack as $key => $val) {
        if ($keyWord == $val) {
            return "1";
        }
    }
    return "0";
}

//二维数组
function search($keyWord, $stack) {
    foreach ($stack as $key => $val) {
        if (in_array($keyWord, $val)) {
            return "1";
        }
    }
    return "0";
}

function getsre($str, $len) {
    $strlen = strlen($str);
    if ($strlen < $len) {
        return $str;
    } else {
        return mb_substr($str, 0, $len) . "...";
    }
}

function isToday($publishDate) {
    if (empty($publishDate)) {
        return false;
    }
    $curDate = date("Y-m-d");
    $publishDate = substr($publishDate, 0, 10);
    if ($curDate === $publishDate) {
        return true;
    }
    return false;
}

function GetIP() {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = "IP无法获取";
    }
    return $cip;
}

function order_source() {
    $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    // iphone
    $is_iphone = strripos($useragent, 'iphone');
    if ($is_iphone) {
        return 'iphone';
    }
    // android
    $is_android = strripos($useragent, 'android');
    if ($is_android) {
        return 'android';
    }
    // 微信
    $is_weixin = strripos($useragent, 'micromessenger');
    if ($is_weixin) {
        return 'weixin';
    }
    // ipad
    $is_ipad = strripos($useragent, 'ipad');
    if ($is_ipad) {
        return 'ipad';
    }
    // ipod
    $is_ipod = strripos($useragent, 'ipod');
    if ($is_ipod) {
        return 'ipod';
    }
    // pc电脑
    $is_pc = strripos($useragent, 'windows nt');
    if ($is_pc) {
        return 'pc';
    }
    return 'other';
}

function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return 'true';
    }
    return 'false';
}

function is_weixin_versions() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false) {
        echo "非微信浏览器禁止浏览";
    } else {
        echo "微信浏览器，允许访问";
        preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        if (intval($matches[2]) >= 5.2) {
            echo '<br>你的微信版本号为:' . $matches[2];
        } else {
            echo '你的微信版本太低，自带浏览器暂不支持上传功能，请升级版本或者点击右上角功能选择使用其他浏览器进行上传，谢谢！';
        }

    }
}

function is_weixin_upload() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false) {
        return 'false';
    } else {
        preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
        if (strstr($matches[2], '5.1') == true) {
            return 'false';
        } else {
            return 'true';
        }

    }
}

function is_mobile($mobile) {
    if (!is_numeric($mobile)) {
         return false;
     }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 是否在地址里
 */
function in_url($page) {
    $url = $_SERVER['PHP_SELF'];
    if (preg_match(sprintf("/%s/i", str_replace("/", "\/", $page)), $url)) {
        return TRUE;
    }
    return FALSE;
}

/**
 * 图片地址
 */
function img_url($url) {
    $ci = &get_instance();
    if ($ci->config->item('images_url')) {
        $url = $ci->config->item('images_url') . $url;
    } else {
        $url = $ci->config->item('base_url') . $url;
    }
    return $url;
}

function startsWith($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) == $needle;
}

//var_dump(startsWith("hello world", "hello")); // true
//var_dump(endsWith("hello world", "world"));   // true

/**
 * 上传文件路径
 */
function uploads_url($url) {
    $ci = &get_instance();
    if ($ci->config->item('uploads_url')) {
        $url = $ci->config->item('uploads_url') . $url;
    } else {
        $url = $ci->config->item('base_url') . $url;
    }
    return $url;
}

/**
 * 生成uuid
 */
function uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * http://stackoverflow.com/questions/1201798/use-php-to-convert-png-to-jpg-with-compression
 */
function png2jpg($originalFile, $outputFile, $quality) {
    $image = imagecreatefrompng($originalFile);
    imagejpeg($image, $outputFile, $quality);
    imagedestroy($image);
}

function cut_str($sourcestr, $cutlength) {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr);//字符串的字节数
    while (($n < $cutlength) and ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str);//得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224)    //如果ASCII位高与224，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3;            //实际Byte计为3
            $n++;            //字串长度计1
        } elseif ($ascnum >= 192) //如果ASCII位高与192，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2;            //实际Byte计为2
            $n++;            //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1;            //实际的Byte数仍计1个
            $n++;            //但考虑整体美观，大写字母计成一个高位字符
        } else                //其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1;            //实际的Byte数计1个
            $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > $cutlength) {
        $returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
    }
    return $returnstr;

}


/**
 * 保存上传文件
 * @param $file string 文件
 * @param $save_path string 文件路径
 * @param $save_name string 文件名称，包括扩展名
 * @return 返回文件路径名
 */
function save_upload_file($file, $save_path, $save_name = NULL) {
    $file_name = '';
    if ($file["error"] == 0) {
        if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $save_path)) {
            mkdir($_SERVER["DOCUMENT_ROOT"] . $save_path, 0777, TRUE);
        }
        if (empty($save_name)) {
            $file_ext = pathinfo($file["tmp_name"], PATHINFO_EXTENSION);
            $save_name = time() . rand(10000, 99999) . $file_ext;
        }
        $file_name = $save_path . "/" . $save_name;
        move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $file_name);
    }
    return $file_name;
}

/**
 * 保存上传图片
 * @param $file string 文件
 * @param $save_path string 文件路径
 * @param $save_name string 文件名称，包括扩展名
 * @return array 返回数组
 */
function save_upload_image($file, $save_path, $save_name = NULL) {
    //图片类型
    $allow_type = explode(',', 'image/png,image/jpeg,image/gif,application/octet-stream');
    //图片大小 5M
    $allow_size = 5 * 1024 * 1024 * 1024;
    $file_name = '';
    $data = array("name" => "", "error" => 0);
    if ($file["error"] == 0) {
        if (!in_array($file["type"], $allow_type)) {
            $data["error"] = 1;
        } else if ($file['size'] > $allow_size) {
            $data["error"] = 2;
        } else {
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $save_path)) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . $save_path, 0777, TRUE);
            }
            if (empty($save_name)) {
                $file_ext = ".jpg";
                $save_name = time() . rand(10000, 99999) . $file_ext;
            }
            $file_name = $save_path . "/" . $save_name;
            move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $file_name);
            $data["name"] = $file_name;
        }
    } else {
        $data["error"] = $file["error"];
    }
    return $data;
}

/**
 * 保存上传的录音
 * @param $file string 文件
 * @param $save_path string 文件路径
 * @param $save_name string 文件名称，包括扩展名
 * @return array 返回数组
 */
function save_upload_sound($file, $save_path, $save_name = NULL) {
    //图片类型
    $allow_type = explode(',', 'video/3gpp,application/octet-stream');
    //图片大小 5M
    $allow_size = 5 * 1024 * 1024 * 1024;
    $file_name = '';
    $data = array("name" => "", "error" => 0);
    if ($file["error"] == 0) {
        if (!in_array($file["type"], $allow_type)) {
            $data["error"] = 1;
        } else if ($file['size'] > $allow_size) {
            $data["error"] = 2;
        } else {
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $save_path)) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . $save_path, 0777, TRUE);
            }
            if (empty($save_name)) {
                $file_ext = ".3gp";
                $save_name = time() . rand(10000, 99999) . $file_ext;
            }
            $file_name = $save_path . "/" . $save_name;
            move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $file_name);
            $data["name"] = $file_name;
        }
    } else {
        $data["error"] = $file["error"];
    }
    return $data;
}

/**
 * 获取图片新尺寸
 * @param $image_url 图片地址
 * @param $new_width 新宽度
 */
function get_image_size($image_url, $new_width) {
    $img_w = $new_width;
    $img_h = 0;
    $scale = 0;
    if (is_file($image_url)) {
        $image = getimagesize($image_url);
        $ori_w = $image[0];
        $ori_h = $image[1];
        $scale = $new_width / $ori_w;
        $img_h = intval($ori_h * $scale);
    }
    $data = array();
    $data['width'] = $img_w;
    $data['height'] = $img_h;
    $data['scale'] = $scale;
    $data[0] = $img_w;
    $data[1] = $img_h;
    $data[2] = $scale;
    return $data;

}

/**
 * 获取数据列表
 * @param array $arr_data_list 数组列表
 * @param array 返回列表和当前列表最大id
 */
function get_data_list($list) {
    //获取since_id
    if (is_array($list) && count($list) > 0) {
        $last = end($list);
        $since_id = $last['id'];
    } else {
        $since_id = 0;
    }

    return array("list" => $list, "since_id" => $since_id);
}

/**
 * 获取数据列表
 * @param array $arr_data_list 数组列表
 * @param array 返回列表和当前列表最大id
 */
function get_data_list_modified_on($list) {
    //获取since_id
    if (is_array($list) && count($list) > 0) {
        $last = end($list);
        $since_id = $last['modified_on'];
    } else {
        $since_id = 0;
    }

    return array("list" => $list, "since_id" => $since_id);
}

/**
 * 检测参数签名
 * @return 签名正确返回true，否则返回false
 */
function check_params_sig() {
    //私密令牌
    $secret = '3886818e022a2f8c4251caa85b3f51bc';
    //过期时间10分钟
    $time_expired = 600;
    $params = $_REQUEST;
    if (count($params) > 0) {
        if (isset($params['ts']) && isset($params['sig'])) {
            natsort($params);
            $keys = array_keys($params);
            $temp = array();
            $ts = 0;
            $sig = '';
            for ($i = 0; $i < count($keys); $i++) {
                $key = strtolower($keys[$i]);
                if ($key == 'sig') {
                    $sig = $params['sig'];
                } else if ($key == 'ts') {
                    $ts = intval($params['ts']);
                } else {
                    $value = $params[$key];
                    array_push($temp, $key . "=" . $value);
                }
            }

            //验证过期日间
            $now = time();
            if ($timestamp > 0 && $now - $ts <= $time_expired) {
                //验证参数签名
                $new_sig = md5(implode($temp, '&') . $ts . $secret);
                return (!empty($sig) && $new_sig == $sig);
            }
        }
    }
    return FALSE;
}

function writeLog($msg, $filename = '') {
    $logFile = $filename . "/" . date('Y-m-d') . '.txt';
    $msg = date('Y-m-d H:i:s') . ' >>> ' . $msg . "\r\n";
    if (!file_exists($filename)) {
        mkdir($filename, 0777, true);
    }
    file_put_contents($logFile, $msg, FILE_APPEND);
}

function json_result($arr_data) {
    if (is_array($arr_data)) {
        echo json_encode($arr_data);
    }
    return TRUE;
}


function randStr($len = 6, $format = 'ALL') {
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
    }
    mt_srand((double)microtime() * 1000000 * getmypid());
    $str = "";
    while (strlen($str) < $len)
        $str .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $str;
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str 要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',') {
    if (empty($str)) {
        return array();
    } else {
        return explode($glue, $str);
    }
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array $arr 要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',') {
    if (empty($arr)) {
        return "";
    } else {
        return implode($glue, $arr);
    }
}


if (!function_exists('password_hash')) {
    /**
     * password_hash()
     *
     * @link    http://php.net/password_hash
     * @param    string $password
     * @param    int $algo
     * @param    array $options
     * @return    mixed
     */
    function password_hash($password, $algo, array $options = array()) {
        static $func_override;
        isset($func_override) OR $func_override = (extension_loaded('mbstring') && ini_get('mbstring.func_override'));

        if ($algo !== 1) {
            trigger_error('password_hash(): Unknown hashing algorithm: ' . (int)$algo, E_USER_WARNING);
            return NULL;
        }

        if (isset($options['cost']) && ($options['cost'] < 4 OR $options['cost'] > 31)) {
            trigger_error('password_hash(): Invalid bcrypt cost parameter specified: ' . (int)$options['cost'], E_USER_WARNING);
            return NULL;
        }

        if (isset($options['salt']) && ($saltlen = ($func_override ? mb_strlen($options['salt'], '8bit') : strlen($options['salt']))) < 22) {
            trigger_error('password_hash(): Provided salt is too short: ' . $saltlen . ' expecting 22', E_USER_WARNING);
            return NULL;
        } elseif (!isset($options['salt'])) {
            if (defined('MCRYPT_DEV_URANDOM')) {
                $options['salt'] = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $options['salt'] = openssl_random_pseudo_bytes(16);
            } elseif (DIRECTORY_SEPARATOR === '/' && (is_readable($dev = '/dev/arandom') OR is_readable($dev = '/dev/urandom'))) {
                if (($fp = fopen($dev, 'rb')) === FALSE) {
                    log_message('error', 'compat/password: Unable to open ' . $dev . ' for reading.');
                    return FALSE;
                }

                // Try not to waste entropy ...
                is_php('5.4') && stream_set_chunk_size($fp, 16);

                $options['salt'] = '';
                for ($read = 0; $read < 16; $read = ($func_override) ? mb_strlen($options['salt'], '8bit') : strlen($options['salt'])) {
                    if (($read = fread($fp, 16 - $read)) === FALSE) {
                        log_message('error', 'compat/password: Error while reading from ' . $dev . '.');
                        return FALSE;
                    }
                    $options['salt'] .= $read;
                }

                fclose($fp);
            } else {
                log_message('error', 'compat/password: No CSPRNG available.');
                return FALSE;
            }

            $options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
        } elseif (!preg_match('#^[a-zA-Z0-9./]+$#D', $options['salt'])) {
            $options['salt'] = str_replace('+', '.', rtrim(base64_encode($options['salt']), '='));
        }

        isset($options['cost']) OR $options['cost'] = 10;

        return (strlen($password = crypt($password, sprintf('$2y$%02d$%s', $options['cost'], $options['salt']))) === 60)
            ? $password
            : FALSE;
    }
}

if (!function_exists('password_verify')) {
    /**
     * password_verify()
     *
     * @link    http://php.net/password_verify
     * @param    string $password
     * @param    string $hash
     * @return    bool
     */
    function password_verify($password, $hash) {
        if (strlen($hash) !== 60 OR strlen($password = crypt($password, $hash)) !== 60) {
            return FALSE;
        }

        $compare = 0;
        for ($i = 0; $i < 60; $i++) {
            $compare |= (ord($password[$i]) ^ ord($hash[$i]));
        }

        return ($compare === 0);
    }
}


if (!function_exists('page_lib')) {
    /**
     * password_verify()
     *
     * @param    int $page_size 每页数据量
     * @param    int $total 总数据
     * @return   string $create_links 分页代码
     */
    function page_lib($page_size, $total) {

        $CI =& get_instance();
        $CI->load->library('pagination');
        // 分页
        $config['base_url'] = currentUrl();
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['per_page'] = $page_size;
        $config['total_rows'] = $total;
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $CI->pagination->initialize($config);

        $create_links = $CI->pagination->create_links();

        return $create_links;
    }
}

//获取地址
function currentUrl() {
    $_url = $_SERVER["REQUEST_URI"];
    $_par = parse_url($_url);
    if (isset($_par['query'])) {
        parse_str($_par['query'], $_query);
        unset($_query['page']);
        $_url = $_par['path'] . '?' . http_build_query($_query);
    } else {
        $_url = $_par['path'] . '?';
    }
    return $_url;
}

function time_tran($the_time) {
    $now_time = date("Y-m-d H:i:s", time());
    //echo $now_time;
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return $the_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $the_time;
                    }
                }
            }
        }
    }
}





function get_region($partent_id = 0, $region_type = 0) {
    $CI =& get_instance();
    $CI->load->model('Region_mdl');
    $where = array('parent_id' => $partent_id);
    if ($region_type > 1) {
        $where['region_type'] = $region_type;
    }
    $lists = $CI->Region_mdl->get($where, 2);
    return $lists;
}

function get_self_region($id = 1) {
    $CI =& get_instance();
    $CI->load->model('Region_mdl');

    $region = $CI->Region_mdl->get(array('region_id' => $id));
    return $region;
}

function get_user_info($user_id = 0) {
    $CI =& get_instance();
    $CI->load->model('admin/User_mdl');

    $user = $CI->User_mdl->get(array('user_id' => $user_id));
//  $user['province'] = get_self_region($user['province'])['region_name'];
//  $user['city'] = get_self_region($user['city'])['region_name'];
//  $user['district'] = get_self_region($user['district'])['region_name'];
    $user['province'] = '';
    $user['city'] = "";
    $user['district'] = "";
    return $user;

}

	/**
	 * 浏览器友好的变量输出
	 * @param mixed $var 变量
	 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
	 * @param string $label 标签 默认为空
	 * @param boolean $strict 是否严谨 默认为true
	 * @return void|string
	 */
	function dump($var, $echo = true, $label = null, $strict = true)
	{
	    $label = ($label === null) ? '' : rtrim($label) . ' ';
	    if (!$strict) {
	        if (ini_get('html_errors')) {
	            $output = print_r($var, true);
	            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
	        } else {
	            $output = $label . print_r($var, true);
	        }
	    } else {
	        ob_start();
	        var_dump($var);
	        $output = ob_get_clean();
	        if (!extension_loaded('xdebug')) {
	            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
	            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
	        }
	    }
	    if ($echo) {
	        echo($output);
	        return null;
	    } else
	        return $output;
	}