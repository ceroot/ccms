<?php
namespace app\console\controller;

use app\common\controller\Extend;

class Login extends Extend
{
    public $model;

    /**
     * [_initialize 初始化]
     */
    public function _initialize()
    {
        //echo 'init<br/>';
        parent::_initialize();
        $this->model = model('manager');
    }
    // 登录首页
    public function index()
    {
        if (request()->isAjax()) {

            if ($user = $this->model->validateLogin()) {
                //return 1;
                // 设置登录错误记录的session为0
                session('error_num', 0);
                // 设置session
                $this->model->setSession($user); // 登录成功，写入缓存
                $this->model->updateLogin($user); // 登录成功，更新最后一次登录

                // 记录登录日志
                action_log($user['id'], 'console_login', 'manager', $user['id']);

                $time   = date('YmdHis') . getrandom(128);
                $redata = array('status' => 1, 'info' => '登录成功', 'url' => url('console/index/index?time=' . $time), 'error_num' => 0);
            } else {
                $error_num = session('error_num');
                if ($error_num >= 3) {
                    $redata = array('status' => 0, 'info' => $this->model->getError(), 'show_code' => 1, 'error_num' => $error_num);
                } else {
                    $redata = array('status' => 0, 'info' => $this->model->getError(), 'show_code' => 0, 'error_num' => $error_num);
                }
            }
            return $redata;
        } else {
            // if (session('userid')) {
            //     $this->redirect(input('get.backurl'));
            // }
            return view();
        }
    }

    // 显示验证码
    public function showverify()
    {
        $error_num = session('error_num');
        if ($error_num > 3) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function yctest()
    {
        $user = model('Manager');
        $dd   = $user->validateLogin();
        dump($dd);
    }

}
