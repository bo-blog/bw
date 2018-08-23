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
<h3>[[::aTime, dateFormat, Y/m/d H:i]] [[=page:InCate]] <a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a>
<span class="articleShare"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/#comment-[[::aID]]"><span class="icon-comment" title="[[=page:Comments]]"></span>[[::aComments]]</a></span>
</h3>
<div class="details">
[[::aContent, formatText, less]]
[[::aID, readMore, <span class="readMore"><a href="%s"><span class="icon-arrow-right6"></span> %s</a></span>]]
</div>

[[::aTags, hasTags, <h3 class="tagsRow"><span class="icon-tag"></span>]] [[::aTags, formatTags, <span class="oneTag"><a href="[::siteURL]/[::linkPrefixTag]/[::tagInURL]/">[::tagValue]</a></span>]][[::aTags, hasTags, </h3>]]
[[::ext_summaryDetail]]

</article>
