<?php
namespace app\user\api;

use app\user\model\UcenterMember;

class UserApi
{
    public $model;

    /**
     * [_initialize 初始化]
     */
    public function _initialize()
    {
        //$this->model = new UcenterMember;
        //parent::_initialize();
        //$this->model = model('UcenterMember');
        //$this->model = Loader::model('UcenterMember');

    }

    /**
     * 构造方法，检测相关配置
     */
    public function __construct()
    {
        $this->model = new UcenterMember;
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
        return $this->model->login($username, $password);
    }

}
