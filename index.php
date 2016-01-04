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

if (isset ($_REQUEST['list'])) { //two hidden mode, for static page mode aka "Mill" project.	
	define ('M', 'list');
} 

$canonical = new bwCanonicalization;

include_once ($canonical -> loader ());

