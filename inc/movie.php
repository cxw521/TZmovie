<?php

$url = 'https://www.360kan.com/m/'.$tvID.'.html';

$content = curl($url);

// 匹配主体
preg_match('/<div class="title-left g-clear">(.*)<div class="p-body-right">/sU', $content, $temp);
if(!isset($temp[1])) die404('404');

$content = $temp[1];
 
// 名字
preg_match('/<h1>(.*)<\/h1>/sU', $content, $temp);
$movieInfo['name'] = $temp[1];

/*
// 评分
preg_match('/<span class="s">(.*)<\/span>  /sU', $content, $temp);
$movieInfo['score'] = $temp[1];

// 年代
preg_match('/年代：<\/span>(.*)<\/p>/sU', $content, $temp);
$movieInfo['years'] = $temp[1];

// 地区
preg_match('/地区：<\/span>(.*)<\/p>/sU', $content, $temp);
$movieInfo['area'] = $temp[1];

// 类型
preg_match('/<p class="tag">(.*)<\/p>/sU', $content, $temp);
$movieInfo['tag'] = $temp[1];

// 演员
preg_match('/<span>演员：<\/span>(.*)<\/p>/sU', $content, $temp);
preg_match_all('/<a class="name" href="(.*)">(.*)<\/a>/sU', $temp[1], $temp);
for($j=0; $j<count($temp[0]); $j++) {
    $movieInfo['actor'][] = array('name' => $temp[2][$j]);
}


// 导演
preg_match('/<span>导演：<\/span>(.*)<\/p>/sU', $content, $temp);
preg_match_all('/<a class="name" href="(.*)">(.*)<\/a>/sU', $temp[1], $temp);
for($j=0; $j<count($temp[0]); $j++) {
    $movieInfo['director'][] = array('name' => $temp[2][$j]);
}

*/

// 描述
preg_match('/style="display:none;"><span>简介：<\/span>(.*)<a href="#" class="js-close btn">收起/sU', $content, $temp);
$movieInfo['description'] = $temp[1];

// 链接
preg_match_all('/data-daochu="to=\w*" class="btn js-site ea-site ea-site-\w*" href="(.*)">(.*)<\/a>/sU', $content, $temp);
for($j=0; $j<count($temp[0]); $j++) { 
    $temp_urls = str_replace("http://cps.youku.com/redirect.html?id=0000028f&url=", "", $temp[1][$j]);
    // $temp_urls = str_replace("?ptag=360kan.movie.pay", "", $temp_urls);
    // $temp_urls = str_replace("&tpa=dW5pb25faWQ9MTAyMjEzXzEwMDAwN18wMV8wMQ", "", $temp_urls);
    // $temp_urls = str_replace("?ch=360_ffdy", "", $temp_urls);
    // $temp_urls = str_replace("&from=360sousuo&refer=360sousuo", "", $temp_urls);
    // $temp_urls = str_replace("?vfm=f_191_360y", "", $temp_urls);    // 爱奇艺
    $movieInfo['urls'][] = array('from' => $temp[2][$j],
                              'url' => $temp_urls);
}

// 输出网页头文件
ui_head(
    $movieInfo['name'].' - 高清电影在线观看',         // 标题
    htmlspecialchars(mb_substr($movieInfo['description'], 0, 150, 'utf-8')),    // 描述
    $movieInfo['name'].',电影,在线观看,迅雷下载,快播,bt,无广告,免VIP'  // 关键字
);

// 输出顶部导航栏
ui_topNav();
?>

<style>
/* 资源切换按钮 */
.play-source-group {
    margin: 0 5px 5px 0;
}
/* 源站播放按钮 */
.play-source-group .am-dropdown-toggle {
    padding: .5em .6em;
    border-left: 1px solid #c7c6c6;
}

/* 下侧推荐列表 */
.tuijian-item {
    max-width: 160px;
    margin: 0 auto;
}
.tuijian-item img {
    max-height: 200px!important;
}
</style>

<div class="am-container">

