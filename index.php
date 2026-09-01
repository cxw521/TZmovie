<?php
require_once('init.php');

ui_head(C('name'));

ui_topNav();

$content = curl('https://www.360kan.com/');

?>

<div class="am-container index-container">

<!-- 搜索表单 -->
<form action="<?php echo C('siteurl'); ?>/search.php" class="am-show-sm-only am-margin-bottom">
    <div class="am-input-group am-input-group-primary">
        <input name="wd" type="text" class="am-form-field" placeholder="搜索..." required>
        <span class="am-input-group-btn">
            <button class="am-btn am-btn-primary" type="submit">
                <span class="am-icon-search"></span>
            </button>
        </span>
    </div>
</form>

<div class="am-g">
<div class="am-u-sm-12 am-u-lg-9">

    <!-- 上次播放记录 -->
    <div class="index-last-watch am-alert am-alert-secondary am-margin-bottom" data-am-alert hidden>
        <button type="button" class="am-close">&times;</button>
        <p>继续观看 <span id="last-watch"></span></p>
    </div>

    <!-- 顶部轮播图banner -->
    <div data-am-widget="slider" class="am-slider am-slider-d2" 
      data-am-slider='{"directionNav":false}' >
    <ul class="am-slides">
        <!-- 轮播图开始 -->
        
        <?php 
        $slider_count = count(C('slider'));
        
        for($i=0; $i<$slider_count; $i++) {
        echo '
        <li>
            <img src="'.C('slider')[$i]['img'].'">
            <div class="am-slider-desc">
                <div class="am-slider-content">
                    <h2 class="am-slider-title">'.C('slider')[$i]['name'].'</h2>
                    <p>'.C('slider')[$i]['description'].'</p>
                </div>
                <a href="'.C('slider')[$i]['url'].'" target="_blank" class="am-slider-more">立即观看</a>
            </div>
        </li>';
        }
        ?>
        
        <!--轮播图结束-->
    </ul>
    </div>

    <!-- 影片列表开始 -->
    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default" >
        <h2 class="am-titlebar-title ">
            电影
        </h2>
    </div>

    <ul class="am-avg-sm-3 am-avg-md-4 am-avg-lg-5 am-thumbnails movie-lists">
        <?php 
        // 名字
        preg_match('/<div class="content rmcontent">(.*)<\/ul>/sU', $content, $temp);
        preg_match_all('/<a href=\'http[s]?:\/\/www.360kan.com\/m\/(\w*).html\'  class=\'js-link\'><div class=\'w-newfigure-imglink g-playicon js-playicon\'> <img src=\'([^\']*)\' data-src=\'([^\']*)\' alt=\'([^\']*)\'  \/><span class=\'w-newfigure-hint\'>(\d*)<\/span><\/div><div class=\'w-newfigure-detail\'><p class=\'title g-clear\'><span class=\'s1\'>([^<]*)<\/span>(<span class=\'s2\'>([^<]*)<\/span>)?<\/p><p class=\'w-newfigure-desc\'>([^<]*)<\/p>/sU', $temp[1], $temp);
        for($j=0; $j<count($temp[0]); $j++) {
            $tmpArr['url'] = 'player.php?mid='.$temp[1][$j];
            $tmpArr['cover'] = $temp[3][$j];
            $tmpArr['name'] = $temp[4][$j];
            $tmpArr['name2'] = $temp[9][$j];
            if($temp[8][$j]) {
                $tmpArr['line1'] = '评分：'.$temp[8][$j];
            } else {
                $tmpArr['line1'] = '暂无评分';
            }
            $tmpArr['line2'] = '年代：'.$temp[5][$j];
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        ?>
    </ul>

    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default" >
        <h2 class="am-titlebar-title ">
            电视剧
        </h2>
    </div>

    <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-5 am-thumbnails tv-lists">
        <?php 
        // 名字
        preg_match('/<ul class="list g-clear w-newfigure-list">(.*)<\/ul>/sU', $content, $temp);
        preg_match_all('/<a href=\'http[s]?:\/\/www.360kan.com\/tv\/(\w*).html\'  class=\'js-link\'><div class=\'w-newfigure-imglink g-playicon js-playicon\'> <img src=\'(.*)\' data-src=\'(.*)\' alt=\'(.*)\'  \/><span class=\'w-newfigure-hint\'>(.*)<\/span><\/div><div class=\'w-newfigure-detail\'><p class=\'title g-clear\'><span class=\'s1\'>(.*)<\/span>(<span class=\'s2\'>(.*)<\/span>)?<\/p><p class=\'w-newfigure-desc\'>(.*)<\/p>/sU', $temp[1], $temp);
        for($j=0; $j<count($temp[0]); $j++) {
            $tmpArr['url'] = 'player.php?tvid='.$temp[1][$j];
            $tmpArr['cover'] = $temp[3][$j];
            $tmpArr['name'] = $temp[4][$j];
            $tmpArr['name2'] = $temp[9][$j];
            $tmpArr['line1'] = $temp[5][$j];
            if($temp[8][$j]) {
                $tmpArr['line2'] = '评分：'.$temp[8][$j];
            } else {
                $tmpArr['line2'] = '暂无评分';
            }
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        ?>
    </ul>
</div>
<div class="am-u-sm-12 am-u-lg-3">

    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default" style="margin-top: 0">
        <h2 class="am-titlebar-title ">
            热播电影榜
        </h2>
    </div>

    <ul class="right-list">
        <?php 
        // 获取电影榜
        preg_match('/<span class="p-mod-label">电影榜<\/span>(.*)<\/ul>/sU', $content, $temp);
        
        preg_match_all('/<span class="num( top3)?">(\d+)<\/span>(.*)<a title="(.*)" href="http[s]?:\/\/www.360kan.com\/m\/(\w+).html" class="name">(.*)<\/a>(.*)<span class="vv">(.*)<\/span>/sU', $temp[1], $temp);
        for($j=0; $j<count($temp[0]); $j++) {
            echo '
        <li>
            <a href="'.C('siteurl').'/player.php?mid='.$temp[5][$j].'" target="_blank" class="am-text-truncate">
                <span class="r-l-right">'.$temp[8][$j].'</span>
                <span class="am-badge am-round">'.$temp[2][$j].'</span>
                '.$temp[6][$j].'
            </a>
        </li>
            ';
        }
        
        ?>
    </ul>

    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default" style="margin-top: 0">
        <h2 class="am-titlebar-title ">
            热播电视剧榜
        </h2>
    </div>

    <ul class="right-list">
        <?php 
        // 电视剧榜
        preg_match('/<span class="p-mod-label">电视剧排行榜<\/span>(.*)<\/ul>/sU', $content, $temp);
        
        preg_match_all('/<span class="num( top3)?">(\d+)<\/span>(.*)<a title="(.*)" href="http[s]?:\/\/www.360kan.com\/tv\/(\w+).html" class="name">(.*)<\/a>(.*)<span class="vv">(.*)<\/span>/sU', $temp[1], $temp);
        for($j=0; $j<count($temp[0]); $j++) {
            echo '
        <li>
            <a href="'.C('siteurl').'/player.php?tvid='.$temp[5][$j].'" target="_blank" class="am-text-truncate">
                <span class="r-l-right">'.$temp[8][$j].'</span>
                <span class="am-badge am-round">'.$temp[2][$j].'</span>
                '.$temp[6][$j].'
            </a>
        </li>
            ';
        }
        
        ?>
    </ul>

</div>
</div>

<div class="am-panel am-panel-default friend-links">
    <div class="am-panel-hd">友情链接</div>
    <div class="am-panel-bd">
        <!-- 友情链接 -->
        <a href="https://mkblog.cn" target="_blank">孟坤博客</a>
        <a href="http://tool.mkblog.cn/" target="_blank">孟坤工具箱</a>
    </div>
</div>

</div>  <!-- 容器 -->

<script type="text/javascript">
// 上次播放提示
$(function() {
    var store = $.AMUI.store;
    if (store.enabled) {
        var histemp = store.get('history')? store.get('history'): [];
        
        if(histemp.length !== 0) {
            switch(histemp[0].types) {
                case 'movie':
                    $("#last-watch").html('<a href="player.php?mid='+histemp[0].id+'">《'+histemp[0].name+'》</a>');
                break;
                
                case 'tv':
                    $("#last-watch").html('<a href="player.php?tvid='+histemp[0].id+'">《'+histemp[0].name+' [第'+histemp[0].episode+'集]》</a>');
                break;
                
                case 'ct':
                    $("#last-watch").html('<a href="player.php?tvid='+histemp[0].id+'">《'+histemp[0].name+' [第'+histemp[0].episode+'集]》</a>');
                break;
                
                case 'va':
                    $("#last-watch").html('<a href="player.php?vaid='+histemp[0].id+'">《'+histemp[0].name+' ['+histemp[0].episode+']》</a>');
                break;
            }
            $(".index-last-watch").slideDown();
        }
    }
});
</script>

<?php


ui_footer();