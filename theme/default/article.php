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
<h2>[[::aTitle]]</h2>
<h3>[[::aTime, dateFormat, Y/m/d H:i]] [[=page:InCate]] <a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a>
<span class="articleShare"><a href="#comment-[[::aID]]"><span class="icon-comment" title="[[=page:Comments]]"></span><span id="ds-thread-count" class="ds-thread-count" data-thread-key="[[::aID]]"></span>[[::aComments]]
</a></span>
</h3>
<div class="details">
[[::aContent, formatText, full]]
</div>
[[::aTags, hasTags, <h3 class="tagsRow"><span class="icon-tag"></span>]] [[::aTags, formatTags, <span class="oneTag"><a href="[::siteURL]/[::linkPrefixTag]/[::tagInURL]/">[::tagValue]</a></span>]][[::aTags, hasTags, </h3>]]
[[::ext_articleDetail]]
</article>

<div id="comment-[[::aID]]">
[[::commentarea]]
[[::gittalkarea]]
[[::gitmentarea]]
[[::disqusarea]]
[[::ext_commentArea]]
</div>
