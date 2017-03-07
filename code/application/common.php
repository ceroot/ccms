<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

function encrypt_password($password, $salt)
{
    return '' === $password ? '' : md5(sha1($password) . sha1($salt));
}

/**
 * [ip2int ip地址转换为int]
 * @param   string  $ip  [ip地址]
 * @return  int          [返回整形数字]
 * @author  SpringYang <ceroot@163.com>
 */
function ip2int($ip = '')
{
    if ($ip == '') {
        $ip = get_client_ip();
    }
    return sprintf("%u", ip2long($ip));
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 随机数函数
 * @param  string    $length     [长度]
 * @param  int       $numeric    [类型 0为数字，1为全部，2为大小写，3为数字加大写，4为数字加小写，5为大写，6为小写，7为uniqid()]
 * @return string    $hash       [返回数字]
 * @author SpringYang <ceroot@163.com>
 */
function getrandom($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
    if ($length > 10 && $numeric == 0) {
        $numeric = 5;
    }

    $hash = '';
    switch ($numeric) {
        case 0:
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
            break;
        case 1:
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            break;
        case 2:
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            break;
        case 3:
            $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            break;
        case 4:
            $chars = '23456789abcdefghjkmnpqrstuvwxyz';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        case 5:
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            break;
        case 6:
            $chars = 'abcdefghjkmnpqrstuvwxyz';
            $max   = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            break;
        case 7:
            $uniqid = implode(null, array_map('ord', str_split(md5(uniqid()), 1)));
            $max    = strlen($uniqid) - 1;
            for ($i = 0; $i < $length; $i++) {
                $temp = $uniqid[mt_rand(0, $max)];
                // 去掉第一个为 0 的情况
                if ($i == 0 && $temp == 0) {
                    $temp = sprintf('%0' . 1 . 'd', mt_rand(0, pow(10, 1) - 1));
                }
                $hash .= $temp;
            }
            break;
        default:
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
            // 代码
    }
    return $hash;
}
