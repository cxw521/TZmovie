<?php
require_once('init.php');

ui_head('VIP视屏破解 - VIP解析', '本站为广大网友提供优酷VIP解析，爱奇艺VIP解析，腾讯VIP解析，乐视VIP解析，芒果VIP解析等解析服务，让你省去购买视频VIP费用，欢迎大家收藏本站，并将它介绍给您的朋友！', 'VIP,爱奇艺VIP,腾讯视屏VIP');

ui_topNav();

?>

<style>
.btn-play-source {
    margin: 0 5px 5px 0;
}
</style>

<div class="am-container">

<div class="am-input-group am-input-group-primary am-margin-bottom">
    <input id="video-url" type="text" class="am-form-field" placeholder="粘贴原视屏播放链接..." required>
    
    <span class="am-input-group-btn">
        <button id="btn-vip-play" class="am-btn am-btn-primary" type="submit">
            <span class="am-icon-play-circle"></span> 破解播放
        </button>
    </span>
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">VIP视屏破解</div>
    
    <div class="am-panel-bd player-box">
        <iframe id="mk-vip-player" src="" allowtransparency="true" scrolling="No" width="100%" height="100%"></iframe>
    </div>
    
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">解析接口</div>
    <div class="am-panel-bd">
        如遇播放失败请尝试切换解析接口
        
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

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">使用说明</div>
    
    <div class="am-panel-bd">
        本工具能破解并播放各大视频网站的 VIP 视频，包括但不限于 爱奇艺、腾讯视屏、优酷、乐视、芒果TV、搜狐、1905。使用方法：
        
        <ol>
            <li>进入各大视频网站，找到想要观看的VIP视频，然后复制链接（浏览器上的视频地址）</li>
            <li>将复制的链接粘贴到顶部的输入框，并点击“破解播放”</li>
            <li>等待解析完成，即可免费观看VIP视频</li>
        </ol>
        
        <span class="am-text-warning">免责声明：本站不生产解析接口，本站只做解析接口的搬运工！</span>
    </div>
    
</div>

<script>
var store;

mkSiteInfo.videoApi = "<?php echo C('videoapi')[0]['url']; ?>";

$(function() {
    store = $.AMUI.store;
    
    // 显示操作演示 GIF
    var playerHtml = '<style type="text/css">* {margin: 0; padding: 0;}html, body {width: 100%; height: 100%;}body{display: table;}.img{text-align: center; vertical-align: middle; display: table-cell;}img{width: 100%; max-width: 500px;}</style>';
    playerHtml += '<div class="img"><img src="https://ws1.sinaimg.cn/large/a15b4afegy1fhduiymjdig20rs0fo0yc.gif"></div>';
    
    $("#mk-vip-player").contents().find("body").html(playerHtml);
    
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
        
    }
    
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
        layer.msg("切换接口为 " + $(this).data("name"));
    });
    
    $("#btn-vip-play").click(function() {
        refreshVideo();
    });
});

// 刷新视屏播放
function refreshVideo() {
    var videoUrl = $("#video-url").val();
    
    if(videoUrl == "") {
        layer.msg("链接不能为空！");
        return false;
    }
    
    $("#mk-vip-player").attr("src", mkSiteInfo.videoApi + videoUrl);
    
    // 记录用户数据
    if (store.enabled) {
        store.set("videoApi", mkSiteInfo.videoApi);    // 记录所用的api接口
    }
}
</script>

</div>  <!-- 容器 -->

<?php

ui_footer();