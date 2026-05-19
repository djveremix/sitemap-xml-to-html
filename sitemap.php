<?php
/**
 * Sitemap XML 转 HTML 生成器
 * 符合：SEO搜索引擎优化 + AI GEO地理收录标准
 * 作用：自动读取同目录sitemap.xml，生成优化级HTML站点地图
 */

// -------------------------- 配置项（可自定义） --------------------------
$site_name = "你的网站名称";          // 网站名称
$page_title = "站点地图 | {$site_name}"; // 页面标题
$page_description = "{$site_name} 站点地图，包含所有页面链接、更新时间、地理信息，方便搜索引擎与AI收录";
$site_domain = $_SERVER['HTTP_HOST']; // 自动获取域名
$save_html = true;                    // 是否自动生成 sitemap.html 静态文件
// -----------------------------------------------------------------------

// 定义文件路径
$sitemap_xml = __DIR__ . '/sitemap.xml';
$output_html = __DIR__ . '/sitemap.html';

// 1. 检查 sitemap.xml 是否存在
if (!file_exists($sitemap_xml)) {
    die("<h1>错误：未找到同目录下的 sitemap.xml 文件</h1>");
}
if (!is_readable($sitemap_xml)) {
    die("<h1>错误：sitemap.xml 文件无读取权限</h1>");
}

// 2. 解析 XML 文件（兼容标准Sitemap协议）
libxml_use_internal_errors(true); // 禁用XML原生错误
try {
    $xml = simplexml_load_file($sitemap_xml);
    if (!$xml) {
        $error = libxml_get_last_error();
        die("<h1>XML解析失败：{$error->message}</h1>");
    }
    $urls = $xml->url ?? [];
} catch (Exception $e) {
    die("<h1>解析异常：{$e->getMessage()}");
}
libxml_clear_errors();

// ✅ 修复点：提前定义变量，解决未定义报错
$total_urls = count($urls);
$generate_time = date('Y-m-d H:i:s');

// 3. 生成 HTML 内容（SEO + AI GEO 标准）
$html = <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <!-- SEO核心：响应式、索引标签 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <title>{$page_title}</title>
    <meta name="description" content="{$page_description}">
    <link rel="canonical" href="https://{$site_domain}/sitemap.html">
    <!-- AI GEO 地理收录优化 -->
    <meta name="geo.region" content="CN">
    <meta name="geo.placename" content="China">
    <!-- 样式：极简响应式，SEO友好无冗余 -->
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:system-ui,Segoe UI,Microsoft YaHei,sans-serif}
        body{max-width:1200px;margin:20px auto;padding:0 20px;line-height:1.6;color:#333}
        header{text-align:center;margin:30px 0;padding-bottom:20px;border-bottom:1px solid #eee}
        h1{color:#222;font-size:28px;margin-bottom:10px}
        .stats{color:#666;font-size:14px}
        .sitemap-table{width:100%;border-collapse:collapse;margin:20px 0}
        .sitemap-table th{background:#f5f5f5;padding:12px;text-align:left;border:1px solid #eee}
        .sitemap-table td{padding:12px;border:1px solid #eee;vertical-align:top}
        .url{color:#007bff;text-decoration:none}
        .url:hover{text-decoration:underline}
        .geo{color:#28a745;font-size:12px}
        .lastmod{color:#666;font-size:13px}
        footer{text-align:center;margin:40px 0;padding-top:20px;border-top:1px solid #eee;color:#999;font-size:14px}
        @media(max-width:768px){.hide-mobile{display:none}}
    </style>
    <!-- Schema.org 结构化数据（搜索引擎+AI 优先识别） -->
    <script type="application/ld+json">
    {
        "@context":"https://schema.org",
        "@type":"WebPage",
        "name":"{$page_title}",
        "description":"{$page_description}",
        "publisher":{"@type":"Organization","name":"{$site_name}"},
        "inLanguage":"zh-CN"
    }
    </script>
</head>
<body>
    <header>
        <h1>{$site_name} 站点地图</h1>
        <p class="stats">总计链接：{$total_urls} 个 | 生成时间：{$generate_time}</p>
    </header>

    <main>
        <section>
            <table class="sitemap-table">
                <thead>
                    <tr>
                        <th>页面链接</th>
                        <th class="hide-mobile">更新时间</th>
                        <th class="hide-mobile">优先级</th>
                        <th>地理坐标</th>
                    </tr>
                </thead>
                <tbody>
HTML;

// 遍历URL数据
foreach ($urls as $item) {
    // 解析Sitemap标准字段
    $loc = htmlspecialchars($item->loc ?? '#');
    $lastmod = !empty($item->lastmod) ? date('Y-m-d H:i', strtotime($item->lastmod)) : '无';
    $priority = htmlspecialchars($item->priority ?? '0.5');
    $changefreq = htmlspecialchars($item->changefreq ?? 'daily');
    
    // 解析 AI GEO 地理坐标（支持扩展sitemap标签）
    $geo_lat = htmlspecialchars($item->children('geo', true)->lat ?? '');
    $geo_lng = htmlspecialchars($item->children('geo', true)->lng ?? '');
    $geo_html = $geo_lat && $geo_lng ? "<span class='geo'>{$geo_lat}, {$geo_lng}</span>" : '无';

    // 拼接表格行
    $html .= <<<TR
    <tr>
        <td><a href="{$loc}" class="url" target="_blank" rel="noopener noreferrer">{$loc}</a></td>
        <td class="lastmod hide-mobile">{$lastmod}</td>
        <td class="hide-mobile">{$priority}</td>
        <td>{$geo_html}</td>
    </tr>
TR;
}

// 闭合HTML结构
$html .= <<<HTML
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>生成时间：{$generate_time} | 优化标准：SEO搜索引擎 + AI GEO地理收录</p>
    </footer>
</body>
</html>
HTML;

// 4. 自动保存静态HTML文件（开启后生成 sitemap.html）
if ($save_html) {
    file_put_contents($output_html, $html);
}

// 5. 直接输出HTML页面
echo $html;
?>
