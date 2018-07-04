<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/7/2
 * Time: 13:36
 */

namespace Util;

use Phalcon\Di as di;

/**
 * PHP 利用redis 做统计缓存mysql的压力
 * Class Cache
 * @package Util
 */
class Cache
{
    protected $redis = null;
    protected $mysql = null;

    public function initialize()
    {
        $di = di::getDefault();
        $this->redis = $di->get('redis');
        $this->mysql = $di->get('db');
    }

    public function index()
    {
        $url_md5 = '';
        if(isset($_SERVER['HTTP_REFERER'])){
            $url_md5 = md5($_SERVER['HTTP_REFERER']);
        }
        $adve_key = 'adve';
        $adve_key_exists = 'adve_exists';
        if (!$this->redis->exists($adve_key_exists)) {
            $list = $this->mysql->mysql_query("select * from user_online_adve");
            if ($list) {
                foreach ($list as $key => $value) {
                    $url_hash = md5($value['adve_url']);
                    $adve_hash_key = $adve_key.":".$url_hash;
                    $id = $value['id'];
                    $this->redis->set($adve_hash_key,$id);
                    $this->redis->set($adve_key_exists,true);
                    $this->redis->hmset($adve_hash_key, array('id' =>$id));
                    print_r($this->redis->get($adve_hash_key));
                }
            }
        }
        $adve_new_key = $adve_key.':'.$url_md5;
        if ($this->redis->exists($adve_new_key)) {
            $adve_plus = $adve_new_key.":plus" ;
            if (!$this->redis->exists($adve_plus)) {
                $this->redis->set($adve_plus,1);
            } else {
                $this->redis->incr($adve_plus);
                $num = $this->redis->get($adve_plus);
                if ($num >10) {
                    $id = $this->redis->get($adve_new_key);
                    // insert to sql;
                    $this->mysql->mysql_query("update user_online_adve set adve_num = adve_num + {$num} where id = {$id}");
                    $this->redis->set($adve_plus,1);
                }
            }
        }
        header('HTTP/1.0 301 Moved Permanently');
        header('Location: https://itunes.apple.com/cn/app/san-guo-zhi15-ba-wangno-da-lu/id694974270?mt=8');
    }
}