<div class="am-panel am-panel-default">
    <div class="am-panel-hd"><?php echo $movieInfo['name']; ?></div>
    
    <?php if(!isset($movieInfo['urls'])) { ?>
    
    <div class="am-alert am-alert-warning" data-am-alert>
        <span class="am-icon-chain-broken am-icon-lg">&nbsp;</span> 
        Sorry! 该片暂无资源. <a href="https://www.baidu.com/s?ie=utf-8&wd=《<?php echo $movieInfo['name']; ?>》" target="_blank">[全网搜索]</a>
    </div>
    
    <?php } else { ?>
    
    <div class="am-panel-bd player-box">
        <iframe id="mk-vip-player" width="100%" height="100%"></iframe>
    </div>
    
    <?php } ?>
</div>

<?php if(isset($movieInfo['urls'])) { ?>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">播放源</div>
    <div class="am-panel-bd">
        
        <?php 
        for($j=0; $j<count($movieInfo['urls']); $j++) { 
            echo '
            <div class="am-btn-group play-source-group">
                <button type="button" class="am-btn am-btn-sm btn-play-source" 
                    data-url="'.$movieInfo['urls'][$j]['url'].'">
                    '.str_replace('(付费)', '(破解)', $movieInfo['urls'][$j]['from']).'
                </button>
                <div class="am-dropdown am-dropdown-up" data-am-dropdown>
                    <button class="am-btn am-btn-sm am-dropdown-toggle" data-am-dropdown-toggle> <span class="am-icon-caret-up"></span></button>
                    <ul class="am-dropdown-content">
                        <li><a href="'.$movieInfo['urls'][$j]['url'].'" target="_blank">去源站播放</a></li>
                    </ul>
                </div>
            </div>
            ';
        }
        ?>
        
        <br>* 如遇播放失败请尝试切换播放源或
        
        <div class="am-btn-group">
            <div class="am-dropdown am-dropdown-up" id="video-api-select" data-am-dropdown>
                <button class="am-btn am-btn-default am-btn-sm am-dropdown-toggle" data-am-dropdown-toggle>
                    <span id="apiname">切换解析接口</span> 
                    <span class="am-icon-caret-down am-margin-left-xs"></span>
                </button>
                
                <ul class="am-dropdown-content" id="videoapi">
                    <li class="am-dropdown-header">切换视屏解析接口</li>
                    
                    <?php 
                        $jk_count = count(C('videoapi'));
                        for($i=0; $i<$jk_count; $i++) {
                            echo '
                    <li class="videoapi-item" data-url="'.C('videoapi')[$i]['url'].'" 
                      data-name="'.C('videoapi')[$i]['name'].'">
                        <a href="javascript:;">'.C('videoapi')[$i]['name'].'</a>
                    </li>';
                        }
                    ?>
                    
                </ul>
            </div>
        </div>
        
    </div>
</div>

<script>
var store;

mkSiteInfo.videoApi = "<?php echo C('videoapi')[0]['url']; ?>";

var videoInfo = {
    id: "<?php echo $tvID;?>",
    url: "<?php echo $movieInfo['urls'][0]['url'];?>",
    name: "<?php echo $movieInfo['name'];?>"
}

$(function() {
    store = $.AMUI.store;
    
    // 获取存储在本地的个性设置
    if (store.enabled) {
        mkSiteInfo.videoApi = store.get('videoApi')? store.get('videoApi'): mkSiteInfo.videoApi;
        
        // 找到并高亮所用解析接口
        $(".videoapi-item").each(function () {
            if($(this).data("url") == mkSiteInfo.videoApi) {
                $(this).addClass("am-active");
                return false;
            }
        });
        
        // 找到历史播放记录并处理
        var histemp = store.get('history')? store.get('history'): [];
        
        for(var i=0; i<histemp.length; i++) {
            if(histemp[i].types == "movie" && histemp[i].id == videoInfo.id) {
                videoInfo.url = histemp[i].url;  // 使用之前的同一个播放源
                break;
            }
        }
    }
    
    // 找到并高亮所用播放源
    $(".btn-play-source").each(function () {
        if($(this).data("url") == videoInfo.url) {
            $(this).addClass("am-btn-secondary");
            return false;    // 退出each
        }
    });
    
    // 切换播放源
    $(".btn-play-source").click(function() {
        $(".btn-play-source").removeClass("am-btn-secondary");
        $(this).addClass("am-btn-secondary");
        
        // 记录播放源
        videoInfo.url = $(this).data("url");
        
        // 更新视屏播放
        refreshVideo();
        
        layer.msg("已切换到 " + $(this).html() + " 线路");
    });
    
    // 切换解析接口
    $(".videoapi-item").click(function() {
        $("#videoapi .am-active").removeClass("am-active");
        $(this).addClass("am-active");
        
        // 记录接口地址
        mkSiteInfo.videoApi = $(this).data("url");
        
        // 关闭下拉
        $("#video-api-select").dropdown("close");
        
        // 更新视屏播放
        refreshVideo();
        
        // 改变显示的接口名
        // $("#apiname").html($(this).data("name"));
        layer.msg("切换接口为 " + $(this).data("name"));
    });
    
    // 启动播放
    refreshVideo();
});

// 刷新视屏播放
function refreshVideo() {
    $("#mk-vip-player").attr("src", mkSiteInfo.videoApi + videoInfo.url);
    
    // 记录用户数据
    if (store.enabled) {
        store.set("videoApi", mkSiteInfo.videoApi);    // 记录所用的api接口
        var temp = {types: "movie", 
                    id: videoInfo.id, 
                    name: videoInfo.name,
                    url: videoInfo.url};
        
        // 找到历史播放记录并删除
        var histemp = store.get('history')? store.get('history'): [];
        
        for(var i=0; i<histemp.length; i++) {
            if(histemp[i].types == "movie" && histemp[i].id == videoInfo.id) {
                histemp.splice(i, 1); // 删除之前的历史记录
                break;
            }
        }
        
        // 添加到历史记录最开始
        histemp.unshift(temp);
        
        // 限定播放历史最多记录6条
        if(histemp.length > 6) histemp.length = 6; 
        
        store.set('history', histemp);
    }
}
</script>

<?php } ?>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">电影简介</div>
    <div class="am-panel-bd">
        
        <?php echo str_replace("\n", '<br>', $movieInfo['description']); ?>
        
    </div>
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">相关推荐</div>
    <div class="am-panel-bd">
        
        <ul data-am-widget="gallery" class="am-gallery am-avg-sm-2 am-avg-md-3 am-avg-lg-5 am-gallery-bordered tuijian-list">
            
            <?php 
            // 推荐
            preg_match('/<span class="p-mod-label">猜你喜欢<\/span>(.*)<ul class="tuijian-list w-newfigure-list g-clear">(.*)<\/ul>/sU', $content, $temp);
            if(isset($temp[2])) {
                preg_match_all('/<a href=\'\/m\/(\w*).html\'  class=\'js-link\'><div class=\'w-newfigure-imglink g-playicon js-playicon\'> <img src=\'(.*)\' data-src=\'(.*)\' alt=\'(.*)\'  \/><\/div><div class=\'w-newfigure-detail\'><p class=\'title g-clear\'><span class=\'s1\'>(.*)<\/span><span class=\'s2\'>(.*)<\/span>/sU', $temp[2], $temp);
                for($j=0; $j<count($temp[0]); $j++) { 
                    echo '
                    <li>
                        <div class="am-gallery-item tuijian-item">
                            <a href="player.php?mid='.$temp[1][$j].'">
                                <img data-original="'.$temp[3][$j].'" alt="'.$temp[4][$j].'" src="assets/i/lazy.gif" class="lazyload">
                                <h3 class="am-gallery-title">
                                    '.$temp[4][$j].' 
                                    <span class="am-gallery-desc">'.$temp[6][$j].'分</span>
                                </h3>
                            </a>
                        </div>
                    </li>
                    ';
                }
            }
            
            if(!isset($temp[2]) || count($temp[0]) == 0) {
                echo '
                    <div class="am-alert am-alert-secondary" data-am-alert>
                        暂无相关推荐
                    </div>';
            }
            ?>
            
        </ul>
        
    </div>
</div>

</div>  <!-- 容器 -->

