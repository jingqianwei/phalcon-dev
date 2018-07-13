<?php
use Phalcon\Arr;
use Phalcon\Http\Client\Adapter\Curl;

class TestController extends ControllerBase
{

    /**
     * 初始化内容
     * @return string
     */
    public function indexAction()
    {
        //$curl = new Curl('www.baidu.com/','post');
        //$curl->post('www.baidu.com/','1111');
        \Phalcon\Arr::get([1111], 0);
        $this->renderJson([1,2], 10001); //接口输出
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

