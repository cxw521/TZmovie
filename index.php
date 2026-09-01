<?php
require_once('init.php');

ui_head(C('name'));

ui_topNav();

// 首页数据（360kan 官方 JSON API）
$homeBanner = homeBanner();                                  // 首页轮播 banner
$homeMovie = filterList(1, array('size' => 10));             // 电影
$homeTv = filterList(2, array('size' => 10));                // 电视剧
$homeVa = filterList(3, array('size' => 10));                // 综艺
$homeCt = filterList(4, array('size' => 10));                // 动漫
$rankMovie = rankList(2);                                    // 热播电影榜
$rankTv = rankList(3);                                       // 热播电视剧榜

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
        $bannerList = $homeBanner;
        
        for($i=0; $i<count($bannerList); $i++) {
            $bImg = isset($bannerList[$i]['img']) ? $bannerList[$i]['img'] : '';
            $bName = isset($bannerList[$i]['name']) ? $bannerList[$i]['name'] : '';
            $bDesc = isset($bannerList[$i]['description']) ? $bannerList[$i]['description'] : '';
            $bUrl = isset($bannerList[$i]['url']) ? $bannerList[$i]['url'] : 'javascript:;';
            if($bImg !== '' && strpos($bImg, '//') === 0) $bImg = 'https:' . $bImg;
            if($bUrl !== 'javascript:;' && strpos($bUrl, '//') === 0) $bUrl = 'https:' . $bUrl;
        echo '
        <li>
            <img src="'.$bImg.'">
            <div class="am-slider-desc">
                <div class="am-slider-content">
                    <h2 class="am-slider-title">'.$bName.'</h2>
                    <p>'.$bDesc.'</p>
                </div>
                <a href="'.$bUrl.'" target="_blank" class="am-slider-more">立即观看</a>
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

    <ul class="am-avg-sm-2 am-avg-md-4 am-avg-lg-5 am-thumbnails movie-lists">
        <?php 
        foreach($homeMovie['movies'] as $j => $movie) {
            $tmpArr['url'] = 'player.php?mid='.$movie['id'];
            $tmpArr['cover'] = isset($movie['cdncover']) ? $movie['cdncover'] : $movie['cover'];
            $tmpArr['name'] = $movie['title'];
            $tmpArr['name2'] = isset($movie['moviecategory'][0]) ? $movie['moviecategory'][0] : '';
            if(isset($movie['score']) && $movie['score']) {
                $tmpArr['line1'] = '评分：'.$movie['score'];
            } else {
                $tmpArr['line1'] = '暂无评分';
            }
            $tmpArr['line2'] = '年代：'.(isset($movie['pubdate']) ? substr($movie['pubdate'],0,4) : '未知');
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
        foreach($homeTv['movies'] as $j => $movie) {
            $tmpArr['url'] = 'player.php?tvid='.$movie['id'];
            $tmpArr['cover'] = isset($movie['cdncover']) ? $movie['cdncover'] : $movie['cover'];
            $tmpArr['name'] = $movie['title'];
            $tmpArr['name2'] = isset($movie['moviecategory'][0]) ? $movie['moviecategory'][0] : '';
            $tmpArr['line1'] = isset($movie['upinfo']) ? '更新至'.$movie['upinfo'].'集' : '全剧';
            if(isset($movie['score']) && $movie['score']) {
                $tmpArr['line2'] = '评分：'.$movie['score'];
            } else {
                $tmpArr['line2'] = '暂无评分';
            }
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        ?>
    </ul>

    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default">
        <h2 class="am-titlebar-title ">
            动漫
        </h2>
    </div>

    <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-5 am-thumbnails ct-lists">
        <?php 
        foreach($homeCt['movies'] as $j => $movie) {
            $tmpArr['url'] = 'player.php?ctid='.$movie['id'];
            $tmpArr['cover'] = isset($movie['cdncover']) ? $movie['cdncover'] : $movie['cover'];
            $tmpArr['name'] = $movie['title'];
            $tmpArr['name2'] = isset($movie['moviecategory'][0]) ? $movie['moviecategory'][0] : '';
            $tmpArr['line1'] = isset($movie['upinfo']) ? '更新至'.$movie['upinfo'].'集' : '全剧';
            if(isset($movie['score']) && $movie['score']) {
                $tmpArr['line2'] = '评分：'.$movie['score'];
            } else {
                $tmpArr['line2'] = '暂无评分';
            }
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        ?>
    </ul>

    <div data-am-widget="titlebar" class="am-titlebar am-titlebar-default">
        <h2 class="am-titlebar-title ">
            综艺
        </h2>
    </div>

    <ul class="am-avg-sm-2 am-avg-md-3 am-avg-lg-5 am-thumbnails va-lists">
        <?php 
        foreach($homeVa['movies'] as $j => $movie) {
            $tmpArr['url'] = 'player.php?vaid='.$movie['id'];
            $tmpArr['cover'] = isset($movie['cdncover']) ? $movie['cdncover'] : $movie['cover'];
            $tmpArr['name'] = $movie['title'];
            $tmpArr['name2'] = isset($movie['moviecategory'][0]) ? $movie['moviecategory'][0] : '';
            $tmpArr['line1'] = isset($movie['upinfo']) ? '更新至'.$movie['upinfo'].'期' : '';
            if(isset($movie['score']) && $movie['score']) {
                $tmpArr['line2'] = '评分：'.$movie['score'];
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
        foreach($rankMovie as $j => $item) {
            $rank = $j + 1;
            echo '
        <li>
            <a href="'.C('siteurl').'/player.php?mid='.$item['ent_id'].'" target="_blank" class="am-text-truncate">
                <span class="r-l-right">'.(isset($item['pv']) ? $item['pv'].'次播放' : '').'</span>
                <span class="am-badge am-round'.($rank<=3?' am-badge-danger':'').'">'.$rank.'</span>
                '.$item['title'].'
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
        foreach($rankTv as $j => $item) {
            $rank = $j + 1;
            echo '
        <li>
            <a href="'.C('siteurl').'/player.php?tvid='.$item['ent_id'].'" target="_blank" class="am-text-truncate">
                <span class="r-l-right">'.(isset($item['pv']) ? $item['pv'].'次播放' : '').'</span>
                <span class="am-badge am-round'.($rank<=3?' am-badge-danger':'').'">'.$rank.'</span>
                '.$item['title'].'
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
        <a href="https://www.taoziwei.com" target="_blank">桃子味🍑博客</a>
        <a href="http://tool.taoziwei.com/" target="_blank">桃子味工具箱</a>
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