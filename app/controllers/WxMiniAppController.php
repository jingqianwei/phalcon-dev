<?php

use Util\Tools;
use Phalcon\Di;

class WxMiniAppController extends \Phalcon\Mvc\Controller
{
    //微信接口url
    const URL = 'https://api.weixin.qq.com/sns/jscode2session';
    protected $redis;
    protected $logger;

    /**
     * 初始化
     */
    public function initialize()
    {
        $di = di::getDefault();
        $this->redis = $di->get('redis');
        date_default_timezone_set('PRC');//设置时区
        $logDir = BASE_PATH . '/logs/function';
        Tools::mkDirs($logDir);
        $this->logger = Tools::initLogData($logDir . '/phalcon-' . date('Y-m-d') . '.log');
    }

    /**
     * @throws Exception
     */
    public function indexAction()
    {
        //调用接口get方式传过来的参数
        $code = $this->request->get('code', '033B82Vy155Vcg0DLCTy1pg9Vy1B82VV');

        $params = [
            'appid' => 'wx6346689153bcf1ba',
            'secret' => 'e84decca6acf6f3739bd80939c18f6d1',
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];

        $response = json_decode(
            Tools::curlRequest(self::URL, 'GET', $params),
            true
        );

        //开启日志事务
        $this->logger->begin();

        //记录接口返回的结果
        $this->logger->info('接口返回的结果为：' . var_export($response, true));

        //保存消息到文件中
        $this->logger->commit();

        //接口无响应
        if (!$response) {
            throw new \Exception('network error');
        }

        //接口请求失败
        if (isset($response['errcode'])) {
            throw new \Exception($response['errmsg']);
        }

        //$response 返回的数组，所以要序列化处理才能用set存储
        $session_id = uniqid(Tools::randomString(10)); //生成随机的session_id
        $this->redis->set('session_id:' . $session_id, serialize($response)); //存入redis
    }
}

