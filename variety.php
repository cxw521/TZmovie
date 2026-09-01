<?php
require_once('init.php');

ui_head('综艺分类列表');

ui_topNav();

$cat = htmlspecialchars(getParam('cat'));    // 类型（数字，转为中文）
$area = htmlspecialchars(getParam('area'));    // 地区（数字，转为中文）
$year = htmlspecialchars(getParam('year'));    // 年代
$act = htmlspecialchars(getParam('act'));    // 主演

$pageno = intval(getParam('pageno', '1'));    // 页码
if($pageno == '') $pageno = '1';

// 360kan 官方 JSON API：数字筛选值转中文
$listData = filterList(3, array(
    'cat' => cnFilter('variety', 'cat', $cat),
    'area' => cnFilter('variety', 'area', $area),
    'year' => $year,
    'act' => $act,
    'pageno' => $pageno,
    'size' => 24
));

$movies = $listData['movies'];
$maxPage = max(1, intval(ceil($listData['total'] / 24)));
?>

<style>
/* 顶部的筛选框 */
.panel-filter {
    padding: 1.25rem 0 0 0;
}

.panel-filter .am-u-md-3 {
    padding-bottom: 1.25rem;
}

.panel-filter .am-btn,.panel-filter input {
    font-size: 14px!important;
    height: 32px!important;
}

#movie-year-select {
    background-color: #fff;
    cursor: pointer;
}

.am-pagination-prev, .am-pagination-next, #selectPage {
    background-color: #fff;
    padding: 5px 10px;
    font-size: 14px;
    line-height: 23px;
    width: auto;
    height: auto;
    color: #444;
    cursor: pointer;
}
#selectPage {
    padding-right: 0;
    -webkit-appearance: menulist;
    -moz-appearance: menulist;
}
.am-pagination-select .am-disabled {
    background-color: #F9F9F9;
    cursor: not-allowed;
}

/* 滑过不要弹起了…… */
.movie-item:hover .movie-description {
    bottom: -51px;
}
</style>

<div class="am-container">

    <form class="am-form" id="filter-form">
    
    <div class="am-panel am-panel-default" style="margin-bottom: 10px">
    <div class="am-panel-bd panel-filter">
    
    <div class="am-g">
    
    <div class="am-u-md-3">
    
        <select placeholder="类型" name="cat" data-am-selected="{btnWidth: '100%', maxHeight: 225}" 
         class="filter-change-listen" id="movie-cat">
            <option selected value=""></option>
            <option value="all">全部</option>
            <option value="101">选秀</option>
            <option value="102">八卦</option>
            <option value="103">访谈</option>
            <option value="104">情感</option>
            <option value="105">生活</option>
            <option value="106">晚会</option>
            <option value="107">搞笑</option>
            <option value="108">音乐</option>
            <option value="109">时尚</option>
            <option value="110">游戏</option>
            <option value="111">少儿</option>
            <option value="112">体育</option>
            <option value="113">纪实</option>
            <option value="114">科教</option>
            <option value="115">曲艺</option>
            <option value="116">歌舞</option>
            <option value="117">财经</option>
            <option value="118">汽车</option>
            <option value="119">播报</option>
            <option value="other">其他</option>
            <option value="120">真人秀</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
    
        <select placeholder="地区" name="area" data-am-selected="{btnWidth: '100%', maxHeight: 225}" 
         class="filter-change-listen" id="movie-area">
            <option selected value=""></option>
            <option value="all">全部</option>
            <option value="10">大陆</option>
            <option value="11">台湾</option>
            <option value="12">韩国</option>
            <option value="13">日本</option>
            <option value="14">欧美</option>
            <option value="15">香港</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
    
        <div class="am-input-group">
            <input type="text" class="am-form-field" name="act" placeholder="主演" id="movie-act">
            <span class="am-input-group-btn">
                <button class="am-btn am-btn-default" type="submit">
                    <i class="am-icon-angle-right"></i>
                </button>
            </span>
        </div>
    
    </div>  <!-- md-3 -->
    
    </div>  <!-- 网格 -->
    
    </div>  <!-- 面板 -->
    </div>  <!-- 面板 -->
    
    <input type="text" name="pageno" id="movie-pageno" class="am-hide">
    
    </form>

    <ul class="am-avg-sm-3 am-avg-md-4 am-avg-lg-6 am-thumbnails movie-lists">
        <?php 
        $movieCount = count($movies);
        
        for($j=0; $j<$movieCount; $j++) {
            $tmpArr['url'] = 'player.php?vaid='.$movies[$j]['id'];
            $tmpArr['cover'] = isset($movies[$j]['cdncover']) ? $movies[$j]['cdncover'] : $movies[$j]['cover'];
            $tmpArr['name'] = $movies[$j]['title'];
            $tmpArr['name2'] = isset($movies[$j]['moviecategory'][0]) ? $movies[$j]['moviecategory'][0] : '';
            $tmpArr['line1'] = isset($movies[$j]['upinfo']) ? '更新至'.$movies[$j]['upinfo'].'期' : '全集';
            $tmpArr['line2'] = '';
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        if($movieCount == 0) {
            echo '
            <div class="am-alert am-alert-warning am-margin-top-sm am-margin-bottom-xl" data-am-alert>
                没找到符合条件的综艺，请尝试其他分类！
            </div>
            ';
        }
        
        ?>
    </ul>
    
    <ul data-am-widget="pagination" class="am-pagination am-pagination-select">
        <li class="am-pagination-prev" id="prevPage">
            上一页
        </li>
        
        <li class="am-pagination-select">
            <select id="selectPage"></select>
        </li>
        
        <li class="am-pagination-next" id="nextPage">
            下一页
        </li>
    
    </ul>
    

</div>  <!-- 容器 -->

<script type="text/javascript">
var pageInfo = {
    act: "<?php echo $act; ?>",
    cat: "<?php echo $cat; ?>",
    area: "<?php echo $area; ?>",
    curPage: <?php echo $pageno; ?>,     // 当前页码
    maxPage: <?php echo $maxPage; ?>    // 最大的页码
}


$(function() {
    $("#movie-cat").val(pageInfo.cat);
    $("#movie-area").val(pageInfo.area);
    $("#movie-act").val(pageInfo.act);
    
    
    // 循环添加页码
    for(var i=1; i<=pageInfo.maxPage; i++) {
        $("#selectPage").append('<option value="'+i+'">第 '+i+' 页</option>');
    }
    $("#selectPage").val(pageInfo.curPage);
    
    // 页码选择器改变自动跳转
    $("#selectPage").change(function(){
        goPage($('#selectPage').val());
    });
    
    // 上下翻页功能
    if(pageInfo.curPage <= 1) {
        $("#prevPage").addClass("am-disabled");
    }
    if(pageInfo.curPage >= pageInfo.maxPage) {
        $("#nextPage").addClass("am-disabled");
    }
    $("#prevPage").click(function() {
        if(pageInfo.curPage > 1) goPage((parseInt(pageInfo.curPage)-1));
    });
    $("#nextPage").click(function() {
        if(pageInfo.curPage < pageInfo.maxPage) goPage((parseInt(pageInfo.curPage)+1));
    });
    
    // 跳转至指定页码
    function goPage(newPage) {
        $("#movie-pageno").val(newPage);
        $("#filter-form").submit();
    }
    
    
    // 监听筛选表单变化
    $(".filter-change-listen").change(function() {
        $("#filter-form").submit();
    });
    
    
});
</script>


<?php


ui_footer();