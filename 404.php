<?php
require_once('init.php');

header('HTTP/1.1 404 Not Found');   //返回404状态
header("status: 404 Not Found");

// 错误内容
$errTitle = isset($errTitle)? $errTitle: '404';

ui_head($errTitle);

ui_topNav();
?>

<div class="am-container">

出错了！原因：<?php echo $errTitle; ?>

<div style="margin-bottom: 200px"></div>

</div>  <!-- 容器 -->

<?php


ui_footer();