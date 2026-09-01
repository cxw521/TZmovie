<?php
// 电视直播流代理：解决 m3u8 跨域(CORS)和防盗链问题
require_once('init.php');

$url = getParam('u');
if($url === '' || (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0)) {
    die('invalid url');
}

$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Referer: ' . parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/',
    'Accept: */*',
));
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
$content = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=60');

if($content === false || $code >= 400) {
    http_response_code($code >= 400 ? $code : 502);
    die('proxy error');
}

// m3u8 播放列表：重写内部相对地址为代理地址
if(stripos($contentType, 'mpegurl') !== false || stripos($content, '#EXTM3U') === 0) {
    $base = dirname($finalUrl);
    if(substr($base, -1) !== '/') $base .= '/';
    
    $lines = explode("\n", $content);
    foreach($lines as $i => $line) {
        $line = trim($line);
        // 只重写非 # 开头的 URL 行（分片/子列表）
        if($line !== '' && $line[0] !== '#') {
            if(stripos($line, 'http://') === 0 || stripos($line, 'https://') === 0) {
                $lines[$i] = 'live_proxy.php?u=' . urlencode($line);
            } else {
                $lines[$i] = 'live_proxy.php?u=' . urlencode($base . $line);
            }
        }
    }
    $content = implode("\n", $lines);
    header('Content-Type: application/vnd.apple.mpegurl;charset=utf-8');
} else {
    if($contentType) header('Content-Type: ' . $contentType);
}
echo $content;
