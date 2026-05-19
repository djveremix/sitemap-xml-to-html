<?php
/**
 * Sitemap XML转HTML生成器
 * 适配：响应式全站适配 + 顶级SEO优化 + AI GEO地理收录标准
 * 作者：开源轻量PHP站点地图工具
 */

// ========== 自定义配置区（直接修改即可） ==========
$site_name     = "你的网站名称";
$site_domain   = "https://www.xxx.com"; // 填写真实域名
$site_city     = "中国";                // 城市/地区
$site_keywords = "站点地图,全站链接,网站导航";
$save_static   = true;                  // 是否自动生成 sitemap.html
// =================================================

// 路径定义
$sitemap_xml_path = __DIR__ . '/sitemap.xml';
$html_save_path   = __DIR__ . '/sitemap.html';

// 文件存在性校验
if (!file_exists($sitemap_xml_path)) {
    exit('<h3 style="text-align:center;color:red;">错误：当前目录未找到 sitemap.xml 文件</h3>');
}
if (!is_readable($sitemap_xml_path)) {
    exit('<h3 style="text-align:center;color:red;">错误：sitemap.xml 无读取权限</h3>');
}

// 解析XML站点地图
libxml_use_internal_errors(true);
$xml_data = simplexml_load_file($sitemap_xml_path);
$url_list = $xml_data->url ?? [];
libxml_clear_errors();

// 提前定义变量 杜绝所有PHP警告
$total_num  = count($url_list);
$create_time = date('Y-m-d H:i:s');
$today_date  = date('Y-m-d');

// 组装完整HTML页面
$html_content = <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <!-- 响应式核心视口标签 -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=yes">
    <!-- 搜索引擎SEO全套标签 -->
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="bingbot" content="index,follow">
    <meta name="baidu-spider" content="index,follow">
    <meta name="description" content="{$site_name}全站站点地图，汇总网站所有有效页面链接，助力搜索引擎快速收录与AI智能抓取">
    <meta name="keywords" content="{$site_keywords}">
    <meta name="author" content="{$site_name}">
    <!-- 移动端适配标签 -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- 标准化链接 防重复收录 -->
    <link rel="canonical" href="{$site_domain}/sitemap.html">
    <!-- ========== AI GEO 全套地理收录标签 ========== -->
    <meta name="geo.region" content="CN">
    <meta name="geo.placename" content="{$site_city}">
    <meta name="geo.position" content="0.0;0.0">
    <meta name="ICBM" content="0.0, 0.0">
    <meta property="place:location:latitude" content="">
    <meta property="place:location:longitude" content="">
    <!-- 页面标题 -->
    <title>{$site_name} - 全站站点地图 | 全站链接导航</title>
    <!-- 全局响应式CSS 手机/平板/电脑完美适配 -->
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:"Microsoft YaHei",system-ui,sans-serif;background:#f8f9fa;color:#333;line-height:1.7;padding:15px;}
        .container{max-width:1280px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px #eee;}
        .header-box{text-align:center;padding-bottom:20px;border-bottom:1px solid #eee;margin-bottom:20px;}
        .header-box h1{font-size:24px;color:#222;margin-bottom:8px;}
        .tips-text{font-size:14px;color:#666;margin:5px 0;}
        .info-bar{display:flex;flex-wrap:wrap;gap:15px;margin:15px 0;font-size:14px;color:#555;}
        /* 表格响应式核心：手机横向滚动 */
        .table-wrap{width:100%;overflow-x:auto;margin:20px 0;}
        table{width:100%;min-width:720px;border-collapse:collapse;}
        th,td{padding:12px 10px;border:1px solid #e5e7eb;text-align:left;font-size:14px;}
        th{background:#f1f5f9;font-weight:600;color:#111;white-space:nowrap;}
        td{background:#fff;}
        .site-link{color:#0066cc;text-decoration:none;word-break:break-all;}
        .site-link:hover{text-decoration:underline;}
        .geo-text{color:#16a34a;font-size:13px;}
        .time-text{color:#888;font-size:13px;}
        footer{text-align:center;margin-top:30px;padding-top:20px;border-top:1px solid #eee;font-size:13px;color:#999;}
        /* 小屏手机适配 */
        @media (max-width:576px){
            body{padding:8px;}
            .container{padding:15px;}
            .header-box h1{font-size:20px;}
        }
    </style>
    <!-- Schema.org 顶级结构化数据 适配搜索引擎+AI收录 -->
    <script type="application/ld+json">
    {
        "@context":"https://schema.org",
        "@type":"SiteNavigationElement",
        "name":"{$site_name}站点地图",
        "description":"全站页面链接汇总导航，助力搜索引擎收录",
        "url":"{$site_domain}/sitemap.html",
        "publisher":{"
            "@type":"Organization",
            "name":"{$site_name}"
        },
        "datePublished":"{$today_date}"
    }
    </script>
</head>
<body>
    <div class="container">
        <div class="header-box">
            <h1>{$site_name} 全站站点地图</h1>
            <p class="tips-text">本页面符合SEO搜索引擎优化、AI地理GEO收录双重标准</p>
            <div class="info-bar">
                <span>页面总数量：{$total_num} 条</span>
                <span>地图生成时间：{$create_time}</span>
                <span>收录适配：百度/搜狗/谷歌/AI智能搜索</span>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>页面访问链接</th>
                        <th>最后更新时间</th>
                        <th>抓取优先级</th>
                        <th>地理收录坐标</th>
                    </tr>
                </thead>
                <tbody>
HTML;

// 循环遍历所有链接 安全转义防止XSS
foreach ($url_list as $item) {
    $url_loc     = htmlspecialchars(trim($item->loc ?? ''));
    $last_mod    = !empty($item->lastmod) ? date('Y-m-d H:i',strtotime($item->lastmod)) : '暂无更新';
    $priority    = htmlspecialchars($item->priority ?? '0.5');
    // 读取GEO经纬度
    $lat = htmlspecialchars($item->children('geo',true)->lat ?? '');
    $lng = htmlspecialchars($item->children('geo',true)->lng ?? '');
    $geo_info = (!empty($lat) && !empty($lng)) ? "{$lat} , {$lng}" : "未设置地理坐标";

    $html_content .= "
    <tr>
        <td><a href=\"{$url_loc}\" class=\"site-link\" target=\"_blank\" rel=\"noopener noreferrer\">{$url_loc}</a></td>
        <td class=\"time-text\">{$last_mod}</td>
        <td>{$priority}</td>
        <td class=\"geo-text\">{$geo_info}</td>
    </tr>
    ";
}

// 闭合页面底部
$html_content .= <<<HTML
                </tbody>
            </table>
        </div>

        <footer>
            <p>站点地图仅用于搜索引擎抓取收录与站内导航 · 遵循全网SEO优化规范 · 适配AI地理位置收录</p>
            <p>建议将本页面添加至网站底部导航，提升全站内链权重</p>
        </footer>
    </div>
</body>
</html>
HTML;

// 保存静态HTML文件
if ($save_static === true) {
    file_put_contents($html_save_path, $html_content);
}

// 前端直接输出页面
echo $html_content;
?>
