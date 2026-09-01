<?php

$site = getParam('site');
if($site) {
    // 获取其它播放源的资源
    $content = curl('https://www.360kan.com/cover/switchsite?site='.$site.'&id='.$tvID.'&category=2');
    $content = json_decode($content, true)['data'];
    
    $urls = decodeTVUrl($content);
    echoTVUrls($urls);
    
    die();
}

$url = 'https://www.360kan.com/tv/'.$tvID.'.html';

$content = curl($url);

// 匹配主体
preg_match('/<div class="title-left g-clear">(.*)<\/body>/sU', $content, $temp);
if(!isset($temp[1])) die404('404');

$content = $temp[1];

// 名字
preg_match('/<h1>(.*)<\/h1>/sU', $content, $temp);
$movieInfo['name'] = $temp[1];

// 描述
preg_match('/style="display:none;"><span>简介：<\/span>(.*)<a href="#" class="js-close btn">收起/sU', $content, $temp);
$movieInfo['description'] = $temp[1];

// 链接

$movieInfo['urls'] = decodeTVUrl($content);

// 解码链接
function decodeTVUrl($content) {
    preg_match('/<div class="num-tab-main g-clear js-tab"(.*)<\/div>/sU', $content, $temp);
    preg_match_all('/<a data-num="(\d+)" data-daochu="(.*)" href="(.*)">/sU', $temp[1], $temp);
    for($j=0; $j<count($temp[0]); $j++) { 
        $temp_urls = str_replace("http://cps.youku.com/redirect.html?id=0000028f&url=", "", $temp[3][$j]);
        $tmpArr[] = array('no' => $temp[1][$j],
                                  'url' => $temp_urls);
    }
    return $tmpArr;
}

// 输出电视链接
function echoTVUrls($urls) {
    for($j=0; $j<count($urls); $j++) { 
        echo '
        <button type="button" class="am-btn am-btn-sm btn-play-source" data-url="'.$urls[$j]['url'].'">'.$urls[$j]['no'].'</button>';
    }
}

// 播放源
preg_match('/\',\r\s*playsite:(.*),\r\s*playtype:/sU', $content, $temp);
$movieInfo['playsite'] = json_decode($temp[1], true);

// 输出网页头文件
ui_head(
    $movieInfo['name'].' - 电视剧在线观看',         // 标题
    htmlspecialchars(mb_substr($movieInfo['description'], 0, 150, 'utf-8')),    // 描述
    $movieInfo['name'].',电视剧,在线观看,迅雷下载,快播,bt,无广告,免VIP'  // 关键字
);

// 输出顶部导航栏
ui_topNav();
?>

<style>
/* 上一集，下一集 */
.btn-prev-source, .btn-next-source, .btn-goto-origin, .btn-goto-origin:hover {
    color: #dd514c;
    cursor: pointer;
    margin-left: 10px;
}
.btn-play-source {
    margin: 0 5px 5px 0;
}
/* 下侧推荐列表 */
.tuijian-item {
    max-width: 160px;
    margin: 0 auto;
}
.tuijian-item img {
    max-width: 150px!important;
    max-height: 85px!important;
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
        <iframe id="mk-vip-player" src="" width="100%" height="100%"></iframe>
    </div>
    
    <?php } ?>
</div>

<?php if(isset($movieInfo['urls'])) { ?>
<div class="am-panel am-panel-default">
    <div class="am-panel-hd">
        选集
        <span class="btn-prev-source">[上一集]</span>
        <span class="btn-next-source">[下一集]</span>
        <a class="btn-goto-origin" target="_blank">[源站播放]</a>
    </div>
    <div class="am-panel-bd">
        <div class="am-tabs" id="tv-res-choose" data-am-tabs>
            <ul class="am-tabs-nav am-nav am-nav-tabs">
                <?php 
                echo '
                <li class="am-active" id="tttest">
                    <a href="#'.$movieInfo['playsite'][0]['ensite'].'">'.$movieInfo['playsite'][0]['cnsite'].'</a>
                </li>';
                
                for($i=1; $i < count($movieInfo['playsite']); $i++) { 
                    echo '
                    <li>
                        <a href="#'.$movieInfo['playsite'][$i]['ensite'].'">'.$movieInfo['playsite'][$i]['cnsite'].'</a>
                    </li>';
                }
                ?>
            </ul>
            
            <div class="am-tabs-bd tv-res-lists">
                <?php 
                // 默认的播放源
                echo '<div class="am-tab-panel am-fade am-in am-active" id="'.$movieInfo['playsite'][0]['ensite'].'">';
                
                // 输出全部的剧集
                echoTVUrls($movieInfo['urls']);
                
                echo '</div>';
                
                // 其它播放源
                for($i=1; $i < count($movieInfo['playsite']); $i++) { 
                    echo '<div class="am-tab-panel am-fade tv-res" id="'.$movieInfo['playsite'][$i]['ensite'].'">读取中...</div>';
                }
                ?>
            </div>
        </div>
        
        <br> * 如遇播放失败请尝试
        
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
<?php } ?>

<script>
var store;

mkSiteInfo.videoApi = "<?php echo C('videoapi')[0]['url']; ?>";

