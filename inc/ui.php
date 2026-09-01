<?php
// 前端输出模块
if(!defined('movie')) die('Insufficient Permissions');

/**
 * 输出网站头，提供网站必备的内容
 * @param $title 页面标题
 * @param $description 页面描述
 * @param $keywords 页面关键字
 * @return 获取到的内容（没有则为默认值）
 */
function ui_head($title, $description=null, $keywords=null) {
    header('Content-type: text/html; charset=utf-8');
    $title .= ' - '.C('slogan');
    $description = empty($description) ? '' : $description;
    $keywords .=',在线观看,无广告,高清,蓝光'; //这里加上共用的描述词
    ?>
<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="<?php echo $description;?>">
    <meta name="keywords" content="<?php echo $keywords;?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $title;?></title>
    
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    
    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    
    <link rel="icon" type="image/png" href="<?php echo C('siteurl'); ?>/assets/i/favicon.png">
    
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="<?php echo C('siteurl'); ?>/assets/i/app-icon72x72@2x.png">
    
    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <link rel="apple-touch-icon-precomposed" href="<?php echo C('siteurl'); ?>/assets/i/app-icon72x72@2x.png">
    
    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="<?php echo C('siteurl'); ?>/assets/i/app-icon72x72@2x.png">
    <meta name="msapplication-TileColor" content="#0e90d2">
    
    <link rel="stylesheet" href="<?php echo C('siteurl'); ?>/assets/css/amazeui.min.css">
    <link rel="stylesheet" href="<?php echo C('siteurl'); ?>/assets/css/app.css">

    <!--[if (gte IE 9)|!(IE)]><!-->
    <script src="<?php echo C('siteurl'); ?>/assets/js/jquery.min.js"></script>
    <!--<![endif]-->
    
    <!--[if lte IE 8 ]>
    <script src="https://libs.baidu.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
    <script src="<?php echo C('siteurl'); ?>/assets/js/amazeui.ie8polyfill.min.js"></script>
    <![endif]-->

    <?php jsSiteInfo();?>
    
<?php
}

/**
 * 输出网站顶部banner
 * @param $show 是否展示底部内容
 * @return 输出页面元素
 */
function ui_topNav() {
?>

</head>
<body>

<header class="am-topbar">
    <div class="am-container">
    <h1 class="am-topbar-brand hover-bounce">
        <a href="<?php echo C('siteurl'); ?>/" class="web-name">
            <!-- 网站 Logo -->
            <span class="am-icon-film am-icon-md"></span> 
            <?php echo C('name'); ?>
            
        </a>
    </h1>

    <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#doc-topbar-collapse'}">
        <span class="am-sr-only">导航切换</span> 
        <span class="am-icon-bars"></span>
    </button>

  <div class="am-collapse am-topbar-collapse" id="doc-topbar-collapse">
    <ul class="am-nav am-nav-pills am-topbar-nav">
        <li><a href="<?php echo C('siteurl'); ?>/">首页</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/movie.php">电影</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/tv.php">电视剧</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/cartoon.php">动漫</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/variety.php">综艺</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/live.php">电视直播</a></li>
        <li><a href="<?php echo C('siteurl'); ?>/vip.php">VIP解析</a></li>
        <li class="am-dropdown" data-am-dropdown>
            <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                更多 <span class="am-icon-caret-down"></span>
            </a>
            <ul class="am-dropdown-content">
                <li><a href="http://lab.mkblog.cn/music_new/" target="_blank">音乐</a></li>
                <li><a href="http://lab.mkblog.cn/wallpaper/" target="_blank">壁纸</a></li>
                <li><a href="http://tool.mkblog.cn/tao/" target="_blank">购物</a></li>
            </ul>
        </li>
    </ul>

    <form class="am-topbar-form am-topbar-left am-form-inline" role="search" action="<?php echo C('siteurl'); ?>/search.php">
        <div class="am-input-group am-input-group-primary am-input-group-sm">
            <input name="wd" type="text" class="am-form-field" placeholder="搜索" required>
            <span class="am-input-group-btn">
                <button class="am-btn am-btn-primary" type="submit">
                    <span class="am-icon-search"></span>
                </button>
            </span>
        </div>
    </form>

    <div class="am-topbar-right">
        <div id="show-history-dropdown" class="am-dropdown" data-am-dropdown="{boundary: '.am-topbar'}">
            <button id="show-history" class="am-btn am-btn-secondary am-topbar-btn am-btn-sm am-dropdown-toggle" data-am-dropdown-toggle>
                观看记录 <span class="am-icon-caret-down"></span>
            </button>
            <ul id="history-list" class="am-dropdown-content">
                <li><a href="javascript:;">播放记录载入中..</a></li>
            </ul>
        </div>
    </div>

  </div>
  </div>
</header>

<?php
}


