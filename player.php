<?php
require_once('init.php');

$tvID = getParam('mid');    // 电影

if($tvID) {
    require SYSTEM_ROOT.'/inc/movie.php';
} else {
    $tvID = getParam('tvid');   // 电视剧
    
    if($tvID) {
        require SYSTEM_ROOT.'/inc/tv.php';
    } else {
        
        $tvID = getParam('ctid');   // 动画片
    
        if($tvID) {
            require SYSTEM_ROOT.'/inc/ct.php';
        } else {
            $tvID = getParam('vaid');   // 综艺
    
            if($tvID) {
                require SYSTEM_ROOT.'/inc/va.php';
            } else {
                die404('缺少参数');
            }
        }
        
    }
}
ui_footer();