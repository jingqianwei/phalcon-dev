<?php

namespace Util;

use Phalcon\Logger\Formatter\Line as LineFormatter;
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * 参考网址 https://www.cnblogs.com/Steven-shi/p/5896903.html
 */
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

    /**
     * 将xml转换为数组
     * @param string $xml  需要转化的xml
     * @return mixed
     */
    public static function xml_to_array($xml)
    {
        $ob = simplexml_load_string($xml);
        $json = json_encode($ob);
        $array = json_decode($json, true);
        return $array;
    }

    /**
     * 将数组转化成xml
     * @param mixed $data 需要转化的数组
     * @return string
     */
    public static function data_to_xml($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $xml = '';
        foreach ($data as $key => $val) {
            if (is_null($val)) {
                $xml .= "<$key/>\n";
            } else {
                if (!is_numeric($key)) {
                    $xml .= "<$key>";
                }
                $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
                if (!is_numeric($key)) {
                    $xml .= "</$key>";
                }
            }
        }
        return $xml;
    }

    /**
     * PHP post请求之发送XML数据
     * @param string $url 请求的URL
     * @param $xmlData
     * @return mixed
     */
    public static function xml_post_request($url, $xmlData)
    {
        $header[] = "Content-type: text/xml"; //定义content-type为xml,注意是数组
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    /**
     * PHP post请求之发送Json对象数据
     *
     * @param string $url 请求url
     * @param string $jsonStr 发送的json字符串
     * @return array
     */
    public static function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return array($httpCode, $response);
    }

    /**
     * PHP post请求之发送数组
     * @param $url
     * @param array $param
     * @return mixed
     * @throws \Exception
     */
    public static function httpPostArray($url, $param = array())
    {
        $ch = curl_init(); // 初始化一个 cURL 对象
        curl_setopt($ch, CURLOPT_URL, $url); // 设置需要抓取的URL
        curl_setopt($ch, CURLOPT_HEADER, 0); // // 设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        // 如果你想PHP去做一个正规的HTTP POST，设置这个选项为一个非零值。这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param)); // 传递一个作为HTTP “POST”操作的所有数据的字符串。//http_build_query:生成 URL-encode 之后的请求字符串
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type:application/x-www-form-urlencoded;charset=utf-8'
        ));
        $rtn = curl_exec($ch); // 运行cURL，请求网页
        if ($errNo = curl_errno($ch)) {
            throw new \Exception ('Curl Error(' . $errNo . '):' . curl_error($ch));
        }
        curl_close($ch); // 关闭URL请求
        return $rtn; // 返回获取的数据
    }

    /**
     * 接收xml数据并转化成数组
     * @return array
     */
    public static function getRequestBean()
    {
        //simplexml_load_string() 函数把 XML 字符串载入对象中。如果失败，则返回 false。
        $bean = simplexml_load_string(file_get_contents('php://input'));
        $request = array();
        foreach ($bean as $key => $value) {
            $request [( string )$key] = ( string )$value;
        }
        return $request;
    }

    /**
     * 接收json数据并转化成数组
     * @return mixed
     */
    public static function getJsonData()
    {
        $bean = file_get_contents('php://input');
        $result = json_decode($bean, true);
        return $result;
    }

    /**
     * 翻译中英文字符串（调换位置）
     * @param $string
     * @return string
     */
    public static function m_strrev($string)
    {
        $num = mb_strlen($string, 'utf-8');
        $new_string = "";
        for ($i = $num - 1; $i >= 0; $i--) {
            $char = mb_substr($string, $i, 1, 'utf-8');
            $new_string .= $char;
        }
        return $new_string;
    }

    /**
     * 判断当前服务器系统
     * @return string
     */
    public static function getOS()
    {
        if (PATH_SEPARATOR == ':') {
            return 'Linux';
        } else {
            return 'Windows';
        }
    }

    /**
     * 日志方法
     * @param string $log 写人日志的内容
     * @return bool
     */
    public static function writeLog($log)
    {
        $dir = __DIR__ . "/../logs/";
        self::mkDirs($dir);
        $filename = $dir . date("Y-m-d") . ".log";
        file_put_contents($filename, date("Y-m-d H:i:s") . "\t" . $log . PHP_EOL, FILE_APPEND);

        return true;
    }

    /**
     * 签名验证函数
     * @param string $param 需要加密的字符串
     * @param string $sign  第三方已经机密好的用来比对的字串
     * @return bool
     */
    public static function ValidateSign($param, $sign)
    {
        if (md5($param) == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 来检测变量是否为空
     * @param $param
     * @return bool
     */
    public static function isValidator($param)
    {
        if (is_numeric($param) and $param === 0) {
            return true;
        }

        if (!empty($param)) {
            return true;
        }

        return false;
    }

    /**
     * 实例化日志对象
     * @param $logDir
     * @param $logFileName
     * @return FileAdapter
     */
    public static function instanceLogObject($logDir, $logFileName)
    {
        self::mkDirs($logDir); //日志目录
        return self::initLogData($logDir . $logFileName);
    }

    /**
     * 来检测多维数组中是否包含某个值
     * @param string $value 要检测的值
     * @param array $array 一维或多维数组
     * @param null $key 当$key为null时，$array为多维数组，当$key为检测的键值时，$array为一维数组
     * @return bool
     */
    public static function deepInArray($value, $array, $key)
    {
        if (!is_null($key)) {
            if (in_array($value, $array)) {
                return true;
            }
            return false;
        }

        foreach($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if (in_array($value, $item)) {
                return true;
            } elseif (self::deepInArray($value, $item, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 参考网址为：http://www.thinkphp.cn/code/2903.html
     * 对象转化为数组
     * @param $array (值为对象，或者数组里面为对象)
     * @return array
     */
    public static function objectArray($array)
    {
        if (empty($array)) {
            return null;
        }

        if (is_object($array)) {
            $array = (array)$array;
        }

        if (is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = self::objectArray($value);
            }
        }

        return $array;
    }

    /**
     * 返回字节数
     * @param string $val
     * @return int|string
     *
     * 特别说明：$val = 400M时，case 'm' 被命中，其下的 $val *= 1024; 被执行，
     * 但因为没有 break 语言，所以会继续命中 case 'k'，并执行其下的 $val *= 1024;
     * 语句，so，总体上相当于执行了 400 * 1024 * 1024
     */
    public static function returnBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val{strlen($val)-1});
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * @Describe: 获取需要的时间
     * @param string $time 基准时间 ，例如 2018-06-25 00:00:00
     * @param string $date 时间条件 +1 day
     * @param bool $flag 返回天数还是完成时间
     * @return false|string 返回的时间是基准时间和时间条件之和后返回的时间
     */
    public static function getNeedDate($time, $date, $flag)
    {
        $format = $flag ? 'Y-m-d' : 'Y-m-d H:i:s';

        return date($format, strtotime($time . $date));
    }

    /**
     * @Describe: 获取接口请求花费的时间
     * @Author: chinwe.jing
     * @Data: 2018/6/27 14:40
     * @param float $start 开始时间
     * @param string $company 最终得到时间的单位
     * @return string
     */
    public static function requestApiTimeOut($start, $company)
    {
        $last = strtolower($company);
        $time = microtime(true) - $start; //接口请求花费的时间
        switch ($last) { //时间单位转换
            case 's': //秒
                $time /= 1000 * 1000;
                break;
            case 'ms': //毫秒
                $time /= 1000;
                break;
            case 'ns': //纳秒
                $time *= 1000;
                break;
        }

        return $time . $company;
    }
}