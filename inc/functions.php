<?php

/**
 * 获取GET或POST过来的参数
 * @param $key 键值
 * @param $default 默认值
 * @return 获取到的内容（没有则为默认值）
 */
function getParam($key,$default='')
{
    return trim($key && is_string($key) ? (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default)) : $default);
}

/**
 * 输出一条简短的消息（一般是错误消息）
 * @param $code 消息代码
 * @param $msg 消息内容
 */
function echoMsg($code, $msg)    //发出消息
{
    $tempArr = array('code'=>$code,'msg'=>$msg);
    echojson(json_encode($tempArr));
}

/**
 * 输出返回结果，支持输出 json和jsonp 格式
 * @param $data 输出的内容(json格式)
 */
function echoJson($data)    //json和jsonp通用
{
    header("Content-type: application/json");
    $callback = getParam('callback');
    if($callback != '') //输出jsonp格式
    {
        die(htmlspecialchars($callback).'('.$data.')');
    }
    else
    {
        die($data);
    }
}

/**
 * 输出404页面
 * @param $errTitle 页面标题
 * @return 输出404
 */
function die404($errTitle = '404') {
    require(SYSTEM_ROOT.'/404.php');
    exit();
}

/**
 * curl 获取网页源码函数
 * @param $url 目标页面 URL
 * @return 页面源码
 */
function curl($url){ 
    $ch = curl_init(); 
    $timeout = 30; 
    $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);   // 伪造ua 
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); // 取消gzip压缩
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $content = trim(curl_exec($ch)); 
    curl_close($ch); 
    return $content; 
}

/**
 * 返回配置
 * @param $name 配置名称
 */
function C($name=''){
	global $webConfig;
	if(!$name)
		return $webConfig;
	else
		return $webConfig[$name]? $webConfig[$name]: '';
}

/**
 * 写 Cookie
 * @param $key 键
 * @param $val 值
 * @param $time 过期时间
 */
function writeCookie($key, $val, $time = 0) {
    $key = 'mkmovie_'.$key;
    setcookie($key, $val, $time, '/');
}

/**
 * 读 Cookie
 * @param $key 键
 * @param $default 默认值
 */
function readCookie($key, $default = '') {
    $key = 'mkmovie_'.$key;
    if(empty($_COOKIE[$key])) {
        return $default;
    } else {
        return $_COOKIE[$key];
    }
}
