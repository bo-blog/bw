<?php
//Copyright: Byke

if (!defined ('P')) {
	die ('Access Denied.');
}

$article=new bwArticle;

$article->fetchArticle ($canonical->currentArgs['aID']);

loadServices (); //Load Duoshuo

$view=new bwView;
$view->setPageTitle ($article->articleList[$canonical->currentArgs['aID']]['aTitle']);
$view->setActiveNav ($article->articleList[$canonical->currentArgs['aID']]['aCateURLName']);
$view->setPassData ($article->articleList[$canonical->currentArgs['aID']]);
$view->setLoop ('navigation', bw::$cateList);
$view->setLoop ('sociallink', bw::getSocialLinks ());
$view->setLoop ('externallink', bw::getExternalLinks ());
$view->setMaster ('page');
$view->setWorkFlow (array ('article', 'navigation', 'sociallink', 'externallink', 'page'));
$view->finalize ();

