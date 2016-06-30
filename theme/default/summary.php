<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/
if (!defined ('P')) {
	die ('Access Denied.');
}

?>

<article id="article-[[::aID]]">
<span class="decoArticle"></span>
<h2><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/">[[::aTitle]]</a></h2>
<h3><span class="icon-pencil"></span> [[::aTime, dateFormat, Y/m/d H:i]] [[=page:InCate]] <a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a>
<span class="articleShare"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/#comment-[[::aID]]"><span class="icon-comment" title="[[=page:Comments]]"></span>[[::aComments]]</a> <a href="##"><span class="icon-share" id="share-[[::aID]]" title="[[=page:ShareTo]]"></span></a></span>
</h3>
<div class="shareLayer" id="share-[[::aID]]-layer">
<span class="shareLayerItem"><a href="http://www.jiathis.com/send/?webid=weixin&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-weixin"></span> [[=page:social:WeChat]]</a></span>
<span class="shareLayerItem"><a href="http://www.jiathis.com/send/?webid=tsina&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-sina-weibo"></span> [[=page:social:Weibo]]</a></span>
<span class="shareLayerItem"><a href="http://www.jiathis.com/send/?webid=douban&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-douban"></span> [[=page:social:Douban]]</a></span>
<span class="shareLayerItem"><a href="http://www.jiathis.com/send/?webid=renren&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-renren"></span> [[=page:social:Renren]]</a></span>
</div>
<div class="details">
[[::aContent, formatText, less]]
[[::aID, readMore, <span class="readMore"><a href="%s"><span class="icon-arrow-right6"></span> %s</a></span>]]
</div>

[[::aTags, hasTags, <h3 class="tagsRow"><span class="icon-tag"></span>]] [[::aTags, formatTags, <span class="oneTag"><a href="[::siteURL]/[::linkPrefixTag]/[::tagInURL]/">[::tagValue]</a></span>]][[::aTags, hasTags, </h3>]]
[[::ext_summaryDetail]]

</article>
