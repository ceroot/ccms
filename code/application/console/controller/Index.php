<?php
namespace app\console\controller;

class Index
{
    public function index()
    {
        return 'console';
    }

    public function login()
    {
        if (request()->isPost()) {
            return 123;
        } else {
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

}
