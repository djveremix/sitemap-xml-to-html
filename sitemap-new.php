<?php
/**
 * 纯净版 Sitemap XML 转 HTML + RSS
 * 无计划任务 | 无网络请求 | 零服务器负载 | 纯被动执行
 * 仅运行时生成文件，不占用服务器资源
 */

// -------------------------- 基础配置 --------------------------
$site_name     = "立信（万安）智显科技有限公司";
$site_domain   = "https://www.lessonsd.com";
$site_desc     = "专业生产显示模组、液晶屏、LCM液晶模块，万安工厂直营";
$site_city     = "江西省吉安市万安县";
$save_static   = true; // 运行时生成静态文件

// AI GEO 地理坐标
$factory_lat   = "26.463339";
$factory_lng   = "114.719446";
// -------------------------------------------------------------

// 文件路径定义
$sitemap_xml  = __DIR__ . '/sitemap.xml';
$html_file    = __DIR__ . '/sitemap.html';
$rss_file     = __DIR__ . '/rss.xml';

// 检查原始地图文件
if (!file_exists($sitemap_xml)) {
    die("❌ 错误：未找到 sitemap.xml 文件");
}

// 轻量级解析XML
libxml_use_internal_errors(true);
$xml = simplexml_load_file($sitemap_xml);
$url_list = $xml->url ?? [];
libxml_clear_errors();

// 公共参数
$total_links  = count($url_list);
$generate_time = date('Y-m-d H:i:s');
$rss_url = $site_domain . "/rss.xml";
$geo_position = "$factory_lat;$factory_lng";
$icbm_coords  = "$factory_lat, $factory_lng";

// -------------------------- 生成 RSS 2.0 --------------------------
function get_title($url, $domain) {
    $str = str_replace($domain, '', $url);
    $str = preg_replace('/\.(html|php|htm)$/i', '', $str);
    $str = trim($str, '/');
    $str = str_replace(['-', '_', '/'], ' ', $str);
    $title = ucwords($str);
    return empty($title) ? '首页' : htmlspecialchars($title, ENT_XML1, 'UTF-8');
}

$rss = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<atom:link href="'.$rss_url.'" rel="self" type="application/rss+xml" />
<title>'.$site_name.'</title>
<link>'.$site_domain.'</link>
<description>'.$site_desc.'</description>
<language>zh-CN</language>
<pubDate>'.date('r').'</pubDate>
<lastBuildDate>'.date('r').'</lastBuildDate>';

foreach ($url_list as $item) {
    $loc = htmlspecialchars(trim($item->loc ?? ''));
    $lastmod = !empty($item->lastmod) ? date('r', strtotime($item->lastmod)) : date('r');
    $title = get_title($loc, $site_domain);
    $rss .= "<item><title>$title</title><link>$loc</link><pubDate>$lastmod</pubDate><guid isPermaLink=\"true\">$loc</guid></item>";
}
$rss .= '</channel></rss>';

// -------------------------- 生成 HTML 站点地图 --------------------------
$html = <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="robots" content="index,follow">
<meta name="description" content="{$site_name} 官方站点地图">
<link rel="canonical" href="{$site_domain}/sitemap.html">
<link rel="alternate" type="application/rss+xml" title="RSS订阅" href="{$rss_url}">
<!-- GEO 地理收录标签 -->
<meta name="geo.region" content="CN-JX-JA">
<meta name="geo.placename" content="{$site_city}工业园二区2020标准厂房4栋">
<meta name="geo.position" content="{$geo_position}">
<meta name="ICBM" content="{$icbm_coords}">
<title>{$site_name} - 站点地图</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Microsoft YaHei,sans-serif;background:#f8f9fa;padding:20px;color:#333}
.container{max-width:1200px;margin:0 auto;background:#fff;padding:25px;border-radius:10px}
.header{text-align:center;padding-bottom:20px;border-bottom:1px solid #eee;margin-bottom:20px}
.table-box{overflow-x:auto}
table{width:100%;border-collapse:collapse;margin:20px 0}
th,td{padding:12px;border:1px solid #eee}
th{background:#f5f7fa}
a{color:#0066cc;text-decoration:none}
.rss{color:#ff6600}
footer{text-align:center;padding-top:20px;border-top:1px solid #eee;color:#999}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{$site_name} 站点地图</h1>
        <p>总链接：{$total_links} 条 | 更新时间：{$generate_time}</p>
    </div>
    <div class="table-box">
        <table>
            <tr><th>页面链接</th><th>更新时间</th><th>优先级</th><th>RSS订阅</th></tr>
HTML;

foreach ($url_list as $item) {
    $loc = htmlspecialchars(trim($item->loc ?? ''));
    $lastmod = !empty($item->lastmod) ? date('Y-m-d H:i', strtotime($item->lastmod)) : '无';
    $priority = htmlspecialchars($item->priority ?? '0.5');
    $html .= "<tr><td><a href='$loc' target='_blank'>$loc</a></td><td>$lastmod</td><td>$priority</td><td><a href='$rss_url' class='rss'>订阅</a></td></tr>";
}

$html .= <<<HTML
        </table>
    </div>
    <footer>
        <p>{$site_name} | 工厂地址：{$site_city}工业园二区2020标准厂房4栋</p>
        <p>自动生成：sitemap.html + rss.xml | SEO优化版</p>
    </footer>
</div>
</body>
</html>
HTML;

// -------------------------- 保存文件（仅运行时执行） --------------------------
if ($save_static) {
    file_put_contents($rss_file, $rss);
    file_put_contents($html_file, $html);
}

// 执行完成提示
echo "✅ 执行完成！已生成：<br>1. sitemap.html<br>2. rss.xml";
?>
