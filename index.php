<?php
/**
* 
* @link http://bw.bo-blog.com
* @copyright (c) 2014 bW Development Team
* @license MIT
*/

if (!defined ('P')) {
	define ('P', './');
} 
define ('FPATH', dirname (__FILE__));

include_once (P . 'inc/system.php');

$canonical = new bwCanonicalization;

include_once ($canonical -> loader ());

