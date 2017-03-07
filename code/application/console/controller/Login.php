<?php
namespace app\console\controller;

use app\common\controller\Extend;
use app\user\api\UserApi;

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
            /******接收数据******/
            $username = input('post.username');
            $password = input('post.password');
            $code     = input('post.verify');

            // 检测错误次数
            $error_num = session('error_num');
            if (!isset($error_num)) {
                session('error_num', 0);
            }

            // 验证码验不为空
            if ($error_num > 3 && !$code) {
                $error = '请输入验证码';
                $this->error($error);
            }

            // 验证码是否相等
            if ($error_num > 3 && !captcha_check($code)) {
                $error = '验证码输入错误';
                $this->error($error);
            }

            // 实例化用户 Api
            $user = new UserApi();
            $uid  = $user->login($username, $password); // 用户验证成功取得用户 id

            if ($uid > 0) {
                $manager = $this->model->login($uid); // 管理员用户验证
                if ($manager) {
                    session('error_num', 0); // 登录成功设置登录错误记录的 session 为 0
                    $time = date('YmdHis') . getrandom(128);
                    $this->success('登录成功', url('console/index/index?time=' . $time), array('error_num' => 0));
                } else {
                    $showCode = $error_num >= 3 ? 1 : 0;
                    $this->error($this->model->getError(), '', array('show_code' => $showCode, 'error_num' => $error_num));
                }
            } else {
                session('error_num', $error_num + 1); // 错误次数加 1

                switch ($uid) {
                    case -1:$error = '密码错误！';
                        break; //系统级别禁用
                    case -2:$error = '用户锁定中，请联系管理员';
                        break;
                    case -3:$error = '用户名不存在';
                        break;
                    default:$error = '未知错误！';
                        break; // 0-接口参数错误（调试阶段使用）
                }
                $showCode = $error_num >= 3 ? 1 : 0; // 是否显示验证码
                $this->error($error, '', array('show_code' => $showCode, 'error_num' => $error_num));
            }
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
        echo $error_num > 3 ? 1 : 0;
    }

    public function yctest()
    {
        $error_num = session('error_num');
        if (!isset($error_num)) {
            session('error_num', 0);
        }
        // 验证码验不为空
        if ($error_num > 3 && !$code) {
            $this->error = '请输入验证码';
            return false;
        }
        // 验证码是否相等
        if ($error_num > 3 && !captcha_check($code)) {
            $this->error = '验证码输入错误';
            return false;
        }

        $user = new UserApi();
        $uid  = $user->login('admin', 123456);

        if ($uid > 0) {
            // 设置登录错误记录的session为0
            session('error_num', 0);

            $manager = $this->model->login($uid);
            if ($manager) {
                dump($manager);
            } else {
                dump($this->model->getError());
            }
        } else {

            session('error_num', $error_num + 1);

            dump($uid);
        }

    }

}
