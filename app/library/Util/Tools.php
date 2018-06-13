<?php

namespace Util;

use Phalcon\Logger\Formatter\Line as LineFormatter;
use Phalcon\Logger\Adapter\File as FileAdapter;

class Tools
{
    /**
     * @ignore
     * @method curlRequest
     *
     * @param string $url 网页地址
     * @param string $method HTTP 方法默认'GET'
     * @param null $parameters HTTP 参数集合
     * @param array $headers HTTP 请求头设置
     * @param string $user_agent user_agent
     * @return bool|mixed|string
     */
    public static function curlRequest($url = '', $method = "GET", $parameters = null, $headers = array(), $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)')
    {

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);

            $https_array = explode('://', $url);
            $head = $https_array[0];
            if ($head == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }

            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            if (!empty($user_agent)) {
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            }

            switch ($method) {
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    if (!empty($parameters)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                    }
                    break;
                case 'GET':
                    if (!empty($parameters)) {
                        $url = $url . '?' . http_build_query($parameters);
                    }
                    break;
            }

            curl_setopt($ch, CURLOPT_URL, $url);

            $response = curl_exec($ch);

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                //echo "Curl Error: ", json_encode(curl_error($ch));
                return false;
            }

            if ($http_code != '200') {
                //echo "Code ",$http_code," Error: ", json_encode(curl_getinfo($ch));
                return false;
            }

            curl_close($ch);
            return $response;
        } catch (\Exception $ex) {
            //echo "Exception Error: ", json_encode($ex);
            return false;
        }
    }

    /**
     * 生成随机字符串
     * @param int $length 位数
     * @return string
     */
    public static function randomString($length = 6)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    /**
     * 初始化日志对象,同时设置日志输出格式
     * $format: 日志信息格式  $dateFormat: 日期格式
     * 参考网址 https://blog.csdn.net/qzfzz/article/details/39995715
     * @param $path
     * @param string $format
     * @param string $dataFormat
     * @return FileAdapter
     */
    public static function initLogData($path, $format = "[%date%] [%type%]: %message%", $dataFormat = 'Y-m-d H:i:s')
    {
        $logger = new FileAdapter($path); //实例化日志对象
        $formatter = new LineFormatter($format, $dataFormat);//定义信息格式
        $logger->setFormatter($formatter); //设置日志信息

        return $logger;
    }

    /**
     * 创建文件夹
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function mkDirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return TRUE;
        }

        if (!self::mkdirs(dirname($dir), $mode)) {
            return FALSE;
        }

        return @mkdir($dir, $mode);
    }
}