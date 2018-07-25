<?php
/**
 * Created by PhpStorm.
 * User: zhangchaozheng
 * Date: 2018/5/7
 * Time: 11:03
 */

namespace SbDaRepository;

use Phalcon\JWT\JWT;
use Util\Tools;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 用户登录 token
     * @param $userId
     * @return string
     */
    public function getToken($userId)
    {
        $privateKey = $this->jwt->privateKey;

        $token = array(
            "iss" => $userId,
            "date" => time(),
        );

        $jwt = JWT::encode($token, $privateKey, 'RS256');
        return $jwt;
    }

    /**
     * 检查用户token
     * @param $token
     * @param $userId
     * @return bool
     */
    public function checkToken($token,$userId)
    {
        $redisKey = RedisRepository::USER_PREFIX.$userId;

        if(!$this->redis->exists($redisKey)) {
            return false;
        }

        $user = unserialize($this->redis->get($redisKey));

        return $user['token'] == $token;
    }

    /**
     * 通过token获取用户id
     * @param $token
     * @return mixed
     */
    public function getUserId($token)
    {
        if( !empty($token) ) {
            $publicKey = $this->jwt->publicKey;
            $decoded = JWT::decode($token, $publicKey, array('RS256'));

            $decodedArray = (array) $decoded;

            return $decodedArray['iss'];
        }
        return false;
    }

    /**
     * 用户登录
     * @param $userName
     * @param $password
     * @return bool
     */
    public function signIn($userName,$password)
    {
        $userLoginApi = $this->config->userApi.'/login/login';

        $result = Tools::curlRequest($userLoginApi,'POST',[
            'login_name' => $userName,
            'login_pwd' => $password,
        ]);

        if( $result ) {
            $result = json_decode($result,true);
            if($result['code'] == 200 ) {
                $data = $result['data'];

                if(empty($data)) {
                    return false;
                }
                unset($data['lg_ck_sign']);
                $data['token'] = $this->getToken($data['id']);
                $redisKey = RedisRepository::USER_PREFIX.$data['id'];
                $this->redis->set($redisKey,serialize($data));
                return $data;
            }
        }

        return false;

    }
}