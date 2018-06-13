<?php

use Phalcon\Di as di;

class MongodbController extends \Phalcon\Mvc\Controller
{
    public $mongodb;

    /**
     * 初始化mongodb
     */
    public function initialize()
    {
        $di = di::getDefault();

        $this->mongodb = $di->get('mongodb');
    }

    //插入数据
    public function indexAction()
    {
        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]); //['ordered' => false],默认是有序的，如果要新建无序则ordered为true
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

        $data = [
            ['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com'],
            ['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com'],
            ['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com'],
        ];//插入的数据

        foreach ($data as $val) {
            $bulk->insert($val); //批量插入
        }

        $res = $this->mongodb->executeBulkWrite('test.cons', $bulk, $writeConcern);//database.collections(数据库.集合)

        var_dump($res);
    }

    //查询数据
    public function findAction()
    {
        $filter = ['x' => ['$gt' => 1]]; //查询条件

        $options = [
            'projection' => ['_id' => 0],//需要查询出来的字段，不设置默认查询所有字段
            'sort' => ['x' => -1], //按字段x进行倒序
        ];

        $query = new MongoDB\Driver\Query($filter,$options);
        $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);

        $cursor = $this->mongodb->executeQuery('test.cons', $query, $readPreference);

        foreach ($cursor as $document) {
            echo '<pre>';
            var_dump($document);
        }
    }

    //更新数据
    public function updateAction()
    {
        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        $filter = ['x' => 2]; //更新条件
        $newObj = ['$set' => ['name' => '菜鸟工具', 'url' => 'tool.runoob.com']]; //新的值，$set 是固定形式
        $updateOptions = ['multi' => false, 'upsert' => false]; //更新结果约束

        $bulk->update(
            $filter,
            $newObj,
            $updateOptions
        );

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $result = $this->mongodb->executeBulkWrite('test.cons', $bulk, $writeConcern);

        var_dump($result);
    }

    //删除数据
    public function deleteAction()
    {
        $bulk = new MongoDB\Driver\BulkWrite;

        $bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
        $bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $result = $this->mongodb->executeBulkWrite('test.cons', $bulk, $writeConcern);

        var_dump($result);
    }

}

