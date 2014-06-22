<?php
//Copyright: Byke

if (!defined ('P')) {
	die ('Access Denied.');
}

?>

<article id="article-[[::aID]]">
<h2 class="colorWhite padLeft textL inMiddle shadowGrey"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/">[[::aTitle]]</a></h2>
<h3 class="colorGrey padLeft textXS inMiddle"><a href="[[::siteURL]]/[[::linkPrefixCategory]]/[[::aCateURLName]]/">[[::aCateDispName]]</a> | [[::aTime, dateFormat, M d Y g:ia]]
<span class="articleShare toRight textS"><a href="[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/#comment-[[::aID]]"><span class="icon-comment" title="[[=page:Comments]]"></span></a> <a href="##"><span class="icon-share" id="share-[[::aID]]" title="[[=page:ShareTo]]"></span></a></span>
</h3>
<div class="shareLayer toRight shadowGrey bgWhite textS asNone" id="share-[[::aID]]-layer">
<span class="shareLayerItem asBlock"><a href="http://www.jiathis.com/send/?webid=weixin&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-weixin"></span> [[=page:social:WeChat]]</a></span>
<span class="shareLayerItem asBlock"><a href="http://www.jiathis.com/send/?webid=tsina&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-sina-weibo"></span> [[=page:social:Weibo]]</a></span>
<span class="shareLayerItem asBlock"><a href="http://www.jiathis.com/send/?webid=renren&url=[[::siteURL]]/[[::linkPrefixArticle]]/[[::aID]]/&title=[[::aTitle, URLEncode]]"><span class="icon-renren"></span> [[=page:social:Renren]]</a></span>
</div>
<div class="details padLeft padRight textS colorDarkGrey bgWhite shadowGrey inMiddle">
[[::aContent, formatText, less]]
</div>
[[::aID, readMore, <div class="readMore padLeft heavy colorGreen toLeft"><a href="%s">%s</a></div>]]


[[::aTags, hasTags, <h3 class="tagsRow inMiddle"><span class="icon-tag colorGrey"></span>]] [[::aTags, formatTags, <span class="oneTag"><a href="[::siteURL]/[::linkPrefixTag]/[::tagInURL]/">[::tagValue]</a></span>]][[::aTags, hasTags, </h3>]]

</article>
