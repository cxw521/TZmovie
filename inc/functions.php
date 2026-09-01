<?php

/**
 * 获取GET或POST过来的参数
 * @param $key 键值
 * @param $default 默认值
 * @return 获取到的内容（没有则为默认值）
 */
function getParam($key,$default='')
{
    return trim($key && is_string($key) ? (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default)) : $default);
}

/**
 * 输出404页面
 * @param $errTitle 页面标题
 * @return 输出404
 */
function die404($errTitle = '404') {
    require(SYSTEM_ROOT.'/404.php');
    exit();
}

/**
 * curl 获取网页源码函数
 * @param $url 目标页面 URL
 * @return 页面源码
 */
function curl($url){ 
    $ch = curl_init(); 
    $timeout = 30; 
    $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);   // 伪造ua 
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); // 取消gzip压缩
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $content = trim(curl_exec($ch)); 
    curl_close($ch); 
    return $content; 
}

/**
 * 请求 360kan JSON API，返回解码后的数组
 * @param $url 接口完整地址
 * @return array|null
 */
function api_get($url) {
    $content = curl($url);
    if($content === '') return null;
    $data = json_decode($content, true);
    return is_array($data) ? $data : null;
}

/**
 * 分类筛选接口（/v1/filter/list）
 * @param $catid 分类ID 1=电影 2=电视剧 3=综艺 4=动漫
 * @param $opts 筛选参数 cat(中文类型)/area(中文地区)/year/act/pageno/size
 * @return array('movies'=>[], 'total'=>int, 'current_page'=>int)
 */
function filterList($catid, $opts = array()) {
    $params = array('catid' => $catid, 'pageno' => 1, 'size' => 24);
    foreach(array('cat','area','year','act') as $k) {
        if(isset($opts[$k]) && $opts[$k] !== '' && $opts[$k] !== 'all' && $opts[$k] !== 'other') {
            $params[$k] = $opts[$k];
        }
    }
    if(isset($opts['pageno']) && intval($opts['pageno']) > 0) $params['pageno'] = intval($opts['pageno']);
    if(isset($opts['size']) && intval($opts['size']) > 0) $params['size'] = intval($opts['size']);

    $url = 'https://api.web.360kan.com/v1/filter/list?' . http_build_query($params);
    $data = api_get($url);

    $ret = array('movies' => array(), 'total' => 0, 'current_page' => 1);
    if($data && isset($data['data'])) {
        $d = $data['data'];
        $ret['movies'] = isset($d['movies']) ? $d['movies'] : array();
        $ret['total'] = isset($d['total']) ? intval($d['total']) : 0;
        $ret['current_page'] = isset($d['current_page']) ? intval($d['current_page']) : 1;
    }
    return $ret;
}

/**
 * 榜单接口（/v1/rank）
 * @param $cat 榜单分类 2=电影 3=电视剧 4=综艺 5=动漫
 * @return array 榜单条目数组
 */
function rankList($cat) {
    $data = api_get('https://api.web.360kan.com/v1/rank?cat='.$cat);
    return ($data && isset($data['data']) && is_array($data['data'])) ? $data['data'] : array();
}

/**
 * 搜索接口（api.so.360kan.com/index）
 * @param $kw 关键词
 * @return array rows 数组
 */
function searchVideos($kw) {
    $data = api_get('https://api.so.360kan.com/index?kw='.urlencode($kw));
    if($data && isset($data['data']['longData']['rows'])) {
        return $data['data']['longData']['rows'];
    }
    return array();
}

/**
 * 播放详情接口（/v1/play）
 * @param $cat 分类 1=电影 2=电视剧 3=综艺 4=动漫
 * @param $id 影片 ent_id
 * @return array|null data 内容
 */
function playInfo($cat, $id) {
    $data = api_get('https://api.web.360kan.com/v1/play?cat='.$cat.'&id='.urlencode($id).'&num=1');
    return ($data && isset($data['data'])) ? $data['data'] : null;
}