/**
 * 输出网站底部公共文件
 * @param $show 是否展示底部内容
 * @return 输出页面元素
 */
function ui_footer($show = true) {
?>

    <!-- 返回顶部 -->
    <div data-am-widget="gotop" class="am-gotop am-gotop-fixed" title="返回顶部">
        <a href="#top" title="">
            <i class="am-gotop-icon am-icon-arrow-up"></i>
        </a>
    </div>
    
    <?php if($show) { ?>

    <!-- 底部栏 -->
    <footer data-am-widget="footer" class="am-footer am-footer-default am-hide-sm-only" data-am-footer="{  }">
        <div class="am-footer-miscs">
            <p>由 <a href="https://mkblog.cn/" title="孟坤博客" target="_blank" class="">孟坤博客</a>
            提供技术支持</p>
            <p>CopyRight © 2017 mkblog.cn</p>
            <p>湘ICP备xxxxxxxx号</p>
            <!-- 站长统计代码放在这里 -->
            <p>本站不提供任何资源存储服务，只提供查询服务</p>
        </div>
    </footer>

    <!-- 底部导航栏 -->
    <!-- 图标资源来自于 https://www.flaticon.com/packs/cinema-3 -->
    <div data-am-widget="navbar" class="am-navbar am-cf am-navbar-default am-show-sm-only" id="">
        <ul class="am-navbar-nav am-cf am-avg-sm-4">
            <li>
                <a href="<?php echo C('siteurl'); ?>/" class="">
                    <img src="https://ws1.sinaimg.cn/large/a15b4afegy1fic1fki6s1j203k03ka9t.jpg" alt="首页"/>
                    <span class="am-navbar-label">首页</span>
                </a>
            </li>
            <li>
                <a href="<?php echo C('siteurl'); ?>/movie.php" class="">
                    <img src="https://ws1.sinaimg.cn/large/a15b4afely1fic14zf5pqj203k03kjr9.jpg" alt="电影"/>
                    <span class="am-navbar-label">电影</span>
                </a>
            </li>
            <li>
                <a href="<?php echo C('siteurl'); ?>/tv.php" class="">
                    <img src="https://ws1.sinaimg.cn/large/a15b4afegy1fic1iuanydj203k03kjrd.jpg" alt="电视剧"/>
                    <span class="am-navbar-label">电视剧</span>
                </a>
            </li>
            <li>
                <a href="<?php echo C('siteurl'); ?>/cartoon.php" class="">
                    <img src="https://ws1.sinaimg.cn/large/a15b4afely1fic14imn45j203k03kglh.jpg" alt="动漫"/>
                    <span class="am-navbar-label">动漫</span>
                </a>
            </li>
            <li >
                <a href="<?php echo C('siteurl'); ?>/vip.php" class="">
                    <img src="https://ws1.sinaimg.cn/large/a15b4afegy1fic1eik5w4j203k03kq2t.jpg" alt="VIP解析"/>
                    <span class="am-navbar-label">VIP解析</span>
                </a>
            </li>
        </ul>
    </div>


    <?php }  ?>


    <!-- layer弹窗插件 -->
    <script src="<?php echo C('siteurl');?>/assets/plugns/layer/layer.js"></script>


<?php
includeJs('jquery.lazyload.min', '滚动加载插件');

?>

<script type="text/javascript">
var store;

$(function() {
    
    // 图片懒加载
    $("img.lazyload").lazyload({
        effect: "fadeIn",
        load: function() {
            $(this).removeClass('lazyload');
            $(this).addClass('img-loaded');
        }
    });
    
    // 展示播放历史记录
    $("#show-history").click(function() {
        store = $.AMUI.store;
        if (store.enabled) {
            var histemp = store.get('history')? store.get('history'): [];
            
            if(histemp.length == 0) {
                $("#history-list").html('<li><a href="javascript:;">暂无播放记录</a></li>');
            } else {
                $("#history-list").html('');
                
                for(var i=0; i<histemp.length; i++) {
                    switch(histemp[i].types) {
                        case 'movie':
                            $("#history-list").append('<li><a href="<?php echo C('siteurl'); ?>/player.php?mid='+histemp[i].id+'">'+histemp[i].name+'</a></li>');
                        break;
                        
                        case 'tv':
                            $("#history-list").append('<li><a href="<?php echo C('siteurl'); ?>/player.php?tvid='+histemp[i].id+'">'+histemp[i].name+' [第'+histemp[i].episode+'集]</a></li>');
                        break;
                        
                        case 'ct':
                            $("#history-list").append('<li><a href="<?php echo C('siteurl'); ?>/player.php?ctid='+histemp[i].id+'">'+histemp[i].name+' [第'+histemp[i].episode+'集]</a></li>');
                        break;
                        
                        case 'va':
                            $("#history-list").append('<li><a href="<?php echo C('siteurl'); ?>/player.php?vaid='+histemp[i].id+'">'+histemp[i].name+' ['+histemp[i].episode+']</a></li>');
                        break;
                    }
                }
                
                $("#history-list").append('<li><a href="javascript:;" onclick="clearHistory();"><span class="am-text-warning am-text-xs">清空播放记录</span></a></li>');
            }
            
        }
    });

});

// 清空历史记录
function clearHistory() {
    // 关闭下拉
    $("#show-history-dropdown").dropdown("close");
    
    // 清空播放记录存储
    store.remove('history');
    
    layer.msg("播放记录已清空");
}

// url编码
// 输入参数：待编码的字符串
function urlEncode(String) {
    return encodeURIComponent(String).replace(/'/g,"%27").replace(/"/g,"%22");	
}
</script>

<!-- 百度、360等搜索引擎的主动推送代码放在这里 ↓↓↓ -->



<!-- 百度、360等搜索引擎的主动推送代码放在这里 ↑↑↑ -->

<script src="<?php echo C('siteurl'); ?>/assets/js/amazeui.min.js"></script>
</body>
</html>

<?php
}



/**
 * 输出一个影片
 * @param $info 包含图片信息的数组
 * @return 输出到前端界面
 */
function movieItem($movie) {
    echo '
    <li>
    <a class="movie-item" href="'.C('siteurl').'/'.htmlspecialchars($movie["url"]).'" target="_blank">
        <div class="movie-cover">
            <img src="'.C('siteurl').'/assets/i/lazy.gif" data-original="'.htmlspecialchars($movie["cover"]).'" class="lazyload">
            <span class="movie-description">
                <i class="description-bg"></i>
                <p>'.htmlspecialchars($movie["line1"]).'</p>
                <p>'.htmlspecialchars($movie["line2"]).'</p>
                <p>'.htmlspecialchars($movie["line3"]).'</p>
            </span>
        </div>
        <div class="movie-title">
            <p class="movie-name">'.htmlspecialchars($movie["name"]).'</p>
            <p class="movie-tags">'.htmlspecialchars($movie["name2"]).'</p>
        </div>
    </a>
    </li>';
} 

/**
 * 输出 js 文件
 * @param $name js 文件名
 * @param $description js文件描述
 * @param $ver js版本号
 * @return 输出对应js文件
 */
function includeJs($name, $description = '', $ver = '1.0') {
    if($description) echo "\n    <!-- $description -->";
    echo "\n    <script src=\"".C('siteurl')."/assets/js/{$name}.js?v{$ver}\"></script>\n";
}

/**
 * 输出 css 文件
 * @param $name css文件名
 * @param $description css文件描述
 * @param $ver css版本号
 * @return 输出对应css文件
 */
function includeCss($name, $description = '', $ver = '1.0') {
    if($description) echo "\n    <!-- $description -->";
    echo "\n    <link rel=\"stylesheet\" href=\"".C('siteurl')."/assets/css/{$name}.css?v{$ver}\">\n";
}

/**
 * 输出网站相关信息，供页面内的 js 文件调用
 * @param 无
 * @return 无
 */
function jsSiteInfo() {
?>

    <script>
        // 网站相关信息，供页面内的 js 文件调用
        var mkSiteInfo = { siteUrl: "<?php echo C('siteurl'); ?>" }
    </script>
    
    <!-- 百度统计代码放在这里 ↓↓↓ -->
    
    
    
    <!-- 百度统计代码放在这里 ↑↑↑ -->
    
<?php
}