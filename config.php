<?php
// 网站配置文件
if(!defined('movie')) die('非法访问 - Insufficient Permissions');


// 网站的一些设置
$webConfig = array(
    'siteurl' => '',    // 网站网址（部署在站点根目录时留空）
    
    'name' => '桃子味🍑影院',    // 网站名称
    
    'slogan' => '无广告在线看电影',    // 网站口号
    
    /*
    * API解析接口配置
    * 找最新有效的视频解析接口可以来这里：http://tool.taoziwei.com/movie/share.php
    */ 
    'videoapi' => array(
            array(
                'name' => '爱豆-VIP(默认)',
                'url' => 'https://jx.aidouer.net/?url='
            ),
            array(
                'name' => '虾米-VIP',
                'url' => 'https://jx.xmflv.com/?url='
            ),
            array(
                'name' => '3U8-VIP',
                'url' => 'https://www.playm3u8.cn/jiexi.php?url='
            ),
            array(
                'name' => 'TXNQ解析',
                'url' => 'https://bfq.txnp.cn/player?url='
            ),
            array(
                'name' => '冰豆解析',
                'url' => 'https://bd.jx.cn/?url='
            ),
            array(
                'name' => '789解析',
                'url' => 'https://jiexi.789jiexi.com/?url='
            ),
            array(
                'name' => '极速解析',
                'url' => 'https://jx.2s0.cn/player/?url='
            ),
            array(
                'name' => 'XYFLV解析',
                'url' => 'https://jx.xyflv.cc/?url='
            ),
        ),    // API解析接口(结束)
    
);