/**
 * 详情接口（/v1/detail），综艺（cat=3）数据层
 * @param $cat 分类 1=电影 2=电视剧 3=综艺 4=动漫
 * @param $id 影片 ent_id
 * @return array|null
 */
function detailInfo($cat, $id) {
    $data = api_get('https://api.web.360kan.com/v1/detail?cat='.$cat.'&id='.urlencode($id).'&num=1');
    if(!$data || !isset($data['data']) || !is_array($data['data'])) return null;
    $d = $data['data'];
    $info = array(
        'ent_id' => $id,
        'title' => isset($d['title']) ? $d['title'] : '',
        'description' => isset($d['description']) ? $d['description'] : '',
        'moviecategory' => isset($d['moviecategory']) ? $d['moviecategory'] : array(),
        'pubdate' => isset($d['pubdate']) ? $d['pubdate'] : '',
        'area' => isset($d['area']) ? $d['area'] : '',
        'actor' => isset($d['actor']) ? $d['actor'] : array(),
        'cover' => isset($d['cdncover']) ? $d['cdncover'] : (isset($d['cover']) ? $d['cover'] : ''),
        'allupinfo' => isset($d['allupinfo']) ? $d['allupinfo'] : array(),
    );
    $playlinks = array();
    if(isset($d['playlinks']) && is_array($d['playlinks'])) {
        $playlinks = $d['playlinks'];
    }
    return array('info' => $info, 'playlinks' => $playlinks);
}

/**
 * 综艺剧集列表（/v1/detail + site + year）
 * @param $cat_id 分类 3=综艺
 * @param $ent_id 影片 ent_id
 * @param $site 播放源站
 * @return array 每项为 array('url'=>, 'name'=>, 'num'=>)
 */
function varietyEpisodes($cat_id, $ent_id, $site) {
    $data = api_get('https://api.web.360kan.com/v1/detail?cat='.$cat_id.'&id='.urlencode($ent_id).'&site='.urlencode($site).'&year='.date('Y'));
    if(!$data || !isset($data['data']['defaultepisode']) || !is_array($data['data']['defaultepisode'])) return array();
    $list = array();
    foreach($data['data']['defaultepisode'] as $ep) {
        if(empty($ep['url'])) continue;
        $list[] = array(
            'url' => $ep['url'],
            'name' => isset($ep['name']) ? $ep['name'] : '',
            'num' => isset($ep['sort']) ? $ep['sort'] : '',
        );
    }
    return $list;
}

/**
 * 剧集列表接口（api.so.360kan.com/episodesv2）
 * @param $cat_id 分类 1=电影 2=电视剧 3=综艺 4=动漫
 * @param $ent_id 影片 ent_id
 * @param $site 播放源站
 * @return array 每项为 array('url'=>, 'free_icon'=>)
 */
function episodeList($cat_id, $ent_id, $site) {
    $s = json_encode(array(array('cat_id' => intval($cat_id), 'ent_id' => $ent_id, 'site' => $site)));
    $data = api_get('https://api.so.360kan.com/episodesv2?v_ap=1&s='.urlencode($s));
    if($data && isset($data['data'][0]['seriesHTML']['seriesPlaylinks'])) {
        return $data['data'][0]['seriesHTML']['seriesPlaylinks'];
    }
    return array();
}

/**
 * 统一获取播放页所需数据
 * @param $cat 分类 1=电影 2=电视剧 3=综艺 4=动漫
 * @param $id 影片 ent_id
 * @param $title 影片名（用于搜索接口兜底获取播放链接）
 * @return array('info'=>详情, 'playlinks'=>[站点=>url], 'sites'=>[{ensite,cnsite}])
 */
