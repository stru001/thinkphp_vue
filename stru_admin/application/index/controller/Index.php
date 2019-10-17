<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    protected $middleware = [
        'Check' 	=> ['except' 	=> ['index'] ],
    ];
    public function index()
    {
        return $this->fetch();
    }

    public function hello(){
        print_r('hello,world');
    }

}
