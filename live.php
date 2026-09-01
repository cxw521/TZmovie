<?php
require_once('init.php');

ui_head('电视直播');

ui_topNav();

?>

<style>
.player-box {
    height: 480px;
}
#mk-vip-player {
    width: 100%;
    height: 100%;
    border: none;
    background: #000;
}
.live-groups {
    margin-bottom: 10px;
}
.live-groups .am-btn {
    margin: 0 5px 5px 0;
}
.live-channels {
    max-height: 300px;
    overflow-y: auto;
}
.btn-live-channel {
    margin: 0 5px 5px 0;
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.btn-live-channel.active {
    color: #fff;
    background-color: #0e90d2;
}
</style>

<div class="am-container">

<div class="am-alert am-alert-warning am-show-sm-only" data-am-alert>
    <button type="button" class="am-close">&times;</button>
    <p>电视直播功能建议在电脑上使用</p>
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">电视直播 <span id="live-status" class="am-text-success"></span></div>
    
    <div class="am-panel-bd player-box">
        <video id="mk-vip-player" controls autoplay playsinline></video>
    </div>
    
</div>

<div class="am-panel am-panel-default">
    <div class="am-panel-hd">直播频道</div>
    <div class="am-panel-bd">
        <?php
        $liveGroups = parseLiveChannels(liveSource());
        if(empty($liveGroups)) {
            echo '<div class="am-alert am-alert-danger" data-am-alert>直播源获取失败，请稍后重试</div>';
        } else {
            $liveCount = 0;
            foreach($liveGroups as $g) $liveCount += count($g['channels']);
        ?>
        <div class="live-groups">
            <?php foreach($liveGroups as $i => $g) { ?>
            <button type="button" class="am-btn am-btn-xs am-btn-secondary live-group-btn<?php echo $i == 0 ? ' am-active' : ''; ?>" data-group="<?php echo $i; ?>"><?php echo htmlspecialchars($g['name']); ?></button>
            <?php } ?>
        </div>
        
        <div class="live-channels">
            <?php foreach($liveGroups as $i => $g) { ?>
            <div class="live-channel-group" data-group="<?php echo $i; ?>"<?php echo $i == 0 ? '' : ' style="display:none;"'; ?>>
                <?php foreach($g['channels'] as $j => $ch) { ?>
                <button type="button" class="am-btn am-btn-xs am-btn-default btn-live-channel" data-url="<?php echo htmlspecialchars($ch['url']); ?>"><?php echo htmlspecialchars($ch['name']); ?></button>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        
        <div class="am-text-muted am-margin-top-sm">共 <?php echo $liveCount; ?> 个频道</div>
        <?php } ?>
    </div>
</div>

<script src="assets/js/hls.min.js"></script>
<script>
var store;
var video = document.getElementById('mk-vip-player');
var hls;

function playLive(url) {
    if(hls) { hls.destroy(); hls = null; }
    // 经同源代理播放，解决 m3u8 跨域问题
    var proxied = 'live_proxy.php?u=' + encodeURIComponent(url);
    if(video.canPlayType('application/vnd.apple.mpegurl')) {
        // Safari 原生支持 HLS
        video.src = proxied;
    } else if(Hls.isSupported()) {
        hls = new Hls();
        hls.loadSource(proxied);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
        hls.on(Hls.Events.ERROR, function(e, data) {
            if(data.fatal) {
                document.getElementById('live-status').innerHTML = '<span class="am-text-danger">播放出错，请切换频道或源</span>';
            }
        });
    } else {
        document.getElementById('live-status').innerHTML = '<span class="am-text-danger">当前浏览器不支持 HLS 播放</span>';
        return;
    }
    document.getElementById('live-status').innerHTML = '';
}

$(function() {
    store = $.AMUI.store;
    
    // 切换频道分组
    $(".live-group-btn").click(function() {
        var g = $(this).data("group");
        $(".live-group-btn").removeClass("am-active");
        $(this).addClass("am-active");
        $(".live-channel-group").hide();
        $('.live-channel-group[data-group="' + g + '"]').show();
    });
    
    // 播放频道
    $(".btn-live-channel").click(function() {
        $(".btn-live-channel").removeClass("active");
        $(this).addClass("active");
        playLive($(this).data("url"));
        
        if(store.enabled) {
            store.set('liveChannel', $(this).data("url"));
        }
    });
    
    // 恢复上次播放的频道
    var lastUrl = '';
    if(store.enabled) {
        lastUrl = store.get('liveChannel')? store.get('liveChannel'): '';
    }
    if(lastUrl) {
        var $btn = $('.btn-live-channel[data-url="' + lastUrl + '"]');
        if($btn.length) {
            $btn.addClass("active");
            playLive(lastUrl);
        } else {
            playLive($(".btn-live-channel:eq(0)").data("url"));
        }
    } else {
        playLive($(".btn-live-channel:eq(0)").data("url"));
    }
});
</script>

</div>  <!-- 容器 -->

<?php

ui_footer();
