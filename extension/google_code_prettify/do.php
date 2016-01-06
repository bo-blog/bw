<?php
if (!defined ('P')) {
	die ('Access Denied.');
} 
class ext_google_code_prettify {
	public static function init ()
	{
	} 

	public static function footer ()
	{
		return '<script>$("<link>").attr({rel:"stylesheet", type:"text/css", href: "[[::siteURL]]/extension/google_code_prettify/assets/prettify.css"}).appendTo("head");
$("<sc"+"ript>"+"</sc"+"ript>").attr({src: "[[::siteURL]]/extension/google_code_prettify/assets/prettify.js"}).appendTo("head");
prettyPrint();</script>';
	} 

	public static function textParser ($text)
	{
		return str_replace (array ('<pre><code class="', '</code>', '<pre><code>'), array ('<pre class="prettyprint ', '', '<pre class="prettyprint">'), $text);
	} 
} 