function getMovieLinks($cat, $id, $title = '') {
    // 综艺走 detail 接口，其余走 play 接口
    $detail = null;
    if(intval($cat) === 3) {
        $detail = detailInfo($cat, $id);
        $info = $detail ? $detail['info'] : null;
        $playlinks = $detail ? $detail['playlinks'] : array();
    } else {
        $info = playInfo($cat, $id);
        $playlinks = array();
        if($info && isset($info['playlinks']) && is_array($info['playlinks']) && count($info['playlinks']) > 0) {
            $playlinks = $info['playlinks'];
        }
    }

    // 兜底：play 接口未返回链接或信息时，通过搜索接口取
    if(($title === '' && $info && isset($info['title']))) {
        $title = $info['title'];
    }
    if((empty($playlinks) || !$info) && $title !== '') {
        $rows = searchVideos($title);
        foreach($rows as $row) {
            if($row['en_id'] == $id) {
                if(empty($playlinks) && isset($row['playlinks']) && is_array($row['playlinks']) && count($row['playlinks']) > 0) {
                    $playlinks = $row['playlinks'];
                }
                if(!$info && isset($row['title'])) {
                    $info = array(
                        'ent_id' => $id,
                        'title' => strip_tags($row['title']),
                        'description' => isset($row['description']) ? $row['description'] : '',
                        'moviecategory' => isset($row['tag']) ? array($row['tag']) : array(),
                        'pubdate' => isset($row['year']) ? $row['year'] : '',
                        'actor' => isset($row['actList']) ? $row['actList'] : array(),
                    );
                }
                break;
            }
        }
    }

    return array(
        'info' => $info,
        'playlinks' => $playlinks,
        'sites' => siteList(array_keys($playlinks)),
    );
}

/**
 * 播放源站英文名转中文名
 * @param $en 英文站点名
 * @return string 中文站点名
 */
function siteName($en) {    $map = array(
        'qiyi' => '爱奇艺',
        'iqiyi' => '爱奇艺',
        'youku' => '优酷',
        'qq' => '腾讯视频',
        'leshi' => '乐视',
        'imgo' => '芒果TV',
        'mgtv' => '芒果TV',
        'sohu' => '搜狐',
        'bilibili1' => '哔哩哔哩',
        'douyin' => '抖音',
        'xigua' => '西瓜视频',
        'hunan' => '湖南卫视',
        'hunantv' => '湖南卫视',
        'm1905' => '1905电影网',
        'taopiaopiao' => '淘票票',
        'vip360' => '360VIP',
        'quanmin' => '全民小视频',
        'renren' => '人人视频',
        '56' => '56视频',
        'iqilu' => '齐鲁网',
        'wan' => '万维',
        'mtime' => '时光网',
        'cooltv' => '酷6',
        'tvb' => 'TVB',
        'pptv' => 'PPTV',
    );
    return isset($map[$en]) ? $map[$en] : $en;
}

/**
 * 把播放源站数组转为 [{ensite, cnsite}] 结构
 * @param $sites 站点英文名数组
 * @return array
 */
function siteList($sites) {
    $ret = array();
    if(!is_array($sites)) return $ret;
    foreach($sites as $s) {
        $ret[] = array('ensite' => $s, 'cnsite' => siteName($s));
    }
    return $ret;
}

/**
 * 数字类型/地区映射为中文（兼容旧 URL 数字筛选参数）
 * @param $type 页面类型 movie/tv/cartoon/variety
 * @param $key cat|area
 * @param $val 数字或中文值
 * @return string 中文值；无法映射时返回原值
 */
