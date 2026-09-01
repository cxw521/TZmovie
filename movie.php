<?php
require_once('init.php');

ui_head('电影分类列表');

ui_topNav();

$cat = htmlspecialchars(getParam('cat'));    // 类型（数字，转为中文）
$area = htmlspecialchars(getParam('area'));    // 地区（数字，转为中文）
$year = htmlspecialchars(getParam('year'));    // 年代
$act = htmlspecialchars(getParam('act'));    // 主演

$pageno = intval(getParam('pageno', '1'));    // 页码
if($pageno == '') $pageno = '1';

// 360kan 官方 JSON API：数字筛选值转中文
$listData = filterList(1, array(
    'cat' => cnFilter('movie', 'cat', $cat),
    'area' => cnFilter('movie', 'area', $area),
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
            <option value="103">喜剧</option>
            <option value="100">爱情</option>
            <option value="106">动作</option>
            <option value="102">恐怖</option>
            <option value="104">科幻</option>
            <option value="112">剧情</option>
            <option value="105">犯罪</option>
            <option value="113">奇幻</option>
            <option value="108">战争</option>
            <option value="115">悬疑</option>
            <option value="107">动画</option>
            <option value="117">文艺</option>
            <option value="101">伦理</option>
            <option value="118">纪录</option>
            <option value="119">传记</option>
            <option value="120">歌舞</option>
            <option value="121">古装</option>
            <option value="122">历史</option>
            <option value="123">惊悚</option>
            <option value="other">其他</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
    
        <select placeholder="地区" name="area" data-am-selected="{btnWidth: '100%', maxHeight: 225}" 
         class="filter-change-listen" id="movie-area">
            <option selected value=""></option>
            <option value="all">全部</option>
            <option value="11">美国</option>
            <option value="10">大陆</option>
            <option value="15">香港</option>
            <option value="13">韩国</option>
            <option value="14">日本</option>
            <option value="12">法国</option>
            <option value="16">英国</option>
            <option value="17">德国</option>
            <option value="18">台湾</option>
            <option value="21">泰国</option>
            <option value="22">印度</option>
            <option value="other">其他</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
        
        <div class="am-input-group">
            <input type="text" class="am-form-field" id="movie-year-select" 
             data-am-datepicker="{format: 'yyyy ', viewMode: 'years', minViewMode: 'years'}" value="<?php echo $year; ?>" 
             placeholder="年份" data-am-datepicker readonly>
            
            <input type="text" name="year" class="am-hide filter-change-listen" id="movie-year" value="<?php echo $year; ?>" readonly hidden="hidden">
            
            <span class="am-input-group-btn">
                <button class="am-btn am-btn-default" type="button" id="movie-year-clear" title="清除年份设置">
                    ×
                    <!--<i class="am-icon-remove"></i>-->
                </button>
            </span>
        </div>
        
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
            $tmpArr['url'] = 'player.php?mid='.$movies[$j]['id'];
            $tmpArr['cover'] = isset($movies[$j]['cdncover']) ? $movies[$j]['cdncover'] : $movies[$j]['cover'];
            $tmpArr['name'] = $movies[$j]['title'];
            $tmpArr['name2'] = isset($movies[$j]['moviecategory'][0]) ? $movies[$j]['moviecategory'][0] : '';
            if(isset($movies[$j]['score']) && $movies[$j]['score']) {
                $tmpArr['line1'] = '评分：'.$movies[$j]['score'];
            } else {
                $tmpArr['line1'] = '暂无评分';
            }
            $tmpArr['line2'] = '年代：'.(isset($movies[$j]['pubdate']) ? substr($movies[$j]['pubdate'],0,4) : '未知');
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        if($movieCount == 0) {
            echo '
            <div class="am-alert am-alert-warning am-margin-top-sm am-margin-bottom-xl" data-am-alert>
                没找到符合条件的电影，请尝试其他分类！
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
    
    // 删除年份
    $("#movie-year-clear").click(function() {
        $("#movie-year").val('');
        $("#filter-form").submit();
    });
    
    // 监听筛选表单变化
    $(".filter-change-listen").change(function() {
        $("#filter-form").submit();
    });
    
    
    
    var nowTemp = new Date();
    var nowYear = new Date(nowTemp.getFullYear() + 1, 0, 1, 0, 0, 0, 0).valueOf();
    var $myStart2 = $('#movie-year-select');
    
    
    var checkin = $myStart2.datepicker({
        // linkField: 'movie-year',
        // linkFormat: 'yyyy ',
        onRender: function(date, viewMode) {
            // 默认 days 视图，与当前日期比较
            var viewDate = nowYear;
            
            switch (viewMode) {
                // years 视图，与当前年份比较
                case 2:
                    viewDate = nowYear;
                break;
            }
            
            return date.valueOf() > viewDate - 1 ? 'am-disabled' : '';
        }
    }).on('changeDate.datepicker.amui', function(ev) {
        checkin.close();
        $("#movie-year").val(ev.date.getYear() + 1900);
        $("#filter-form").submit();
    }).data('amui.datepicker');
    
});
</script>


<?php


ui_footer();