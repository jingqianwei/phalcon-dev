<?php

class TestController extends \Phalcon\Mvc\Controller
{

    /**
     * 初始化内容
     * @return string
     */
    public function indexAction()
    {
        write_log('测试日志：' . var_export([1,2,3], true), 'info');

        return json_encode([1,2,3]);
    }

    /**
     * 输出详情
     */
    public function detailAction()
    {
        echo 'hello world!';
    }

}

