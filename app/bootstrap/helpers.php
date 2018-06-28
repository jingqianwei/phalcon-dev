<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/6/15
 * Time: 11:53
 */

use Util\Tools;

if (! function_exists('curl_request')) {

    /**
     * @description curl的http请求
     * @method curl_request
     *
     * @param string $url 网页地址
     * @param string $method HTTP 方法默认'GET'
     * @param null $parameters HTTP 参数集合
     * @param array $headers HTTP 请求头设置
     * @param string $user_agent user_agent
     * @return bool|mixed|string
     */
    function curl_request($url = '', $method = "GET", $parameters = null, $headers = array(), $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)')
    {
       return Tools::curlRequest($url, $method, $parameters, $headers, $user_agent);
    }
}

if (! function_exists('random_string')) {

    /**
     * 生成随机字符串
     * @param int $length 位数
     * @return string
     */
    function random_string($length = 6)
    {
        return Tools::randomString($length);
    }
}

if (! function_exists('mk_dirs')) {

    /**
     * 创建文件夹
     * @param $dir
     * @param int $mode
     * @return bool
     */
    function mk_dirs($dir, $mode = 0777)
    {
        return Tools::mkDirs($dir, $mode);
    }
}

if (! function_exists('xml_to_array')) {

    /**
     * 将xml转换为数组
     * @param string $xml  需要转化的xml
     * @return mixed
     */
    function xml_to_array($xml)
    {
        return Tools::xml_to_array($xml);
    }
}

if (! function_exists('data_to_xml')) {

    /**
     * 将数组转化成xml
     * @param mixed $data 需要转化的数组
     * @return string
     */
    function data_to_xml($data)
    {
        return Tools::data_to_xml($data);
    }
}

if (! function_exists('xml_post_request')) {

    /**
     * PHP post请求之发送XML数据
     * @param string $url 请求的URL
     * @param $xmlData
     * @return mixed
     */
    function xml_post_request($url, $xmlData)
    {
        return Tools::xml_post_request($url, $xmlData);
    }
}

if (! function_exists('http_post_json')) {

    /**
     * PHP post请求之发送Json对象数据
     *
     * @param string $url 请求url
     * @param string $jsonStr 发送的json字符串
     * @return array
     */
    function http_post_json($url, $jsonStr)
    {
        return Tools::http_post_json($url, $jsonStr);
    }
}

if (! function_exists('http_post_array')) {

    /**
     * PHP post请求之发送数组
     * @param $url
     * @param array $param
     * @return mixed
     * @throws \Exception
     */
    function http_post_array($url, $param = array())
    {
        return Tools::httpPostArray($url, $param);
    }
}

if (! function_exists('get_request_bean')) {

    /**
     * 接收xml数据并转化成数组
     * @return array
     */
    function get_request_bean()
    {
        return Tools::getRequestBean();
    }
}

if (! function_exists('get_json_data')) {

    /**
     * 接收json数据并转化成数组
     * @return array
     */
    function get_json_data()
    {
        return Tools::getJsonData();
    }
}

if (! function_exists('m_strrev')) {

    /**
     * 翻译中英文字符串（调换位置）
     * @param $string
     * @return string
     */
    function m_strrev($string)
    {
        return Tools::m_strrev($string);
    }
}

if (! function_exists('get_os')) {

    /**
     * 判断当前服务器系统
     * @return string
     */
    function get_os()
    {
        return Tools::getOS();
    }
}

if (! function_exists('write_log')) {

    /**
     * 日志方法
     * @param string|array $content 日志内容
     * @param string $type 日志类型
     * @param string $date 按什么日期命名日志文件
     * @return bool
     */
    function write_log($content, $type = 'debug', $date = 'today')
    {
        return Tools::writeLog($content, $type, $date);
    }
}

if (! function_exists('validate_sign')) {

    /**
     * 签名验证函数
     * @param string $param 需要加密的字符串
     * @param string $sign  第三方已经机密好的用来比对的字串
     * @return bool
     */
    function validate_sign($param, $sign)
    {
        return Tools::ValidateSign($param, $sign);
    }
}

if (! function_exists('is_validator')) {

    /**
     * 来检测变量是否为空
     * @param $param
     * @return bool
     */
    function is_validator($param)
    {
        return Tools::isValidator($param);
    }
}

if (! function_exists('instance_log_object')) {

    /**
     * 实例化日志对象
     * @param $logDir
     * @param $logFileName
     * @return \Phalcon\Logger\Adapter\File
     */
    function instance_log_object($logDir, $logFileName)
    {
        return Tools::instanceLogObject($logDir, $logFileName);
    }
}

if (! function_exists('deep_in_array')) {

    /**
     * 来检测多维数组中是否包含某个值
     * @param string $value 要检测的值
     * @param array $array 一维或多维数组
     * @param null $key 当$key为null时，$array为多维数组，当$key为检测的键值时，$array为一维数组
     * @return bool
     */
    function deep_in_array($value, $array, $key = null)
    {
        return Tools::deepInArray($value, $array, $key);
    }
}

if (! function_exists('object_array')) {

    /**
     * 对象转化为数组
     * @param $array (值为对象，或者数组里面为对象)
     * @return array
     */
    function object_array($array)
    {
        return Tools::objectArray($array);
    }
}

if (! function_exists('return_bytes')) {

    /**
     * 返回字节数 (不确定内存的单位是什么，统一转化为字节单位)
     * @param string $val
     * @return int|string
     */
    function return_bytes($val = '')
    {
        return Tools::returnBytes($val);
    }
}

if (! function_exists('get_need_date')) {

    /**
     * 获取需要的时间
     * @param string $time
     * @param string $date
     * @param bool $flag
     * @return int|string
     */
    function get_need_date($time = '', $date = '', $flag = true)
    {
        return Tools::getNeedDate($time, $date, $flag);
    }
}

if (! function_exists('request_api_timeout')) {

    /**
     * @Describe: 获取接口请求花费的时间
     * @param float $start 开始时间
     * @param string $company 最终得到时间的单位
     * @return string
     */
    function request_api_timeout($start, $company = 'ms')
    {
        return Tools::requestApiTimeOut($start, $company);
    }
}

if (! function_exists('get_client_ip')) {

    /**
     * @Describe: 获取客户端请求ip
     * @return array|false|string
     */
    function get_client_ip()
    {
        return Tools::getClientIp();
    }
}