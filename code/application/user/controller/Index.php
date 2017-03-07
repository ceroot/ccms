<?php
namespace app\user\controller;

//use app\user\model\ucenterMember;

class Index
{
    public function index()
    {
        return 'user';
    }

    public function yctest()
    {
        dump(encrypt_password(123456, 'yaIDbJHVTh'));
        //$user = new ucenterMember();
        $user = model('ucenterMember');
        $dd   = $user->dsf();
        dump($dd);
        return 'user';
    }

}
