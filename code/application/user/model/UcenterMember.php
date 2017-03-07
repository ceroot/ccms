<?php
// +----------------------------------------------------------------------+
// | CYCMS                                                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2016 http://beneng.com All rights reserved.            |
// +----------------------------------------------------------------------+
// | Authors: SpringYang [ceroot@163.com]                                 |
// +----------------------------------------------------------------------+
/**
 * @filename  Manager.php[管理员表模型]
 * @authors   SpringYang
 * @email     ceroot@163.com
 * @QQ        525566309
 * @date      2016-03-29 17:41:58
 * @site      http://www.benweng.com
 */

namespace app\user\model;

use think\Model;

class UcenterMember extends Model
{

    // 自动完成
    protected $auto   = [];
    protected $insert = ['create_uid', 'create_time', 'create_ip'];
    protected $update = ['update_uid', 'update_time', 'update_ip'];

    // public function setPasswordAttr($value, $data)
    // {
    //     return md5(input('username') . input('password'));
    // }

    public function setLoginIpAttr($value)
    {
        return ip2int();
    }

    public function setCreateUidAttr($value, $data)
    {
        if (input('password')) {
            $this->data['password'] = md5(input('username') . input('password'));
        }

        return UID;
    }

    public function setCreateIpAttr()
    {
        return ip2int();
    }

    public function setUpdateUidAttr($value, $data)
    {
        return session('userid');
    }

    public function setUpdateIpAttr()
    {
        return ip2int();
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '正常'];
        return $status[$data['status']];
    }

    public function getCreateTimeShowAttr($value, $data)
    {
        if ($data['create_time'] == 0) {
            $revalue = '注册时间不详';
        } else {
            $revalue = time_format($data['create_time']);
        }
        return $revalue;
    }

    public function getLoginTimeShowAttr($value, $data)
    {
        if ($data['login_time'] == 0) {
            $revalue = '还没有进行登录';
        } else {
            $revalue = time_format($data['login_time']);
        }
        return $revalue;
    }

    // public function __construct()
    // {
    //     // $ddd = 0;
    // }
    public function dsf()
    {
        return encrypt_password(1, 2);
    }
    /**
     * [login 用户登录认证]
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        $map['username|email|mobile'] = $username;

        // 数据查询
        $user = $this::get(function ($q) use ($map) {
            $q->where($map)->field('id,password,status,salt');
        });

        if ($user) {
            // 判断用户状态
            if ($user['status']) {
                if ($user['password'] != encrypt_password($password, $user['salt'])) {
                    $this->error = '密码错误';
                    return -1;
                } else {
                    $this->updateLogin($user->id); // 更新登录记录
                    return $user->id;
                }
            } else {
                $this->error = '用户锁定中，请联系管理员';
                return -2;
            }
        } else {
            $this->error = '用户名不存在';
            return -3;
        }
    }

    /**
     * [update_login 更新登录信息]
     * @param  [type] $uid [description]
     * @return [type]       [description]
     */
    public function updateLogin($uid)
    {
        $data['id']         = $uid;
        $data['times']      = array('exp', '`times`+1');
        $data['login_time'] = time();
        $data['login_ip']   = ip2int();
        $this->update($data);
    }

}
