<?php
// 网站配置文件
if(!defined('movie')) die('非法访问 - Insufficient Permissions');


// 网站的一些设置
$webConfig = array(
    'siteurl' => 'http://tool.mkblog.cn/movie',    // 网站网址
    
    'name' => 'XX影院',    // 网站名称
    
    'slogan' => '无广告在线看电影',    // 网站口号
    
    // 首页轮播图
    'slider' => array(
            array(
                'img' => 'https://ws1.sinaimg.cn/large/a15b4afegy1fhxhdbued4j20rs0bwdwo.jpg',   // 图片
                'name' => '鬼吹灯之黄皮子坟',     // 名字
                'description' => '正在热播，每周五更新',    // 描述
                'url' => 'player.php?tvid=PrJoc07kTzbpN3'   // 链接
            ),
            array(
                'img' => 'https://ws1.sinaimg.cn/large/a15b4afegy1fm78cm313pj20rs0bw7e0.jpg',   // 图片
                'name' => '人民的名义',     // 名字
                'description' => '"互联网时代最具影响力影视作品"奖',    // 描述
                'url' => 'player.php?tvid=Q4Rrc07kTzDrM3'   // 链接
            ),
            array(
                'img' => 'https://ws1.sinaimg.cn/large/a15b4afegy1fm78dbwo8uj20rs0bwq58.jpg',   // 图片
                'name' => '拆弹专家',     // 名字
                'description' => '2017年大陆 香港动作大片',    // 描述
                'url' => 'player.php?mid=gajmZRH4QHT1TB'   // 链接
            ),
        ),    // 首页轮播图结束
    
    /*
    * API解析接口配置
    * 找最新有效的视频解析接口可以来这里：http://tool.mkblog.cn/movie/share.php
    */ 
    'videoapi' => array(
            array(
                'name' => '接口一(默认)',
                'url' => 'https://api.47ks.com/webcloud/?v='
            ),
            array(
                'name' => '接口二',
                'url' => 'http://www.82190555.com/index/qqvod.php?url='
            ),
            array(
                'name' => '接口三',
                'url' => 'http://www.52jiexi.com/tong.php?url='
            ),
            array(
                'name' => '爱奇艺超清',
                'url' => 'http://api.taoge.la/jiexi/index.php?url='
            ),
            array(
                'name' => '接口四',
                'url' => 'http://v.72du.com/api/?url='
            ),
            array(
                'name' => '通用非VIP',
                'url' => 'https://api.flvsp.com/?url='
            ),
            array(
                'name' => '旋风动漫',
                'url' => 'http://api.xfsub.com/index.php?url='
            ),
            array(
                'name' => '接口八(腾讯)',
                'url' => 'http://api.baiyug.cn/vip/?url='
            ),
            array(
                'name' => '平民解析',
                'url' => 'https://jx.chuaien.com/mdparse/index.php?id='
            ),
            array(
                'name' => '通用普清',
                'url' => 'http://jiexi.92fz.cn/player/vip.php?url='
            ),
        ),    // API解析接口(结束)
    
);