function cnFilter($type, $key, $val) {
    $maps = array(
        'movie' => array(
            'cat' => array('103'=>'喜剧','100'=>'爱情','106'=>'动作','102'=>'恐怖','104'=>'科幻','112'=>'剧情','105'=>'犯罪','113'=>'奇幻','108'=>'战争','115'=>'悬疑','107'=>'动画','117'=>'文艺','101'=>'伦理','118'=>'纪录','119'=>'传记','120'=>'歌舞','121'=>'古装','122'=>'历史','123'=>'惊悚'),
            'area' => array('11'=>'美国','10'=>'大陆','15'=>'香港','13'=>'韩国','14'=>'日本','12'=>'法国','16'=>'英国','17'=>'德国','18'=>'台湾','21'=>'泰国','22'=>'印度'),
        ),
        'tv' => array(
            'cat' => array('101'=>'言情','105'=>'伦理','109'=>'喜剧','108'=>'悬疑','111'=>'都市','100'=>'偶像','104'=>'古装','107'=>'军事','103'=>'警匪','112'=>'历史','106'=>'武侠','113'=>'科幻','102'=>'宫廷','114'=>'情景','115'=>'动作','116'=>'励志','117'=>'神话','118'=>'谍战','110'=>'粤语'),
            'area' => array('10'=>'内地','11'=>'香港','16'=>'台湾','12'=>'韩国','14'=>'泰国','15'=>'日本','13'=>'美国','17'=>'英国','18'=>'新加坡'),
        ),
        'cartoon' => array(
            'cat' => array('100'=>'热血','101'=>'恋爱','102'=>'美少女','103'=>'运动','104'=>'校园','105'=>'搞笑','106'=>'幻想','107'=>'冒险','108'=>'悬疑','109'=>'魔幻','110'=>'动物','111'=>'少儿','131'=>'亲子','112'=>'机战','113'=>'怪物','114'=>'益智','115'=>'战争','116'=>'社会','117'=>'友情','118'=>'成人','119'=>'竞技','120'=>'耽美','121'=>'童话','122'=>'LOLI','123'=>'青春','124'=>'男性向','125'=>'女性向','126'=>'动作','127'=>'真人版','128'=>'OVA版','129'=>'TV版','130'=>'电影版','132'=>'新番动画','133'=>'完结动画'),
            'area' => array('11'=>'日本','12'=>'美国','10'=>'大陆'),
        ),
        'variety' => array(
            'cat' => array('101'=>'选秀','102'=>'八卦','103'=>'访谈','104'=>'情感','105'=>'生活','106'=>'晚会','107'=>'搞笑','108'=>'音乐','109'=>'时尚','110'=>'游戏','111'=>'少儿','112'=>'体育','113'=>'纪实','114'=>'科教','115'=>'曲艺','116'=>'歌舞','117'=>'财经','118'=>'汽车','119'=>'播报','120'=>'真人秀'),
            'area' => array('10'=>'大陆','11'=>'台湾','12'=>'韩国','13'=>'日本','14'=>'欧美','15'=>'香港'),
        ),
    );
    if(isset($maps[$type][$key][$val])) return $maps[$type][$key][$val];
    return $val;
}

/**
 * 返回配置
 * @param $name 配置名称
 */
function C($name=''){
	global $webConfig;
	if(!$name)
		return $webConfig;
	else
		return $webConfig[$name]? $webConfig[$name]: '';
}

/**
 * 获取电视直播源（TXT 格式，带本地缓存）
 * @param string $url 直播源 TXT 地址
 * @param int $cacheTtl 缓存时间（秒），默认 6 小时
 * @return string 直播源文本，失败返回 ''
 */
function liveSource($url = 'https://live.zbds.top/tv/iptv4.txt', $cacheTtl = 21600) {
    // 优先使用已验证可用的本地源文件（删除无效频道后的精简列表）
    $verifiedFile = dirname(__DIR__) . '/data/live_verified.txt';
    if(file_exists($verifiedFile) && filesize($verifiedFile) > 0) {
        $content = @file_get_contents($verifiedFile);
        if(is_string($content) && trim($content) !== '') return $content;
    }
    $cacheFile = sys_get_temp_dir() . '/mkmovie_live_' . md5($url) . '.txt';
    $content = '';
    if(file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTtl) {
        $content = @file_get_contents($cacheFile);
    }
    if($content === '' || $content === false) {
        $content = curl($url);
        if($content !== '' && $content !== false) {
            @file_put_contents($cacheFile, $content);
        }
    }
    return is_string($content) ? $content : '';
}

