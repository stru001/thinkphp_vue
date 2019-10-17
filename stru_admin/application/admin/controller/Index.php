<?php
namespace app\admin\controller;
use think\Controller;

class Index extends Controller
{
//    protected $middleware = ['Check'];
    public function index()
    {
        return $this->fetch();
    }

}
