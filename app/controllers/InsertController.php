<?php

use Phalcon\Di as di;

/**
 * 联系phalcon框架的CURD写的demo，参考网址 https://www.cnblogs.com/Kassadin/p/5433865.html
 * Class InsertController
 */
class InsertController extends ControllerBase
{
    public $redis;

    /**
     * 控制器的初始化函数，一般不使用构造函数
     * 参考网址 https://www.jianshu.com/p/2bd5c77dd2d8
     * 作用：初始化redis
     */
    public function initialize()
    {
        $di = di::getDefault();
        $this->redis = $di->get('redis');
    }

    /**
     * 插入数据
     */
    public function indexAction()
    {
        //$this->redis->set('ddd','hahhahaha');redis设置数据
        //$this->redis->get('ddd');redis获取数据

        $customer = new Customer();

        $customer->username = 'zdd';
        $customer->password = '234';

        $res = $customer->save();

        if ($res) {
            echo '插入成功';
        } else {
            echo '插入失败';
        }
    }

    /**
     * 查询所有数据
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function findAction()
    {
        $customer = Customer::find();

        $this->response->setContentType('application/json', 'UTF-8');

        return $this->response->setJsonContent($customer->toArray());
    }

    /**
     * 查询单条数据,如果不给参数，默认查询第一条数据
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function findFirstAction()
    {
        $customer = Customer::findFirst();

        $this->response->setContentType('application/json', 'UTF-8');

        return $this->response->setJsonContent($customer->toArray());
    }

    /**
     * phalcon框架支持 findBy<属性> 的这种查询方法
     */
    public function findByAttributeAction()
    {
        $customer = Customer::findFirstByUsername('jqw');

        if ($customer) {
            echo $customer->password;
        } else {
            echo '用户不存在';
        }
    }

    /**
     * 修改用户数据
     * @param string $username
     */
    public function updateAction($username = 'chinwe')
    {
        $conditions = 'username = :username:';

        $parameters = [
            'username' => 'jqw'
        ];

        $customer = Customer::findFirst(
            [
                $conditions,
                'bind' => $parameters,
            ]);

        if ($customer) {
            $customer->username = $username;
            $customer->save();
            echo '修改成功';
        } else {
            echo '修改失败';
        }

    }

    /**
     *  删除用户数据
     * @param string $username
     */
    public function deleteAction($username = 'zdd')
    {
       $customer = Customer::findFirstByUsername($username);

       if (!$customer) {
           echo '用户不存在';die;
       }

       $res = $customer->delete();

       if ($res) {
           echo '删除成功';
       } else {
           echo '删除失败';
       }
    }

    /**
     * 模拟等登陆接口请求
     */
    public function loginAction()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $conditions = 'username = :username: and password = :password:';
        $parameters = [
            'username' => $username,
            'password' => $password,
        ];

        $res = Customer::findFirst(
            [
                $conditions,
                'bind' => $parameters,
            ]);

        if($res){
            print_r('login success');
        } else {
            print_r('login failed');
        }
    }

    /**
     * 模拟修改密码接口
     */
    public function updatePasswordAction()
    {
        $password = $this->request->getPost('password'); //根据post提交获取数据
        $newPassword = $this->request->getPost('new_password');

        $conditions = 'username = :username: and password = :password:';

        $parameters = [
            'username' => 'chinwe',
            'password' => $password,
        ];

        $customer = Customer::findFirst([
            $conditions,
            'bind' => $parameters,
        ]);

        if($customer){
            $customer->password = $newPassword;
            $customer->save();
            print_r('更新成功');
        } else {
            print_r('用户名不存在或密码错误');
        }
    }
}