/**
 * 解析 TXT 直播源为分组频道列表
 * @param string $text TXT 直播源内容
 * @return array('groups'=>[ ['name'=>, 'channels'=>[ ['name'=>,'url'=>] ] ])
 */
function parseLiveChannels($text) {
    $groups = array();
    $curGroup = null;
    $lines = explode("\n", $text);
    foreach($lines as $line) {
        $line = trim($line);
        if($line === '') continue;
        // 分组标记：频道组名,#genre#
        if(preg_match('/^(.+?),#genre#$/i', $line, $m)) {
            $name = trim($m[1]);
            $groups[$name] = array('name' => $name, 'channels' => array());
            $curGroup = $name;
            continue;
        }
        // 频道行：频道名,url
        $comma = strpos($line, ',');
        if($comma !== false) {
            $name = trim(substr($line, 0, $comma));
            $url = trim(substr($line, $comma + 1));
            if($name === '' || $url === '') continue;
            // 过滤浏览器无法播放的 UDP 组播地址
            if(stripos($url, 'udp://') === 0 || stripos($url, 'rtsp://') === 0 || stripos($url, 'rtmp://') === 0) continue;
            if(stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0) continue;
            $ch = array('name' => $name, 'url' => $url);
            if($curGroup !== null && isset($groups[$curGroup])) {
                $groups[$curGroup]['channels'][] = $ch;
            } else {
                // 无分组标记，放入"其他"
                if(!isset($groups['其他'])) $groups['其他'] = array('name' => '其他', 'channels' => array());
                $groups['其他']['channels'][] = $ch;
            }
        }
    }
    // 清理空分组
    foreach($groups as $k => $g) {
        if(empty($g['channels'])) unset($groups[$k]);
    }
    return array_values($groups);
}

/**
 * 采集 360kan 首页轮播 banner（/v1/block 接口，"猜你喜欢"推荐）
 * @param int $cacheTtl 缓存时间（秒），默认 6 小时
 * @return array 每个元素为 array('img'=>, 'name'=>, 'description'=>, 'url'=>)
 */
function homeBanner($cacheTtl = 21600) {
    $cacheFile = sys_get_temp_dir() . '/mkmovie_banner.json';
    if(file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTtl) {
        $cached = @json_decode(@file_get_contents($cacheFile), true);
        if(is_array($cached) && count($cached) > 0) return $cached;
    }
    $data = api_get('https://api.web.360kan.com/v1/block?blockid=522');
    $banners = array();
    if($data && isset($data['data']['lists']) && is_array($data['data']['lists'])) {
        foreach($data['data']['lists'] as $it) {
            $img = '';
            if(isset($it['pic_lists']) && is_array($it['pic_lists'])) {
                foreach($it['pic_lists'] as $p) {
                    if(!empty($p['url']) && strpos($p['url'], 'http') === 0) {
                        $img = $p['url'];
                        break;
                    }
                }
            }
            if($img === '') continue;
            $title = isset($it['title']) ? $it['title'] : '';
            $desc = isset($it['comment']) ? $it['comment'] : '';
            $cat = isset($it['cat']) ? $it['cat'] : '';
            $ent = isset($it['ent_id']) ? $it['ent_id'] : '';
            $link = '';
            if($ent !== '') {
                switch($cat) {
                    case '1': $link = 'player.php?mid='.$ent; break;   // 电影
                    case '2': $link = 'player.php?tvid='.$ent; break;  // 电视剧
                    case '3': $link = 'player.php?vaid='.$ent; break;  // 综艺
                    case '4': $link = 'player.php?ctid='.$ent; break;  // 动漫
                    default:  $link = ''; break;
                }
            }
            if($title === '') continue;
            $banners[] = array(
                'img' => $img,
                'name' => $title,
                'description' => $desc,
                'url' => $link,
            );
        }
    }
    if(count($banners) > 0) {
        @file_put_contents($cacheFile, json_encode($banners, JSON_UNESCAPED_UNICODE));
    }
    return $banners;
}
