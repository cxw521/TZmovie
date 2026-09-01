<?php
require_once('init.php');

$wd = htmlspecialchars(getParam('wd'));


ui_head($wd.' - 搜索结果');

?>

<style>
.video-score {
    position: absolute;
    right: 10px;
    top: 0;
    font-size: 16px;
    color: #f72;
}
.video-score:first-letter {
    font-size: 20px;
}
/* 封面左侧边距 */
.am-list-news-default .am-list .am-list-item-thumb-left .am-list-thumb {
    padding-left: 10px;
}
/* 图片尺寸，居中 */
.am-list-news-default .am-list .am-list-thumb img {
    max-width: 200px;
    margin: 0 auto;
}
/* 文字右侧边距 */
.am-list-news-default .am-list .am-list-item-text {
    -webkit-line-clamp: 10; 
    max-height: none; 
    line-height: 1.6em;
    margin-right: 10px;
}
/* 电影标题 */
.am-list-item-hd a {
    font-size: 150%;
}
@media only screen and (max-width: 640px) {
    /* 小屏图片展示完全 */
    .am-list-news-default .am-list-item-thumb-left .am-list-thumb {
        max-height: none;
    }
    /* 右侧描述 */
    .am-list-news-default .am-list .am-list-item-text {
        -webkit-line-clamp: 3; 
        max-height: 3.9em; 
        line-height: 1.3em;
    }
    /* 电影标题 */
    .am-list-item-hd a {
        font-size: 100%;
    }
}

/* 按钮 */
.am-list .am-list-item-desced .am-btn, .am-list .am-list-item-thumbed .am-btn {
    margin-top: 10px;
    padding: .5em 1em;
}
</style>

<?php

ui_topNav();

$content = curl('https://api.so.360kan.com/index?kw='.urlencode($wd));

$searchData = json_decode($content, true);
$rows = array();
if(isset($searchData['data']['longData']['rows'])) {
    $rows = $searchData['data']['longData']['rows'];
}
$movies['counts'] = count($rows);

?>

<div class="am-container">

    <div data-am-widget="list_news" class="am-list-news am-list-news-default" >
    
    <!-- 标题栏 -->
    
    <p>共找到 <?php echo $movies['counts']; ?> 个相关结果</p>
    
    <div class="am-list-news-bd">
    <ul class="am-list">
    
    <!-- 搜索结果列表 -->
<?php
for ($i=0; $i< $movies['counts']; $i++) {
    $row = $rows[$i];
    
    // 播放ID
    $tmpArr['url'] = '';
    switch($row['cat_id']) {
        case 1:     // 电影
            $tmpArr['url'] = 'player.php?mid='.$row['en_id'];
            $tmpArr['types'] = '电影';
        break;
        
        case 2:     // 电视剧
            $tmpArr['url'] = 'player.php?tvid='.$row['en_id'];
            $tmpArr['types'] = '电视剧';
        break;
        
        case 3:     // 综艺
            $tmpArr['url'] = 'player.php?vaid='.$row['en_id'];
            $tmpArr['types'] = '综艺';
        break;
        
        case 4:     // 动漫
            $tmpArr['url'] = 'player.php?ctid='.$row['en_id'];
            $tmpArr['types'] = '动漫';
        break;
        
        default:
            $tmpArr['types'] = isset($row['cat_name']) ? $row['cat_name'] : '未知';
        break;
    }
    
    // 名字
    $tmpArr['name'] = strip_tags($row['title']);
    
    // 评分
    $tmpArr['score'] = isset($row['score']) && $row['score'] ? $row['score'] : '';
    
    // 封面图
    $tmpArr['cover'] = isset($row['cover']) ? $row['cover'] : '';
    
    // 描述
    $tmpArr['description'] = isset($row['description']) ? str_replace("\n", '<br>', strip_tags($row['description'])) : '';
    
    search_item($tmpArr);
    
    unset($tmpArr);    // 清空无关数组
}

if($movies['counts'] == 0) {
    echo '
    <div class="am-alert am-alert-warning" data-am-alert>
        没找到“'.$wd.'”相关结果，请换个关键词再试
    </div>
    ';
}

?>
    <!-- 搜索结果 结束 -->
    
    </ul>
    </div>
    </div>

</div>  <!-- 容器 -->

<script>
$('.search-item-href').each(function(){
    var href = $(this).attr('href');
    if(href == '') {
        $(this).removeAttr('href');
        $(this).removeAttr('target');
    }
});
$('.search-item-btn').each(function(){
    var href = $(this).attr('href');
    if(href == '') {
        $(this).removeAttr('href');
        $(this).removeAttr('target');
        $(this).attr('disabled', 'disabled');
        $(this).html('无法播放');
        $(this).removeClass('am-btn-secondary');
        $(this).addClass('am-btn-default');
    }
});
</script>

<?php

ui_footer();

function search_item($videoInfo) {
    echo '
        <li class="am-g am-list-item-desced am-list-item-thumbed am-list-item-thumb-left">
            <div class="am-u-sm-4 am-list-thumb">
                <a href="'.$videoInfo['url'].'" target="_blank" class="search-item-href">
                    <img src="assets/i/lazy.gif" data-original="'.$videoInfo['cover'].'" alt="'.$videoInfo['name'].'" class="lazyload">
                </a>
            </div>
            
            <span class="video-score" title="评分">'.$videoInfo['score'].'</span>
            
            <div class="am-u-sm-8 am-list-main">
                <h3 class="am-list-item-hd">
                    <a href="'.$videoInfo['url'].'" target="_blank" class="search-item-href">['.$videoInfo['types'].']'.$videoInfo['name'].'</a>
                </h3>
                
                <div class="am-list-item-text">
                    '.$videoInfo['description'].'
                </div>
                <a href="'.$videoInfo['url'].'" target="_blank" class="am-btn am-btn-secondary am-btn-sm search-item-btn">
                    <i class="am-icon-play"></i>
                    在线播放  
                </a>
            </div>
        </li>
    ';
}