<?php
require_once('init.php');

ui_head('动漫分类列表');

ui_topNav();

$cat = htmlspecialchars(getParam('cat'));    // 类型
$area = htmlspecialchars(getParam('area'));    // 地区
$year = htmlspecialchars(getParam('year'));    // 年代
// $act = htmlspecialchars(getParam('act'));    // 主演

$pageno = intval(getParam('pageno', '1'));    // 页码
if($pageno == '') $pageno = '1';

// curl 获取内容
$content = curl('https://www.360kan.com/dongman/list.php?cat='.$cat.'&area='.$area.'&year='.$year.'&pageno='.$pageno);

// 获取最大页码
preg_match('/(\d+)<\/a><a href=\'([^\']*)\' target=\'_self\' class=\'btn\'>下一页/sU', $content, $temp);

if(!isset($temp[1])) {
    preg_match('/<a target=\'_self\' class=\'on\'>(\d+)<\/a><\/div>/sU', $content, $temp);
    
}

$maxPage = intval(isset($temp[1])? $temp[1]: 1);
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

#movie-year {
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
            
            <option value="100">热血</option>
            <option value="101">恋爱</option>
            <option value="102">美少女</option>
            <option value="103">运动</option>
            <option value="104">校园</option>
            <option value="105">搞笑</option>
            <option value="106">幻想</option>
            <option value="107">冒险</option>
            <option value="108">悬疑</option>
            <option value="109">魔幻</option>
            <option value="110">动物</option>
            <option value="111">少儿</option>
            <option value="131">亲子</option>
            <option value="112">机战</option>
            <option value="113">怪物</option>
            <option value="114">益智</option>
            <option value="115">战争</option>
            <option value="116">社会</option>
            <option value="117">友情</option>
            <option value="118">成人</option>
            <option value="119">竞技</option>
            <option value="120">耽美</option>
            <option value="121">童话</option>
            <option value="122">LOLI</option>
            <option value="123">青春</option>
            <option value="124">男性向</option>
            <option value="125">女性向</option>
            <option value="126">动作</option>
            <option value="127">真人版</option>
            <option value="128">OVA版</option>
            <option value="129">TV版</option>
            <option value="130">电影版</option>
            <option value="132">新番动画</option>
            <option value="133">完结动画</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
    
        <select placeholder="地区" name="area" data-am-selected="{btnWidth: '100%', maxHeight: 225}" 
         class="filter-change-listen" id="movie-area">
            <option selected value=""></option>
            <option value="all">全部</option>
            
            <option value="11">日本</option>
            <option value="12">美国</option>
            <option value="10">大陆</option>
        </select>
    
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
        
        <div class="am-input-group">
            <input type="text" name="year" class="am-form-field filter-change-listen" id="movie-year" 
             data-am-datepicker="{format: 'yyyy ', viewMode: 'years', minViewMode: 'years'}" value="<?php echo $year; ?>" 
             placeholder="年份" data-am-datepicker readonly>
            
            <span class="am-input-group-btn">
                <button class="am-btn am-btn-default" type="button" id="movie-year-clear" title="清除年份设置">
                    ×
                    <!--<i class="am-icon-remove"></i>-->
                </button>
            </span>
        </div>
        
    </div>  <!-- md-3 -->
    
    <div class="am-u-md-3">
    
    </div>  <!-- md-3 -->
    
    </div>  <!-- 网格 -->
    
    </div>  <!-- 面板 -->
    </div>  <!-- 面板 -->
    
    <input type="text" name="pageno" id="movie-pageno" class="am-hide">
    
    </form>

    <ul class="am-avg-sm-3 am-avg-md-4 am-avg-lg-6 am-thumbnails movie-lists">
        <?php 
        // 名字
        preg_match('/<div class="s-tab-main">(.*)<\/ul>/sU', $content, $temp);
        
        preg_match_all('/<a class="js-tongjic" href="\/ct\/(\w*).html">\r\n\s+<div class="cover g-playicon">\r\n\s+<img src="([^"]*)">\r\n\s+(<span class="pay">付费<\/span>)?(                                <span class="hint">)?([^<]*)(<\/span>)?\s+<\/div>\r\n\s+<div class="detail">\r\n\s+<p class="title g-clear">\r\n\s+<span class="s1">(.*)<\/span>\r\n\s+<span class="s2">(.*)<\/span>\r\n\s+<\/p>\r\n\s+<p class="star">(.*)<\/p>/sU', $temp[1], $temp);
        
        $movieCount = count($temp[0]);
        
        for($j=0; $j<$movieCount; $j++) {
            $tmpArr['url'] = 'player.php?ctid='.$temp[1][$j];
            $tmpArr['cover'] = $temp[2][$j];
            $tmpArr['name'] = $temp[7][$j];
            $tmpArr['name2'] = $temp[9][$j];
            $tmpArr['line1'] = $temp[5][$j];    // 更新集数
            $tmpArr['line2'] = '';
            $tmpArr['line3'] = '> 在线观看';
            
            movieItem($tmpArr); 
        }
        
        if($movieCount == 0) {
            echo '
            <div class="am-alert am-alert-warning am-margin-top-sm am-margin-bottom-xl" data-am-alert>
                没找到符合条件的动漫，请尝试其他分类！
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
    cat: "<?php echo $cat; ?>",
    area: "<?php echo $area; ?>",
    curPage: <?php echo $pageno; ?>,     // 当前页码
    maxPage: <?php echo $maxPage; ?>    // 最大的页码
}


$(function() {
    $("#movie-cat").val(pageInfo.cat);
    $("#movie-area").val(pageInfo.area);
    
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
    var $myStart2 = $('#movie-year');
    
    
    var checkin = $myStart2.datepicker({
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
    }).data('amui.datepicker');
    
});
</script>


<?php


ui_footer();