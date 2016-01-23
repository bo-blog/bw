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

$article -> alterPerPage (20);
$article -> getArticleList ();

$outputxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>{$conf['siteName']}</title>\n<link>{$conf['siteURL']}</link>\n<description>{$conf['authorIntro']}</description>\n<image><url>{$conf['siteURL']}/conf/profile.png</url><title>{$conf['authorName']}</title><link>{$conf['siteURL']}</link></image>\n";

foreach ($article -> articleList as $item) {
	$item['aContent'] = @explode ('+++', $item['aContent']);
	$outputxml .= "<item>\n<title>{$item['aTitle']}</title>\n<link>{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$item['aID']}/</link>\n<author>{$conf['authorName']}</author><pubDate>" . date('c', strtotime($item['aTime'])) . "</pubDate>\n<guid isPermalLink=\"true\">{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$item['aID']}/</guid>\n<comment>{$conf['siteURL']}/{$conf['linkPrefixArticle']}/{$item['aID']}/#comment-{$item['aID']}</comment>\n<description><![CDATA[". bwView :: textFormatter ($item['aContent'][0]). "]]></description>\n</item>\n";
} 

$outputxml .= "</channel></rss>";

@header("Content-Type: application/xml; charset=utf-8");
die ($outputxml);
