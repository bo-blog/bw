<?php 
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
define ('P', './');

include_once (P . 'inc/system.php');
$canonical = new bwCanonicalization;

$article = new bwArticle;

$article -> alterPerPage (500);
$article -> getArticleList ();

$outputxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
$outputxml .= "<url>\n<loc>{$conf['siteURL']}/index.php</loc>\n<lastmod>" . gmdate("Y-m-d\TH:i:s+00:00") . "</lastmod>\n<changefreq>always</changefreq>\n<priority>1.0</priority>\n</url>\n";

foreach ($article -> articleList as $item) {
	$outputxml .= "<url>\n<loc>{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$item['aID']}/</loc>\n<lastmod>" . gmdate('c', strtotime($item['aTime'])) . "</lastmod>\n<changefreq>daily</changefreq>\n<priority>0.9</priority>\n</url>\n";
} 

$outputxml .= "</urlset>";

@header("Content-Type: application/xml; charset=utf-8");
die ($outputxml);