var videoInfo = {
    id: "<?php echo $tvID;?>",
    url: "<?php echo $movieInfo['urls'][0]['url'];?>",
    name: "<?php echo $movieInfo['name'];?>",
    episode: "<?php echo $movieInfo['urls'][0]['no'];?>",
    site: "<?php echo $movieInfo['playsite'][0]['ensite'];?>"
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
            if(histemp[i].types == "tv" && histemp[i].id == videoInfo.id) {
                videoInfo.url = histemp[i].url;  // 使用之前的同一个播放源
                videoInfo.episode = histemp[i].episode;  // 记录播放集数
                videoInfo.site = histemp[i].site;  // 资源站点
                
                // 切换到当前播放源的 tab
                $('#tv-res-choose').tabs('open', $('.am-tabs-nav a[href="#' + videoInfo.site + '"]'))
                
                layer.msg("您上次观看到第 " + histemp[i].episode + " 集");
                break;
            }
        }
    }
    
    // 依次加载其它源资源
    $(".tv-res").each(function () {
        $(this).load("?tvid=<?php echo $tvID;?>&site=" + $(this).attr("id"), function() {
            if($(this).attr("id") == videoInfo.site) {
                highlightSource();    // 高亮当前播放的这一集
                refreshToolBtn();    // 刷新上一集、下一集按钮
            }
        });
    });
    
    // 高亮当前播放的这一集
    highlightSource();
    
    // 上一集
    $(".btn-prev-source").click(function() {
        $(".btn-play-source.am-btn-secondary").prev().click();
    });
    
    // 下一集
    $(".btn-next-source").click(function() {
        $(".btn-play-source.am-btn-secondary").next().click();
    });
    
    
    // 切换播放源（剧集）
    $(".tv-res-lists").on("click", ".btn-play-source", function() {
        $(".btn-play-source").removeClass("am-btn-secondary");
        $(this).addClass("am-btn-secondary");
        
        // 记录播放源（剧集）
        videoInfo.url = $(this).data("url");    // 链接
        videoInfo.episode = $(this).html();       // 集数
        videoInfo.site = $(this).parent().attr('id');    // 来源
        
        // 更新视屏播放
        refreshVideo();
        
        layer.msg("正在播放第 " + videoInfo.episode + " 集");
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

// 找到并高亮所用播放源(剧集)
function highlightSource() {
    $(".btn-play-source").each(function () {
        if($(this).data("url") == videoInfo.url) {
            $(this).addClass("am-btn-secondary");
            return false;    // 退出each
        }
    });
}

// 刷新上一集、下一集的按钮
function refreshToolBtn() {
    // 上一集、下一集
    if($(".btn-play-source.am-btn-secondary").prev().length == 0) {
        $(".btn-prev-source").hide();
    } else {
        $(".btn-prev-source").show();
    }
    
    if($(".btn-play-source.am-btn-secondary").next().length == 0) {
        $(".btn-next-source").hide();
    } else {
        $(".btn-next-source").show();
    }
}

// 刷新视屏播放
function refreshVideo() {
    $("#mk-vip-player").attr("src", mkSiteInfo.videoApi + videoInfo.url);
    
    $(".btn-goto-origin").attr("href", videoInfo.url);
    
    refreshToolBtn();
    
    // 记录用户数据
    if (store.enabled) {
        store.set("videoApi", mkSiteInfo.videoApi);    // 记录所用的api接口
        var temp = {
                    types: "tv", 
                    id: videoInfo.id, 
                    name: videoInfo.name,
                    url: videoInfo.url,
                    episode: videoInfo.episode,
                    site: videoInfo.site
                };
        
        // layer.msg(videoInfo.episode)
        
        // 找到历史播放记录并删除
        var histemp = store.get('history')? store.get('history'): [];
        
        for(var i=0; i<histemp.length; i++) {
            if(histemp[i].types == "tv" && histemp[i].id == videoInfo.id) {
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

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">简介</div>
    <div class="am-panel-bd">
        <?php echo $movieInfo['description']; ?>
    </div>
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">相关推荐</div>
    <div class="am-panel-bd">
        
        <ul data-am-widget="gallery" class="am-gallery am-avg-sm-2 am-avg-md-3 am-avg-lg-5 am-gallery-bordered tuijian-list">
            <?php 
            // 推荐
            preg_match('/<span>猜你喜欢<\/span>(.*)<ul class=\'s-guess-list g-clear js-list\'(.*)<\/ul>/sU', $content, $temp);
            if(isset($temp[2])) {
                preg_match_all('/data-src=\'([^\']*)\'>\r\s*<\/a>\r\s*<div class=\'s-guess-right\'>\r\s*<p class=\'title\'><a href=\'\/tv\/(\w*).html\' data-index=(\w*)>([^<]*)<\/a>/sU', $temp[2], $temp);
                for($j=0; $j<count($temp[0]); $j++) { 
                    echo '
                        <li>
                        <div class="am-gallery-item tuijian-item">
                            <a href="player.php?tvid='.$temp[2][$j].'">
                                <img src="assets/i/lazy.gif" data-original="'.$temp[1][$j].'" alt="'.$temp[4][$j].'" class="lazyload">
                                <h3 class="am-gallery-title">'.$temp[4][$j].'</h3>
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

